<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::create([
            'title' => 'Основы Web-разработки',
            'slug' => 'web-development-basics',
            'description' => 'Полный курс по HTML, CSS и основам JavaScript для начинающих разработчиков.',
            'is_published' => true,
        ]);

        $module = Module::create([
            'course_id' => $course->id,
            'title' => 'Неделя 1: Фундамент и Текст',
            'position' => 1,
        ]);

        $lessons = [
            ['title' => 'Как работает браузер', 'slug' => 'how-browser-works', 'content' => '<h2>Принцип работы браузера</h2><p>Браузер — это программа, которая запрашивает HTML-страницу с сервера и отображает её.</p><p>Ключевые этапы:</p><ol><li>Запрос страницы</li><li>Получение HTML</li><li>Парсинг DOM</li><li>Отрисовка</li></ol>', 'position' => 1],
            ['title' => 'Структура HTML-документа', 'slug' => 'html-document-structure', 'content' => '<h2>Базовая структура</h2><pre><code>&lt;!DOCTYPE html&gt;\n&lt;html&gt;\n  &lt;head&gt;...&lt;/head&gt;\n  &lt;body&gt;...&lt;/body&gt;\n&lt;/html&gt;</code></pre>', 'position' => 2],
            ['title' => 'Заголовки и параграфы', 'slug' => 'headings-and-paragraphs', 'content' => '<h2>Иерархия заголовков</h2><p>Теги h1-h6 создают уровни важности текста.</p><h3>Практика</h3><p>Создайте страницу о себе, используя заголовки h1 и h2.</p>', 'position' => 3],
            ['title' => 'Списки и форматирование', 'slug' => 'lists-and-formatting', 'content' => '<h2>Виды списков</h2><ul><li>ul — маркированный</li><li>ol — нумерованный</li><li>dl — список определений</li></ul>', 'position' => 4],
            ['title' => 'Первый проект', 'slug' => 'first-project', 'content' => '<h2>Создаём сайт-резюме</h2><p>Применим все полученные знания для создания личной страницы.</p><p>К концу урока у вас будет готовое резюме!</p>', 'position' => 5],
        ];

        foreach ($lessons as $lesson) {
            Lesson::create(array_merge($lesson, [
                'course_id' => $course->id,
                'module_id' => $module->id,
                'is_published' => true,
            ]));
        }
    }
}