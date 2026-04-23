<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $course = Course::create([
            'title' => 'Web-разработка: HTML & CSS (Deep Dive)',
            'slug' => 'web-development-html-css-deep-dive',
            'description' => 'Полный курс по верстке для начинающих. От основ HTML5 до современной адаптивной верстки на Flexbox.',
            'is_published' => true,
        ]);

        $weeks = [
            [
                'title' => 'Неделя 1: Фундамент и Текст',
                'lessons' => [
                    [
                        'title' => 'Урок 1.1: Инфраструктура',
                        'content' => '
                            <section>
                                <p class="lead">Добро пожаловать в мир веб-разработки! Узнаем, как устроен интернет и создадим свою первую страницу.</p>
                                <h3>Как работает интернет?</h3>
                                <p>Представь, что ты заказываешь пиццу:</p>
                                <ul>
                                    <li><b>Клиент (Браузер)</b>: Это ты. Ты вводишь адрес и нажимаешь "Enter" (делаешь заказ).</li>
                                    <li><b>Сервер</b>: Это кухня пиццерии. Он находит нужные файлы и "готовит" страницу.</li>
                                    <li><b>HTTP</b>: Это курьер, который доставляет готовую страницу тебе на стол.</li>
                                </ul>

                                <h3>Настройка VS Code</h3>
                                <p>Установи эти плагины для комфортной работы:</p>
                                <ol>
                                    <li><code>Live Server</code> — мгновенный просмотр изменений.</li>
                                    <li><code>Auto Rename Tag</code> — авто-правка парных тегов.</li>
                                </ol>

                                <h3>Скелет HTML-документа</h3>
                                <p>Базовый шаблон любого сайта (в VS Code набери <code>!</code> и нажми <code>Tab</code>):</p>
                                
                                <pre><span class="code-header">index.html</span><code><span class="hl-comm">&lt;!-- Сообщаем браузеру, что это современный HTML5 --&gt;</span>
<span class="hl-tag">&lt;!DOCTYPE html&gt;</span>
<span class="hl-tag">&lt;html</span> <span class="hl-attr">lang=</span><span class="hl-str">"ru"</span><span class="hl-tag">&gt;</span>
<span class="hl-tag">&lt;head&gt;</span>
    <span class="hl-tag">&lt;meta</span> <span class="hl-attr">charset=</span><span class="hl-str">"UTF-8"</span><span class="hl-tag">&gt;</span>
    <span class="hl-tag">&lt;title&gt;</span>Моя первая страница<span class="hl-tag">&lt;/title&gt;</span>
<span class="hl-tag">&lt;/head&gt;</span>
<span class="hl-tag">&lt;body&gt;</span>
    <span class="hl-comm">&lt;!-- Весь контент пишется здесь --&gt;</span>
    Привет, мир!
<span class="hl-tag">&lt;/body&gt;</span>
<span class="hl-tag">&lt;/html&gt;</span></code></pre>

                                <h3>Что это за теги?</h3>
                                <ul>
                                    <li><code>&lt;!DOCTYPE html&gt;</code> — <b>декларация</b>. Она говорит браузеру: "Эй, это современный стандарт HTML5!".</li>
                                    <li><code>&lt;html lang="ru"&gt;</code> — корень документа.</li>
                                    <li><code>&lt;head&gt;</code> — "голова" сайта. Здесь хранится техническая информация.</li>
                                    <li><code>&lt;meta charset="UTF-8"&gt;</code> — кодировка. <b>UTF-8</b> поддерживает почти все языки мира.</li>
                                    <li><code>&lt;title&gt;</code> — текст на вкладке браузера.</li>
                                    <li><code>&lt;body&gt;</code> — "тело" сайта. Здесь находится всё, что мы видим.</li>
                                </ul>
                            </section>
                        '
                    ],
                    [
                        'title' => 'Урок 1.2: Текстовая иерархия',
                        'content' => '
                            <section>
                                <p>HTML — это про <b>смысл</b>. Теги говорят браузеру, что является заголовком, а что — текстом.</p>

                                <h3>Заголовки (h1–h6)</h3>
                                <div class="alert alert-important">
                                    <span>⚠️</span>
                                    <div>
                                        <b>Важное правило:</b> На странице должен быть только один тег <code>&lt;h1&gt;</code>. Это главный заголовок.
                                    </div>
                                </div>

                                <pre><code><span class="hl-tag">&lt;h1&gt;</span>Главная тема страницы<span class="hl-tag">&lt;/h1&gt;</span>
<span class="hl-tag">&lt;h2&gt;</span>Раздел 1: Введение<span class="hl-tag">&lt;/h2&gt;</span>
<span class="hl-tag">&lt;h3&gt;</span>Подраздел: История вопроса<span class="hl-tag">&lt;/h3&gt;</span></code></pre>

                                <h3>Абзацы и Акценты</h3>
                                <p>Для обычного текста используем тег <code>&lt;p&gt;</code>.</p>
                                <pre><code><span class="hl-tag">&lt;p&gt;</span>Обычный текст. <span class="hl-tag">&lt;strong&gt;</span>Жирный<span class="hl-tag">&lt;/strong&gt;</span> и <span class="hl-tag">&lt;em&gt;</span>курсив<span class="hl-tag">&lt;/em&gt;</span>.<span class="hl-tag">&lt;/p&gt;</span></code></pre>
                            </section>
                        '
                    ],
                    [
                        'title' => 'Урок 1.3: Списки и спецсимволы',
                        'content' => '
                            <section>
                                <h3>Списки</h3>
                                <p><b>Маркированные (ul)</b> — порядок не важен. <b>Нумерованные (ol)</b> — когда важна последовательность.</p>
                                
                                <pre><code><span class="hl-tag">&lt;ul&gt;</span>
    <span class="hl-tag">&lt;li&gt;</span>Яблоки<span class="hl-tag">&lt;/li&gt;</span>
    <span class="hl-tag">&lt;li&gt;</span>Бананы<span class="hl-tag">&lt;/li&gt;</span>
<span class="hl-tag">&lt;/ul&gt;</span></code></pre>

                                <h3>Спецсимволы</h3>
                                <p>Чтобы вывести символы, которые HTML считает кодом, используем сущности:</p>
                                <ul>
                                    <li><code>&amp;lt;</code> — знак "меньше" (&lt;)</li>
                                    <li><code>&amp;gt;</code> — знак "больше" (&gt;)</li>
                                    <li><code>&amp;copy;</code> — знак копирайта (&copy;)</li>
                                </ul>

                                <div class="task-box">
                                    <h2>🚀 Практическое задание</h2>
                                    <p>Создай страницу-визитку о своем любимом герое или хобби:</p>
                                    <ul>
                                        <li>Используй правильную структуру HTML5.</li>
                                        <li>Добавь заголовок <code>h1</code> и подзаголовок <code>h2</code>.</li>
                                        <li>Напиши 2-3 абзаца текста с выделениями.</li>
                                        <li>Создай список из 5 интересных фактов.</li>
                                        <li>В конце добавь <code>&copy; Твое Имя</code>.</li>
                                    </ul>
                                </div>
                            </section>
                        '
                    ],
                ]
            ],
            [
                'title' => 'Неделя 2: Связи и Структура файлов',
                'lessons' => [
                    ['title' => 'Урок 2.1: Ссылки и атрибуты', 'content' => '<ul><li>Тег &lt;a&gt;, атрибут href. Внешние ссылки.</li><li>Атрибут target="_blank". Ссылка-почта и ссылка-телефон.</li></ul>'],
                    ['title' => 'Урок 2.2: Файловая система', 'content' => '<p><strong>Критическая тема. Абсолютные vs Относительные пути.</strong></p><ul><li>Как перемещаться по папкам: ./, ../.</li><li>Создание структуры проекта (папки img, pages, css).</li></ul>'],
                    ['title' => 'Урок 2.3: Якоря и Навигация', 'content' => '<ul><li>Атрибут id. Создание навигации внутри одной длинной страницы.</li><li>Построение простого меню.</li></ul>'],
                ]
            ],
            [
                'title' => 'Неделя 3: Контент и Таблицы',
                'lessons' => [
                    ['title' => 'Урок 3.1: Мультимедиа', 'content' => '<ul><li>Тег &lt;img&gt;. Атрибуты alt, title, width, height.</li><li>Понятие соотношения сторон. Кратко о &lt;video&gt; и &lt;iframe&gt;.</li></ul>'],
                    ['title' => 'Урок 3.2: Таблицы: База', 'content' => '<ul><li>Структура &lt;table&gt;, &lt;tr&gt;, &lt;th&gt;, &lt;td&gt;.</li><li>Зачем нужны таблицы сегодня (только для данных).</li></ul>'],
                    ['title' => 'Урок 3.3: Таблицы: Сложные структуры', 'content' => '<ul><li>Объединение ячеек: colspan и rowspan.</li><li>Группировка: &lt;thead&gt;, &lt;tbody&gt;, &lt;tfoot&gt;.</li></ul>'],
                ]
            ],
            [
                'title' => 'Неделя 4: Формы и Взаимодействие',
                'lessons' => [
                    ['title' => 'Урок 4.1: Основы форм', 'content' => '<ul><li>Тег &lt;form&gt;, атрибуты action и method.</li><li>Текстовые поля: &lt;input type="text/password/email"&gt;.</li><li>Связка &lt;label&gt; и id.</li></ul>'],
                    ['title' => 'Урок 4.2: Выбор и списки', 'content' => '<ul><li>Переключатели (radio), галочки (checkbox).</li><li>Выпадающие списки &lt;select&gt; и &lt;option&gt;. Поле &lt;textarea&gt;.</li></ul>'],
                    ['title' => 'Урок 4.3: Кнопки и Валидация', 'content' => '<ul><li>Типы кнопок: submit, reset, button.</li><li>Атрибуты: required, minlength, maxlength, placeholder, pattern.</li></ul>'],
                ]
            ],
            [
                'title' => 'Неделя 5: Семантика и Подготовка к дизайну',
                'lessons' => [
                    ['title' => 'Урок 5.1: Семантический HTML5', 'content' => '<ul><li>Теги &lt;header&gt;, &lt;nav&gt;, &lt;main&gt;, &lt;section&gt;, &lt;article&gt;, &lt;aside&gt;, &lt;footer&gt;.</li></ul>'],
                    ['title' => 'Урок 5.2: Блочная модель (Теория)', 'content' => '<ul><li>Различие между блочными (div, h1, p) и строчными (span, a, img) элементами.</li><li>Логика их расположения в потоке.</li></ul>'],
                    ['title' => 'Урок 5.3: Meta-теги и HEAD', 'content' => '<ul><li>Кодировка, Favicon, Open Graph.</li><li>Подготовка к подключению CSS.</li></ul>'],
                ]
            ],
            [
                'title' => 'Неделя 6: Стилизация и Селекторы',
                'lessons' => [
                    ['title' => 'Урок 6.1: Введение в CSS', 'content' => '<ul><li>Три способа подключения.</li><li>Синтаксис: Селектор { Свойство: Значение; }.</li></ul>'],
                    ['title' => 'Урок 6.2: Селекторы и Каскад', 'content' => '<ul><li>Селекторы по тегу, классу, ID. Комбинированные селекторы.</li><li>Приоритеты (Специфичность). !important.</li></ul>'],
                    ['title' => 'Урок 6.3: Цвет и Текст', 'content' => '<ul><li>Форматы цвета: HEX, RGB, RGBA.</li><li>Свойства: font-family, font-size, font-weight. Google Fonts.</li></ul>'],
                ]
            ],
            [
                'title' => 'Неделя 7: Box Model и Размеры',
                'lessons' => [
                    ['title' => 'Урок 7.1: Геометрия блока', 'content' => '<ul><li>padding, margin, border. Свойство box-sizing: border-box.</li><li>Схлопывание маржинов.</li></ul>'],
                    ['title' => 'Урок 7.2: Размеры и Единицы', 'content' => '<ul><li>Абсолютные (px) vs Относительные (%, em, rem, vh, vw).</li></ul>'],
                    ['title' => 'Урок 7.3: Фон и Границы', 'content' => '<ul><li>background-image, background-size, linear-gradient.</li><li>Скругление углов border-radius. Тени box-shadow.</li></ul>'],
                ]
            ],
            [
                'title' => 'Неделя 8: Позиционирование и Потоки',
                'lessons' => [
                    ['title' => 'Урок 8.1: Display', 'content' => '<ul><li>Свойства block, inline, inline-block, none.</li></ul>'],
                    ['title' => 'Урок 8.2: Position', 'content' => '<ul><li>static, relative, absolute, fixed, sticky.</li><li>Работа с z-index.</li></ul>'],
                    ['title' => 'Урок 8.3: Центрирование', 'content' => '<ul><li>Способы центрирования по горизонтали и вертикали.</li></ul>'],
                ]
            ],
            [
                'title' => 'Неделя 9: Flexbox (Современная верстка)',
                'lessons' => [
                    ['title' => 'Урок 9.1: Flex Container', 'content' => '<ul><li>display: flex. Главная и поперечная оси.</li><li>justify-content, align-items, flex-direction.</li></ul>'],
                    ['title' => 'Урок 9.2: Flex Items', 'content' => '<ul><li>flex-grow, flex-shrink, flex-basis. Свойство gap.</li></ul>'],
                    ['title' => 'Урок 9.3: Практика Flexbox', 'content' => '<ul><li>Верстка сложной шапки сайта и футера.</li></ul>'],
                ]
            ],
            [
                'title' => 'Неделя 10: Адаптивность и Финал',
                'lessons' => [
                    ['title' => 'Урок 10.1: Media Queries', 'content' => '<ul><li>Понятие Mobile First. Точки перелома (Breakpoints).</li></ul>'],
                    ['title' => 'Урок 10.2: Псевдоклассы', 'content' => '<ul><li>:hover, :active, :focus, :nth-child.</li></ul>'],
                    ['title' => 'Урок 10.3: Финальный зачет', 'content' => '<ul><li>Обзор инструментов разработчика (DevTools).</li><li>Подготовка к курсовой работе.</li></ul>'],
                ]
            ],
        ];

        $lessonPosition = 1;
        foreach ($weeks as $weekIndex => $weekData) {
            $module = Module::create([
                'course_id' => $course->id,
                'title' => $weekData['title'],
                'position' => $weekIndex + 1,
            ]);

            foreach ($weekData['lessons'] as $lessonData) {
                Lesson::create([
                    'course_id' => $course->id,
                    'module_id' => $module->id,
                    'title' => $lessonData['title'],
                    'slug' => Str::slug($lessonData['title']),
                    'content' => $lessonData['content'],
                    'position' => $lessonPosition++,
                    'is_published' => true,
                ]);
            }
        }
    }
}
