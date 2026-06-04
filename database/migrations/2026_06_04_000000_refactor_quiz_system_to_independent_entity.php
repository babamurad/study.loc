<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Обработка старой таблицы lesson_quizzes, если она еще существует
        if (Schema::hasTable('lesson_quizzes')) {
            Schema::table('lesson_quizzes', function (Blueprint $table) {
                // Пытаемся удалить ключ (в MySQL игнорируем ошибку, если его нет, но Laravel так не умеет, 
                // поэтому просто предполагаем, что он есть, раз таблица существует в изначальном виде)
                $table->dropForeign(['lesson_id']);
            });
            Schema::rename('lesson_quizzes', 'quizzes');
        }

        // 2. Добавляем связь в lessons
        if (!Schema::hasColumn('lessons', 'quiz_id')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->foreignId('quiz_id')->nullable()->constrained('quizzes')->nullOnDelete();
            });
        }

        // 3. Мигрируем данные (связываем уроки с их тестами)
        // Если колонка lesson_id еще есть в quizzes, значит мы еще не закончили
        if (Schema::hasColumn('quizzes', 'lesson_id')) {
            DB::table('quizzes')->orderBy('id')->chunk(100, function ($quizzes) {
                foreach ($quizzes as $quiz) {
                    if ($quiz->lesson_id) {
                        DB::table('lessons')->where('id', $quiz->lesson_id)->update(['quiz_id' => $quiz->id]);
                    }
                }
            });

            // 4. Удаляем внешний ключ через сырой SQL, чтобы корректно перехватить ошибку
            try {
                DB::statement('ALTER TABLE `quizzes` DROP FOREIGN KEY `lesson_quizzes_lesson_id_foreign`');
            } catch (\Exception $e) {
                // Игнорируем, если ключа нет
            }
            try {
                DB::statement('ALTER TABLE `quizzes` DROP FOREIGN KEY `quizzes_lesson_id_foreign`');
            } catch (\Exception $e) {
                // Игнорируем, если ключа нет
            }

            Schema::table('quizzes', function (Blueprint $table) {
                $table->dropColumn('lesson_id');
            });
        }

        // 5. Обновляем quiz_questions
        if (Schema::hasColumn('quiz_questions', 'lesson_quiz_id')) {
            Schema::table('quiz_questions', function (Blueprint $table) {
                $table->dropForeign(['lesson_quiz_id']);
                $table->renameColumn('lesson_quiz_id', 'quiz_id');
            });
            Schema::table('quiz_questions', function (Blueprint $table) {
                $table->foreign('quiz_id')->references('id')->on('quizzes')->cascadeOnDelete();
            });
        }

        // 6. Обновляем user_quiz_attempts
        if (Schema::hasColumn('user_quiz_attempts', 'lesson_quiz_id')) {
            Schema::table('user_quiz_attempts', function (Blueprint $table) {
                $table->dropForeign(['lesson_quiz_id']);
                $table->renameColumn('lesson_quiz_id', 'quiz_id');
            });
            Schema::table('user_quiz_attempts', function (Blueprint $table) {
                $table->foreign('quiz_id')->references('id')->on('quizzes')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_quiz_attempts', function (Blueprint $table) {
            $table->dropForeign(['quiz_id']);
            $table->renameColumn('quiz_id', 'lesson_quiz_id');
        });

        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->dropForeign(['quiz_id']);
            $table->renameColumn('quiz_id', 'lesson_quiz_id');
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreignId('lesson_id')->nullable()->constrained('lessons')->cascadeOnDelete();
        });

        DB::table('lessons')->whereNotNull('quiz_id')->orderBy('id')->chunk(100, function ($lessons) {
            foreach ($lessons as $lesson) {
                DB::table('quizzes')->where('id', $lesson->quiz_id)->update(['lesson_id' => $lesson->id]);
            }
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropForeign(['quiz_id']);
            $table->dropColumn('quiz_id');
        });

        Schema::rename('quizzes', 'lesson_quizzes');

        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->foreign('lesson_quiz_id')->references('id')->on('lesson_quizzes')->cascadeOnDelete();
        });

        Schema::table('user_quiz_attempts', function (Blueprint $table) {
            $table->foreign('lesson_quiz_id')->references('id')->on('lesson_quizzes')->cascadeOnDelete();
        });
    }
};
