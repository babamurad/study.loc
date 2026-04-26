# Документация системы тестового раннера

## Оглавление

1. [Обзор системы](#1-обзор-системы)
2. [Архитектура](#2-архитектура)
3. [База данных](#3-база-данных)
4. [Модели Eloquent](#4-модели-eloquent)
5. [Сервисы](#5-сервисы)
6. [Очередь и Jobs](#6-очередь-и-jobs)
7. [API контракты](#7-api-контракты)
8. [Компоненты UI](#8-компоненты-ui)
9. [Настройка и запуск](#9-настройка-и-запуск)
10. [Использование](#10-использование)

---

## 1. Обзор системы

Система тестового раннера позволяет автоматически проверять практические задания учащихся по фронтенду (HTML/CSS/JS) и выставлять оценки по 10-балльной шкале.

### Основные возможности

- ✅ Автоматическая проверка кода в изолированной среде
- ✅ Оценка по шкале 0.0–10.0
- ✅ Поддержка 5 типов тестов: DOM, CSS, Behavior, Console Errors, Snapshot
- ✅ Асинхронная проверка через очередь
- ✅ Детальные результаты по каждому тесту
- ✅ Rate limiting (защита от злоупотреблений)
- ✅ Retry policy при ошибках runner'а
- ✅ Интеграция с существующей системой уроков

### Формула расчёта балла

```
score = round((earned_weight / max_weight) * 10, 1)

Где:
- earned_weight = сумма весов пройденных тестов
- max_weight = сумма весов всех тестов
- passed = true, если score >= pass_score (по умолчанию 7.0)
- Если есть проваленный required-тест → passed = false
```

---

## 2. Архитектура

### Компонентная схема

```
┌─────────────────────────────────────────────────────────────────────┐
│                     Student Browser                              │
│                                                              │
│  ┌─────────────────┐    ┌──────────────────────────────────┐   │
│  │ LessonShow      │───▶│ PracticeEditor (Livewire)        │   │
│  │ Component      │    │ - Code Editor (HTML/CSS/JS)     │   │
│  │                │    │ - Submit Button                  │   │
│  │                │    │ - Results Display               │   │
│  └─────────────────┘    └──────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────┘
                              │
                              │ wire:submit
                              ▼
┌─────────────────────────────────────────────────────────────────────┐
│                     Laravel Application                         │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐     │
│  │  RunPracticeSubmissionJob                              │     │
│  │  - Idempotency (submission_id)                       │     │
│  │  - Retry: 3 attempts with backoff                   │     │
│  │  - Timeout: 30 seconds                              │     │
│  └──────────────────────────────────────────────────────┘     │
│                              │                                  │
│                              ▼                                  │
│  ┌──────────────────────────────────────────────────────┐     │
│  │  Queue: practice-checks                               │     │
│  │  (Redis или Database)                                │     │
│  └──────────────────────────────────────────────────────┘     │
│                              │                                  │
│                              ▼                                  │
│  ┌──────────────────────────────────────────────────────┐     │
│  │  RunnerClient (HTTP)                                  │     │
│  │  POST /api/v1/evaluate                               │     │
│  └──────────────────────────────────────────────────────┘     │
└─────────────────────────────────────────────────────────────────────┘
                              │
                              │ HTTP Request
                              ▼
┌─────────────────────────────────────────────────────────────────────┐
│                  Runner Service (отдельный)                       │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐     │
│  │  API Server (Node.js/FastAPI)                        │     │
│  │  - POST /api/v1/evaluate                            │     │
│  │  - GET /api/v1/jobs/{id}                            │     │
│  └──────────────────────────────────────────────────────┘     │
│                              │                                  │
│                              ▼                                  │
│  ┌──────────────────────────────────────────────────────┐     │
│  │  Sandbox (Puppeteer in Docker)                       │     │
│  │  - No network access                                │     │
│  │  - Resource limits (CPU/RAM)                     │     │
│  │  - Timeout enforcement                           │     │
│  └─────────────────────────���────────────────────────────┘     │
└─────────────────────────────────────────────────────────────────────┘
```

### Поток данных

```
1. Ученик вводит код в редакторе
            │
            ▼
2. Нажимает "Проверить задание"
            │
            ▼
3. Создаётся PracticeSubmission (status: pending)
            │
            ▼
4. Запускается RunPracticeSubmissionJob в очереди
            │
            ▼
5. Job отправляет код в Runner API
            │
            ▼
6. Runner выполняет тесты в sandbox
            │
            ▼
7. Runner возвращает результаты (async)
            │
            ▼
8. Callback обновляет результаты в БД
            │
            ▼
9. Ученик видит балл и детали
```

---

## 3. База данных

### Таблицы

#### 1. lesson_practices

Хранит практические задания для уроков.

```sql
CREATE TABLE lesson_practices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lesson_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    runner_profile VARCHAR(50) DEFAULT 'frontend_html_css_js_v1',
    max_score DECIMAL(3,1) DEFAULT 10.0,
    pass_score DECIMAL(3,1) DEFAULT 7.0,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    INDEX (lesson_id),
    INDEX (lesson_id, is_active)
);
```

**Поля:**
- `lesson_id` — ссылка на урок
- `title` — название практики
- `description` — описание для ученика
- `runner_profile` — профиль runner'а (для будущей совместимости)
- `max_score` — максимальный балл (по умолчанию 10.0)
- `pass_score` — проходной балл (по умолчанию 7.0)
- `is_active` — активна ли практика

---

#### 2. practice_test_cases

Хранит тест-кейсы для каждой практики.

```sql
CREATE TABLE practice_test_cases (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lesson_practice_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    type ENUM('dom', 'css', 'behavior', 'console_errors', 'snapshot') DEFAULT 'dom',
    weight DECIMAL(3,1) DEFAULT 2.0,
    script JSON NOT NULL,
    timeout_ms INT UNSIGNED DEFAULT 1000,
    sort_order INT UNSIGNED DEFAULT 0,
    is_required BOOLEAN DEFAULT FALSE,
    version VARCHAR(20) DEFAULT '1.0',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (lesson_practice_id) REFERENCES lesson_practices(id) ON DELETE CASCADE,
    INDEX (lesson_practice_id),
    INDEX (lesson_practice_id, version)
);
```

**Поля:**
- `name` — название теста (показывается ученику)
- `type` — тип проверки:
  - `dom` — проверка наличия элемента в DOM
  - `css` — проверка CSS свойств
  - `behavior` — проверка JS поведения
  - `console_errors` — проверка отсутствия ошибок в консоли
  - `snapshot` — скриншот-сравнение
- `weight` — вес теста (влияет на балл)
- `script` — JSON-скрипт проверки
- `timeout_ms` — таймаут для этого теста
- `is_required` — обязательный тест (если провален — вся практика не пройдена)
- `version` — версия теста (для пересчёта старых результатов)

---

#### 3. practice_submissions

Хранит попытки учеников.

```sql
CREATE TABLE practice_submissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    lesson_practice_id BIGINT UNSIGNED NOT NULL,
    html_code TEXT,
    css_code TEXT,
    js_code TEXT,
    status ENUM('pending', 'running', 'completed', 'failed', 'timeout') DEFAULT 'pending',
    score DECIMAL(3,1),
    passed BOOLEAN DEFAULT FALSE,
    attempt_no INT UNSIGNED DEFAULT 1,
    runner_job_id VARCHAR(100),
    runner_version VARCHAR(20),
    started_at TIMESTAMP,
    checked_at TIMESTAMP,
    error_message TEXT,
    raw_result JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_practice_id) REFERENCES lesson_practices(id) ON DELETE CASCADE,
    INDEX (user_id, lesson_practice_id),
    INDEX (lesson_practice_id, status),
    INDEX (runner_job_id),
    INDEX (created_at)
);
```

**Поля:**
- `html_code`, `css_code`, `js_code` — код ученика
- `status` — статус проверки:
  - `pending` — ожидает проверки
  - `running` — проверяется
  - `completed` — завершена
  - `failed` — ошибка runner'а
  - `timeout` — превышен таймаут
- `score` — итоговый балл (0.0–10.0)
- `passed` — пройдена ли практика
- `attempt_no` — номер попытки
- `runner_job_id` — ID job в runner'е
- `error_message` — сообщение об ошибке

---

#### 4. practice_test_results

Хранит результаты каждого теста в попытке.

```sql
CREATE TABLE practice_test_results (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    practice_submission_id BIGINT UNSIGNED NOT NULL,
    practice_test_case_id BIGINT UNSIGNED NOT NULL,
    passed BOOLEAN DEFAULT FALSE,
    earned_weight DECIMAL(3,1) DEFAULT 0.0,
    duration_ms INT UNSIGNED DEFAULT 0,
    message VARCHAR(500),
    meta JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (practice_submission_id) REFERENCES practice_submissions(id) ON DELETE CASCADE,
    FOREIGN KEY (practice_test_case_id) REFERENCES practice_test_cases(id) ON DELETE CASCADE,
    INDEX (practice_submission_id),
    INDEX (practice_test_case_id)
);
```

**Поля:**
- `passed` — пройден ли тест
- `earned_weight` — заработанный вес (если passed = true, иначе 0)
- `duration_ms` — время выполнения
- `message` — сообщение для ученика
- `meta` — дополнительные данные (diff, скриншот и т.д.)

---

## 4. Модели Eloquent

### LessonPractice

```php
// app/Models/LessonPractice.php

class LessonPractice extends Model
{
    protected $fillable = [
        'lesson_id',
        'title',
        'description',
        'runner_profile',
        'max_score',
        'pass_score',
        'is_active',
        'sort_order',
    ];

    // Связи
    public function lesson(): BelongsTo
    public function testCases(): HasMany
    public function submissions(): HasMany

    // Методы
    public function isPassedBy(User $user): bool
}
```

### PracticeTestCase

```php
// app/Models/PracticeTestCase.php

class PracticeTestCase extends Model
{
    // Типы тестов
    const TYPES = ['dom', 'css', 'behavior', 'console_errors', 'snapshot'];

    protected $fillable = [
        'lesson_practice_id',
        'name',
        'type',
        'weight',
        'script',
        'timeout_ms',
        'sort_order',
        'is_required',
        'version',
    ];

    protected function casts(): array
    {
        return [
            'script' => 'array',  // JSON -> array
        ];
    }
}
```

### PracticeSubmission

```php
// app/Models/PracticeSubmission.php

class PracticeSubmission extends Model
{
    // Статусы
    const STATUS_PENDING = 'pending';
    const STATUS_RUNNING = 'running';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_TIMEOUT = 'timeout';

    protected $table = 'practice_submissions';

    // Связи
    public function user(): BelongsTo
    public function lessonPractice(): BelongsTo
    public function testResults(): HasMany

    // Методы
    public function hasFailedRequiredTest(): bool

    // Scopes
    public function scopePassed($query)
    public function scopePending($query)

    // Статические методы
    public static function getNextAttemptNumber(int $userId, int $lessonPracticeId): int
}
```

### PracticeTestResult

```php
// app/Models/PracticeTestResult.php

class PracticeTestResult extends Model
{
    protected $table = 'practice_test_results';

    protected function casts(): array
    {
        return [
            'meta' => 'array',  // JSON -> array
        ];
    }

    public function submission(): BelongsTo
    public function testCase(): BelongsTo
}
```

---

## 5. Сервисы

### RunnerClient

HTTP-клиент для взаимодействия с Runner API.

```php
// app/Services/RunnerClient.php

class RunnerClient
{
    public function __construct();
    
    /**
     * Отправляет код на проверку
     * 
     * @param PracticeSubmission $submission
     * @return array Результат от runner'а
     * @throws RunnerException
     */
    public function evaluate(PracticeSubmission $submission): array;

    /**
     * Получает статус job'а
     * 
     * @param string $runnerJobId
     * @return array|null
     */
    public function getJobStatus(string $runnerJobId): ?array;
}

class RunnerException extends \Exception
{
    public array $details;
}
```

**Пример использования:**

```php
$runnerClient = app(RunnerClient::class);

try {
    $result = $runnerClient->evaluate($submission);
    // $result = [
    //     'runner_job_id' => 'job_abc123',
    //     'status' => 'completed',
    //     'total_duration_ms' => 1430,
    //     'tests' => [...],
    //     'score' => 8.0,
    //     'passed' => true,
    // ]
} catch (RunnerException $e) {
    Log::error('Runner error: ' . $e->getMessage());
}
```

### SubmissionScoringService

Сервис для расчёта балла.

```php
// app/Services/SubmissionScoringService.php

class SubmissionScoringService
{
    /**
     * Рассчитывает балл для submission
     * 
     * @param PracticeSubmission $submission
     * @return array [
     *     'score' => float,
     *     'passed' => bool,
     *     'max_weight' => float,
     *     'earned_weight' => float,
     *     'has_failed_required' => bool,
     * ]
     */
    public function calculate(PracticeSubmission $submission): array;

    /**
     * Обрабатывает результаты от runner'а
     * 
     * @param PracticeSubmission $submission
     * @param array $runnerResults
     * @return void
     */
    public function processResults(PracticeSubmission $submission, array $runnerResults): void;
}
```

**Алгоритм расчёта:**

```php
// 1. Получаем сумму весов всех тестов
$maxWeight = $testCases->sum('weight');

// 2. Получаем сумму ве��ов пройденных тестов
$earnedWeight = $results->where('passed', true)->sum('earned_weight');

// 3. Рассчитываем балл
$score = round(($earnedWeight / $maxWeight) * 10, 1);

// 4. Проверяем required-тесты
$hasFailedRequired = $results
    ->whereHas('testCase', fn($q) => $q->where('is_required', true))
    ->where('passed', false)
    ->isNotEmpty();

// 5. Определяем passed
$passed = !$hasFailedRequired && $score >= $practice->pass_score;
```

---

## 6. Очередь и Jobs

### RunPracticeSubmissionJob

Job для асинхронной проверки практики.

```php
// app/Jobs/RunPracticeSubmissionJob.php

class RunPracticeSubmissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Retry конфигурация
    public int $tries = 3;
    public int $backoff = [1, 2, 4];  // 1 сек, 2 сек, 4 сек
    public int $timeout = 30;

    public function __construct(
        public int $submissionId
    ) {}

    public function handle(RunnerClient $runnerClient, SubmissionScoringService $scoringService): void
    {
        // 1. Находим submission
        // 2. Проверяем статус
        // 3. Запускаем проверку
        // 4. Обрабатываем результаты
    }

    public function failed(\Throwable $exception): void
    {
        // Логируем ошибку
        // Обновляем статус на failed
    }
}
```

**Запуск job:**

```php
// Диспатч в очередь
RunPracticeSubmissionJob::dispatch($submissionId)
    ->onQueue('practice-checks');

// Или с задержкой
RunPracticeSubmissionJob::dispatch($submissionId)
    ->delay(now()->addSeconds(5))
    ->onQueue('practice-checks');
```

**Конфигурация очереди:**

```php
// config/queue.php
'queues' => [
    'default' => 'default',
    'practice-checks' => 'practice-checks',  // Высокий приоритет
],
```

---

## 7. API контракты

### Runner API → Laravel (Callback)

**Endpoint:** `POST /api/v1/callback/{submission_id}`

**Headers:**
```
Content-Type: application/json
X-Runner-Signature: sha256=...
X-Runner-Timestamp: 1700000000
```

**Request:**
```json
{
    "runner_job_id": "job_abc123",
    "status": "completed",
    "total_duration_ms": 1430,
    "tests": [
        {
            "id": 1,
            "passed": true,
            "earned_score": 2.0,
            "duration_ms": 120,
            "message": "Selector .card found",
            "meta": {}
        },
        {
            "id": 2,
            "passed": false,
            "earned_score": 0,
            "duration_ms": 45,
            "message": "Expected width: 300px, got: 200px",
            "meta": {}
        }
    ],
    "score": 8.0,
    "passed": true,
    "runner_version": "1.0.0"
}
```

**Response (200):**
```json
{
    "status": "ok"
}
```

**Response (401 - Invalid signature):**
```json
{
    "error": "Invalid signature"
}
```

### Laravel → Runner API

**Endpoint:** `POST /api/v1/evaluate`

**Request:**
```json
{
    "submission_id": 12345,
    "idempotency_key": "user-1-practice-1-attempt-2",
    "profile": "frontend_html_css_js_v1",
    "code": {
        "html": "<div class=\"card\"></div>",
        "css": ".card { width: 300px; }",
        "js": "document.querySelector('.card')"
    },
    "tests": [
        {
            "id": 1,
            "name": "Card exists",
            "type": "dom",
            "weight": 2.0,
            "timeout_ms": 1000,
            "is_required": true,
            "script": {
                "selector": ".card",
                "exists": true
            }
        },
        {
            "id": 2,
            "name": "Card width",
            "type": "css",
            "weight": 1.5,
            "timeout_ms": 500,
            "script": {
                "selector": ".card",
                "property": "width",
                "expected": "300px"
            }
        }
    ],
    "limits": {
        "total_timeout_ms": 5000,
        "memory_mb": 128,
        "cpu_ms": 3000
    }
}
```

**Response (202 Accepted):**
```json
{
    "submission_id": 12345,
    "runner_job_id": "job_abc123",
    "status": "running",
    "estimated_duration_ms": 3500
}
```

---

## 8. Компоненты UI

### PracticeEditor (Livewire)

Компонент для редактирования и проверки кода учеником.

```php
// app/Livewire/PracticeEditor.php

class PracticeEditor extends Component
{
    public LessonPractice $practice;
    public string $htmlCode = '';
    public string $cssCode = '';
    public string $jsCode = '';
    
    public ?PracticeSubmission $currentSubmission = null;
    public ?PracticeSubmission $bestSubmission = null;
    public bool $isRunning = false;
    public bool $showResults = false;
    public array $testResults = [];
    public int $attemptCount = 0;
    public string $activeTab = 'html';

    // Методы
    public function mount(): void;
    public function submit(): void;
    public function retake(): void;
    public function checkStatus(): void;
    public function setActiveTab(string $tab): void;
}
```

### Teacher\Practices\EditPractice (Livewire)

Компонент для редактирования практики учителем.

```php
// app/Livewire/Teacher/Practices/EditPractice.php

class EditPractice extends Component
{
    public ?LessonPractice $practice = null;
    public Lesson $lesson;
    
    public string $title = '';
    public string $description = '';
    public float $maxScore = 10.0;
    public float $passScore = 7.0;
    public bool $isActive = true;
    public array $testCases = [];

    // Методы
    public function mount(Lesson $lesson, ?LessonPractice $practice = null): void;
    public function addTestCase(): void;
    public function removeTestCase(string $id): void;
    public function save(): void;
}
```

---

## 9. Настройка и запуск

### Конфигурация .env

```env
# Runner API
RUNNER_URL=http://localhost:8080
RUNNER_API_KEY=your-api-key
RUNNER_HMAC_SECRET=change-me-in-production
RUNNER_TIMEOUT=5000
RUNNER_TOTAL_TIMEOUT=10000
```

### Конфигурация config/services.php

```php
// config/services.php

'runner' => [
    'url' => env('RUNNER_URL', 'http://localhost:8080'),
    'api_key' => env('RUNNER_API_KEY', 'test-key'),
    'timeout' => env('RUNNER_TIMEOUT', 5000),
    'total_timeout' => env('RUNNER_TOTAL_TIMEOUT', 10000),
    'hmac_secret' => env('RUNNER_HMAC_SECRET', 'change-me-in-production'),
],
```

### Запуск миграций

```bash
php artisan migrate
```

### Запуск очереди

```bash
# Обычный worker
php artisan queue:work --queue=practice-checks

# Или с Horizon
php artisan horizon
```

### Запуск тестов

```bash
# Все тесты
php artisan test

# Только практика
php artisan test tests/Feature/PracticeFlowTest.php
```

---

## 10. Использование

### Создание практики (для учителя)

1. Откройте редактирование урока
2. Перейдите на вкладку "Практика"
3. Заполните название и описание
4. Добавьте тест-кейсы

**Пример тест-кейса:**

```json
{
    "name": "Карточка существует",
    "type": "dom",
    "weight": 2.0,
    "script": {
        "selector": ".card",
        "exists": true
    },
    "is_required": true
}
```

```json
{
    "name": "Ширина карточки 300px",
    "type": "css",
    "weight": 1.5,
    "script": {
        "selector": ".card",
        "property": "width",
        "expected": "300px"
    }
}
```

```json
{
    "name": "Нет ошибок в консоли",
    "type": "console_errors",
    "weight": 1.0,
    "script": {}
}
```

### Прохождение практики (для ученика)

1. Откройте урок
2. Введите HTML/CSS/JS код в редакторе
3. Нажмите "Проверить задание"
4. Дождитесь результата
5. Если не пройдено — исправьте код и попробуйте снова

### Блокировка завершения урока

Урок можно завершить только если:
- ✅ Пройден quiz (если есть)
- ✅ Пройдена практика (passed = true)

---

## Структура файлов

```
app/
├── Jobs/
│   └── RunPracticeSubmissionJob.php
├── Livewire/
│   ├── PracticeEditor.php
│   └── Teacher/Practices/
│       └── EditPractice.php
├── Models/
│   ├── LessonPractice.php
│   ├── PracticeTestCase.php
│   ├── PracticeSubmission.php
│   └── PracticeTestResult.php
└── Services/
    ├── RunnerClient.php
    └── SubmissionScoringService.php

database/migrations/
├── 2026_04_26_000001_create_lesson_practices_table.php
├── 2026_04_26_000002_create_practice_test_cases_table.php
├── 2026_04_26_000003_create_practice_submissions_table.php
└── 2026_04_26_000004_create_practice_test_results_table.php

database/factories/
├── LessonPracticeFactory.php
├── PracticeTestCaseFactory.php
└── PracticeSubmissionFactory.php

resources/views/livewire/
├── practice-editor.blade.php
└── teacher/practices/
    └── edit-practice.blade.php

config/
└── services.php

tests/Feature/
└── PracticeFlowTest.php
```

---

## FAQ

### В: Что делать если runner недоступен?

О: Job будет повторён 3 раза с интервалами 1, 2, 4 секунды. После исчерпания попыток статус будет `failed`.

### В: Как изменить проходной балл?

О: Измените поле `pass_score` в таблице `lesson_practices` (по умолчанию 7.0).

### В: Как добавить новый тип теста?

О: Добавьте значение в enum `type` таблицы `practice_test_cases` и реализуйте проверку в runner'е.

### В: Можно ли использовать без runner'а?

О: Нет, для реальной работы нужен runner service. Для тестирования можно мокнуть `RunnerClient`.

---

## Связанные документы

- [План реализации](./test-runner-implementation-plan.md)
- [Профессиональные промты](./test-runner-professional-prompts.md)
- [Архитектурный дизайн](./runner-architecture-design.md)
- [Чеклист реализации](./checklist-implementation.md)