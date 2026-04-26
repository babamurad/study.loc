# Чеклист реализации MVP тестового раннера

## Выполнено ✓

### 1. Миграции БД
- [x] `lesson_practices` — практики уроков
- [x] `practice_test_cases` — тест-кейсы
- [x] `practice_submissions` — попытки учеников
- [x] `practice_test_results` — результаты тестов

### 2. Модели Eloquent
- [x] `LessonPractice` — с связями к тестам и submission'ам
- [x] `PracticeTestCase` — типы тестов (dom/css/behavior/console_errors)
- [x] `PracticeSubmission` — статусы, попытки, скоринг
- [x] `PracticeTestResult` — результат каждого теста
- [x] Обновлены связи в `Lesson`

### 3. Сервисы
- [x] `RunnerClient` — HTTP-клиент для API runner'а
- [x] `SubmissionScoringService` — расчёт балла (0-10)

### 4. Job (очередь)
- [x] `RunPracticeSubmissionJob` — асинхронная проверка
- [x] Retry policy (3 попытки, backoff)
- [x] Idempotency через submission_id
- [x] Обработка timeout/failed

### 5. API
- [x] `RunnerCallbackController` — callback от runner'а
- [x] HMAC-верификация подписи
- [x] Replay protection через timestamp

### 6. Конфигурация
- [x] `services.runner` — url, api_key, timeout, hmac_secret

### 7. Фабрики
- [x] `LessonPracticeFactory`
- [x] `PracticeTestCaseFactory`
- [x] `PracticeSubmissionFactory`

### 8. Тесты
- [x] `PracticeFlowTest` — покрытие scoring логики

---

## В процессе

### 9. Livewire компоненты
- [ ] `PracticeEditor` — редактор кода (готово, интеграция в progress)
- [ ] `Teacher\Practices\EditPractice` — админка тест-кейсов

### 10. Blade вьюхи
- [ ] `practice-editor.blade.php` — UI редактора (готово)
- [ ] `teacher/practices/edit-practice.blade.php` — UI для учителя (готово)
- [ ] Интеграция в `lesson-show.blade.php` (pending)

---

## Предстоящие задачи

### 11. Runner Service (отдельный микросервис)
- [ ] Node.js/Python API сервер
- [ ] Puppeteer для DOM/CSS проверок
- [ ] Sandbox в Docker
- [ ] Endpoint: `POST /api/v1/evaluate`
- [ ] Endpoint: `GET /api/v1/jobs/{id}`

### 12. Очередь
- [ ] Настроить Redis/database queue
- [ ] `php artisan queue:work practice-checks`
- [ ] Laravel Horizon (опционально)

### 13. UI интеграция
- [ ] Встроить PracticeEditor в lesson-show
- [ ] Добавить кнопку "Завершить урок" (проверка practice passed)
- [ ] Показать прогресс практик

### 14. Rate Limiting
- [ ] 1 запрос/10 сек на пользователя/урок
- [ ] Cooldown UI после лимита

### 15. Observability
- [ ] Логирование submission_id, duration, status
- [ ] Метрики p50/p95 latency
- [ ] Алерты на timeout/error rate

---

## Запуск

```bash
# Миграции
php artisan migrate

# Очередь
php artisan queue:work --queue=practice-checks

# Runner (отдельный сервис — требует реализации)
# cd runner && npm start
```

## Environment variables

```env
RUNNER_URL=http://localhost:8080
RUNNER_API_KEY=your-api-key
RUNNER_HMAC_SECRET=your-secret
RUNNER_TIMEOUT=5000
RUNNER_TOTAL_TIMEOUT=10000
```