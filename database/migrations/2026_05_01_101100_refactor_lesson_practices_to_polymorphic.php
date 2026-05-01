<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Handle table rename and foreign key drop safely
        if (!Schema::hasTable('practices') && Schema::hasTable('lesson_practices')) {
            Schema::table('lesson_practices', function (Blueprint $table) {
                $table->dropForeign(['lesson_id']);
            });
            Schema::rename('lesson_practices', 'practices');
        } else if (Schema::hasTable('practices')) {
            // If the table was already renamed but the migration failed halfway,
            // the foreign key might still be there with the old name
            try {
                Schema::table('practices', function (Blueprint $table) {
                    $table->dropForeign('lesson_practices_lesson_id_foreign');
                });
            } catch (\Exception $e) {
                // Ignore
            }
        }

        // 2. Modify practices table
        if (!Schema::hasColumn('practices', 'practicable_type')) {
            Schema::table('practices', function (Blueprint $table) {
                // Add polymorphic relations and new fields
                $table->string('practicable_type')->nullable();
                $table->unsignedBigInteger('practicable_id')->nullable();
                
                $table->text('objective')->nullable()->after('title');
                $table->text('technical_task')->nullable()->after('objective');
                $table->text('checking_criteria')->nullable()->after('technical_task');
                $table->string('result_image_path')->nullable()->after('checking_criteria');
            });
        }

        // 3. Migrate Data
        if (Schema::hasColumn('practices', 'lesson_id')) {
            DB::table('practices')->update([
                'practicable_type' => 'App\Models\Lesson',
                'practicable_id' => DB::raw('lesson_id'),
            ]);
        }

        $practicesIndexes = collect(Schema::getIndexes('practices'))->pluck('name')->toArray();

        // 4. Remove old column and add index
        Schema::table('practices', function (Blueprint $table) use ($practicesIndexes) {
            // After data is migrated, drop lesson_id if it exists
            if (Schema::hasColumn('practices', 'lesson_id')) {
                // Drop indexes using explicit old names since table was renamed
                $table->dropIndex('lesson_practices_lesson_id_index');
                $table->dropIndex('lesson_practices_lesson_id_is_active_index');
                $table->dropColumn('lesson_id');
            }
            
            // Add polymorphic index
            if (!in_array('practices_practicable_type_practicable_id_index', $practicesIndexes)) {
                $table->index(['practicable_type', 'practicable_id']);
            }
            if (!in_array('practicable_is_active_index', $practicesIndexes)) {
                $table->index(['practicable_type', 'practicable_id', 'is_active'], 'practicable_is_active_index');
            }
        });

        $ptcForeignKeys = collect(Schema::getForeignKeys('practice_test_cases'))->pluck('name')->toArray();
        $ptcIndexes = collect(Schema::getIndexes('practice_test_cases'))->pluck('name')->toArray();

        // 5. Update practice_test_cases
        Schema::table('practice_test_cases', function (Blueprint $table) use ($ptcForeignKeys, $ptcIndexes) {
            if (in_array('practice_test_cases_lesson_practice_id_foreign', $ptcForeignKeys)) {
                $table->dropForeign('practice_test_cases_lesson_practice_id_foreign');
            }
            if (in_array('practice_test_cases_lesson_practice_id_index', $ptcIndexes)) {
                $table->dropIndex('practice_test_cases_lesson_practice_id_index');
            }
            if (in_array('practice_test_cases_lesson_practice_id_version_index', $ptcIndexes)) {
                $table->dropIndex('practice_test_cases_lesson_practice_id_version_index');
            }
            
            if (Schema::hasColumn('practice_test_cases', 'lesson_practice_id')) {
                $table->renameColumn('lesson_practice_id', 'practice_id');
            }
            
            if (!in_array('practice_test_cases_practice_id_foreign', $ptcForeignKeys)) {
                $table->foreign('practice_id')->references('id')->on('practices')->onDelete('cascade');
            }
            if (!in_array('practice_test_cases_practice_id_index', $ptcIndexes)) {
                $table->index('practice_id');
            }
            if (!in_array('practice_test_cases_practice_id_version_index', $ptcIndexes)) {
                $table->index(['practice_id', 'version']);
            }
        });

        $psForeignKeys = collect(Schema::getForeignKeys('practice_submissions'))->pluck('name')->toArray();
        $psIndexes = collect(Schema::getIndexes('practice_submissions'))->pluck('name')->toArray();

        // 6. Update practice_submissions
        // Step A: Drop old foreign key
        Schema::table('practice_submissions', function (Blueprint $table) use ($psForeignKeys) {
            if (in_array('practice_submissions_lesson_practice_id_foreign', $psForeignKeys)) {
                $table->dropForeign('practice_submissions_lesson_practice_id_foreign');
            }
        });

        // Step B: Rename column
        if (Schema::hasColumn('practice_submissions', 'lesson_practice_id')) {
            Schema::table('practice_submissions', function (Blueprint $table) {
                $table->renameColumn('lesson_practice_id', 'practice_id');
            });
        }

        // Step C: Add new foreign key and indexes FIRST (so user_id foreign key constraint doesn't break)
        Schema::table('practice_submissions', function (Blueprint $table) use ($psForeignKeys, $psIndexes) {
            if (!in_array('practice_submissions_practice_id_foreign', $psForeignKeys)) {
                $table->foreign('practice_id')->references('id')->on('practices')->onDelete('cascade');
            }
            if (!in_array('practice_submissions_user_id_practice_id_index', $psIndexes)) {
                $table->index(['user_id', 'practice_id']);
            }
            if (!in_array('practice_submissions_practice_id_status_index', $psIndexes)) {
                $table->index(['practice_id', 'status']);
            }
        });

        // Step D: Now that new indexes exist, safely drop the old indexes
        Schema::table('practice_submissions', function (Blueprint $table) use ($psIndexes) {
            if (in_array('practice_submissions_user_id_lesson_practice_id_index', $psIndexes)) {
                $table->dropIndex('practice_submissions_user_id_lesson_practice_id_index');
            }
            if (in_array('practice_submissions_lesson_practice_id_status_index', $psIndexes)) {
                $table->dropIndex('practice_submissions_lesson_practice_id_status_index');
            }
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Revert practice_submissions
        Schema::table('practice_submissions', function (Blueprint $table) {
            $table->dropForeign(['practice_id']);
            $table->dropIndex(['user_id', 'practice_id']);
            $table->dropIndex(['practice_id', 'status']);
            
            $table->renameColumn('practice_id', 'lesson_practice_id');
            
            $table->foreign('lesson_practice_id')->references('id')->on('lesson_practices')->onDelete('cascade');
            $table->index(['user_id', 'lesson_practice_id']);
            $table->index(['lesson_practice_id', 'status']);
        });

        // 2. Revert practice_test_cases
        Schema::table('practice_test_cases', function (Blueprint $table) {
            $table->dropForeign(['practice_id']);
            $table->dropIndex(['practice_id']);
            $table->dropIndex(['practice_id', 'version']);
            
            $table->renameColumn('practice_id', 'lesson_practice_id');
            
            $table->foreign('lesson_practice_id')->references('id')->on('lesson_practices')->onDelete('cascade');
            $table->index('lesson_practice_id');
            $table->index(['lesson_practice_id', 'version']);
        });

        // 3. Revert practices
        Schema::table('practices', function (Blueprint $table) {
            $table->dropIndex(['practicable_type', 'practicable_id']);
            $table->dropIndex('practicable_is_active_index');
            
            $table->unsignedBigInteger('lesson_id')->nullable();
        });

        // Data revert (only for those that were attached to Lesson)
        DB::table('practices')
            ->where('practicable_type', 'App\Models\Lesson')
            ->update(['lesson_id' => DB::raw('practicable_id')]);

        Schema::table('practices', function (Blueprint $table) {
            $table->dropColumn(['practicable_type', 'practicable_id', 'objective', 'technical_task', 'checking_criteria', 'result_image_path']);
            
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            $table->index('lesson_id');
            $table->index(['lesson_id', 'is_active']);
        });

        // 4. Rename back
        Schema::rename('practices', 'lesson_practices');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
