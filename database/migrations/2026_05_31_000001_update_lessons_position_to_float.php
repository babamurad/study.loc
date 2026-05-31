<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropUnique('lessons_course_id_position_unique');
        });

        if (DB::getDriverName() === 'sqlite') {
            Schema::table('lessons', function (Blueprint $table) {
                $table->float('position', 15, 8)->change();
            });
        } else {
            DB::statement('ALTER TABLE lessons MODIFY position DOUBLE(15,8) NOT NULL');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('lessons', function (Blueprint $table) {
                $table->unsignedInteger('position')->change();
            });
        } else {
            DB::statement('ALTER TABLE lessons MODIFY position INT UNSIGNED NOT NULL');
        }

        Schema::table('lessons', function (Blueprint $table) {
            $table->unique(['course_id', 'position']);
        });
    }
};
