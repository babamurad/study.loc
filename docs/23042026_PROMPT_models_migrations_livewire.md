# Промт для ИИ: создать модели, миграции и связи (Laravel + Livewire)

```text
Ты senior Laravel developer. Помоги мне добавить в существующий проект (Laravel + Livewire) модели, миграции и связи для учебной платформы с поэтапным открытием уроков.

## Контекст проекта (важно)
- Проект уже создан, Laravel установлен.
- Livewire уже установлен и используется.
- Есть стандартная таблица users и модель User.
- База данных проекта: studydb.
- Нужна логика: урок N открывается только после завершения урока N-1.

## Что нужно сгенерировать
Нужны сущности:
1) Course
2) Module
3) Lesson
4) UserLessonProgress

Нужны миграции и модели со связями.

## Требования к структуре таблиц
### courses
- id
- title (string)
- slug (string, unique)
- description (text, nullable)
- is_published (boolean, default true)
- timestamps

### modules
- id
- course_id (foreignId -> courses.id, cascadeOnDelete)
- title (string)
- position (unsignedInteger)
- timestamps
- unique(course_id, position)

### lessons
- id
- course_id (foreignId -> courses.id, cascadeOnDelete)
- module_id (foreignId -> modules.id, nullable, nullOnDelete)
- title (string)
- slug (string)
- content (longText, nullable)
- position (unsignedInteger)
- is_published (boolean, default true)
- timestamps
- unique(course_id, position)
- unique(course_id, slug)

### user_lesson_progress
- id
- user_id (foreignId -> users.id, cascadeOnDelete)
- lesson_id (foreignId -> lessons.id, cascadeOnDelete)
- status (string, default 'completed')
- completed_at (timestamp nullable)
- timestamps
- unique(user_id, lesson_id)
- index(user_id, status)

## Требования к моделям и связям
### User
- hasMany(UserLessonProgress::class)
- belongsToMany(Lesson::class, 'user_lesson_progress') (опционально)

### Course
- hasMany(Module::class)
- hasMany(Lesson::class)

### Module
- belongsTo(Course::class)
- hasMany(Lesson::class)

### Lesson
- belongsTo(Course::class)
- belongsTo(Module::class)
- hasMany(UserLessonProgress::class)
- метод isCompletedBy(User $user): bool

### UserLessonProgress
- belongsTo(User::class)
- belongsTo(Lesson::class)

## Что хочу получить в ответе
1) Точные artisan-команды для создания моделей и миграций.
2) Полные файлы миграций (готовый PHP-код).
3) Полные файлы моделей (готовый PHP-код) со связями и fillable/casts.
4) Какие строки добавить в app/Models/User.php.
5) Команды для запуска:
   - php artisan migrate
   - php artisan optimize:clear
6) Короткий чек-лист проверки после миграций.

## Важные ограничения
- Не используй абстрактные объяснения, дай код.
- Код должен быть совместим с Laravel 11.
- Имена классов/таблиц/полей должны быть консистентны.
- Не трогай Livewire-компоненты в этом шаге (только модели/миграции/связи).

Ответ дай в Markdown со структурой:
- Команды
- Миграции
- Модели
- Изменения в User
- Команды запуска
- Проверка
```
