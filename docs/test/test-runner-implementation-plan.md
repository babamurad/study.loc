# Вариант B — Тестовый раннер: подробный план реализации

## 1) Цель и критерии успеха

### Цель
Построить стабильный и масштабируемый механизм проверки практических заданий через автотесты в изолированной среде (sandbox runner) с итоговой оценкой по шкале **0.0–10.0**.

### Критерии успеха (MVP)
- Проверка задания запускается по кнопке «Проверить» и возвращает результат < 5 секунд для простых кейсов.
- Поддерживаются минимум 3 типа проверок: DOM, CSS, JS-поведение.
- Для каждой попытки сохраняются: итоговый балл, статус pass/fail, детальные результаты по тестам.
- Урок можно завершить только при проходном балле практики.
- Есть базовая защита от злоупотреблений (таймауты, лимиты, rate limit).

---

## 2) Архитектурная схема

## Компоненты
1. **LMS Backend (Laravel + Livewire)**
   - хранит задания, чек-листы/тесты, попытки, результаты;
   - ставит проверку в очередь;
   - агрегирует балл и обновляет прогресс.

2. **Runner API (отдельный сервис)**
   - принимает payload с кодом ученика + спецификацией тестов;
   - запускает тесты в sandbox;
   - возвращает структурированный JSON-результат.

3. **Sandbox Runtime**
   - изолированный контейнер/процесс с жёсткими лимитами CPU/RAM/времени;
   - без доступа к приватной сети и файловой системе хоста.

4. **Queue Worker**
   - асинхронно обрабатывает проверки;
   - поддерживает ретраи, dead-letter и идемпотентность.

---

## 3) Модель данных (рекомендуемая)

## `lesson_practices`
- `id`
- `lesson_id` (FK)
- `title`
- `description`
- `max_score` (default `10`)
- `pass_score` (например `7`)
- `runner_profile` (например `frontend_html_css_js_v1`)
- `is_active`
- `published_at`

## `practice_test_cases`
- `id`
- `lesson_practice_id` (FK)
- `name`
- `type` (`dom`, `css`, `behavior`, `console_errors`, `snapshot`)
- `weight` (например `2.0`)
- `script` (JSON/DSL/код теста)
- `timeout_ms` (индивидуальный лимит)
- `sort_order`
- `is_required` (если `true`, провал == fail всей практики)
- `version`

## `practice_submissions`
- `id`
- `user_id` (FK)
- `lesson_practice_id` (FK)
- `html_code`, `css_code`, `js_code`
- `status` (`pending`, `running`, `completed`, `failed`, `timeout`)
- `score` (0.0–10.0)
- `passed` (bool)
- `attempt_no`
- `runner_job_id`
- `runner_version`
- `started_at`, `checked_at`
- `error_message`

## `practice_test_results`
- `id`
- `practice_submission_id` (FK)
- `practice_test_case_id` (FK)
- `passed`
- `earned_score`
- `duration_ms`
- `message`
- `meta` (JSON, например diff/screenshot hash)

---

## 4) API-контракты между Laravel и Runner

## 4.1 Запуск проверки
`POST /api/v1/evaluate`

### Request
```json
{
  "submission_id": 12345,
  "profile": "frontend_html_css_js_v1",
  "code": {
    "html": "<div class=\"card\"></div>",
    "css": ".card { width: 300px; }",
    "js": "console.log('ok')"
  },
  "tests": [
    {
      "id": 1,
      "name": "Card exists",
      "type": "dom",
      "weight": 2,
      "timeout_ms": 1000,
      "script": { "selector": ".card", "exists": true }
    }
  ],
  "limits": {
    "total_timeout_ms": 5000,
    "memory_mb": 128,
    "cpu_ms": 3000
  }
}
```

### Response
```json
{
  "submission_id": 12345,
  "runner_job_id": "job_abc",
  "status": "completed",
  "total_duration_ms": 1430,
  "tests": [
    {
      "id": 1,
      "passed": true,
      "earned_score": 2,
      "duration_ms": 120,
      "message": "Selector .card found"
    }
  ],
  "score": 8.0,
  "passed": true,
  "runner_version": "1.0.0"
}
```

## 4.2 Callback (опционально)
`POST /api/internal/practice-submissions/{id}/callback`
- Использовать HMAC подпись и timestamp для защиты.

---

## 5) Алгоритм расчёта балла

1. `max_weight = sum(test.weight)`
2. `earned_weight = sum(weight of passed tests)`
3. `score = round((earned_weight / max_weight) * 10, 1)`
4. Если есть `required` тест и он провален → `passed = false` независимо от score.
5. Иначе `passed = score >= pass_score`.

---

## 6) Очереди и отказоустойчивость

- Очередь: `practice-checks`.
- Job: `RunPracticeSubmissionJob(submissionId)`.
- Ретраи: 2–3 попытки только для инфраструктурных ошибок (5xx runner).
- Не ретраить логические ошибки теста/кода ученика.
- `idempotency_key` = `submission_id`.
- Таймаут job должен быть больше runner timeout (например 10с vs 5с).
- При превышении таймаута: статус `timeout`, понятное сообщение для пользователя.

---

## 7) Безопасность

- Запуск тестов только в изоляции (container/jail/firecracker).
- Отключить исходящий доступ к сети по умолчанию.
- Ограничить системные вызовы (seccomp/apparmor), mount read-only.
- Очистка окружения после каждого запуска.
- Rate limit на API «Проверить» (например 1 запрос/10 сек на пользователя/урок).
- Лимит максимального размера кода (например 200 KB суммарно).
- Санитизация логов, исключить утечки секретов.

---

## 8) UX-поток

1. Ученик вводит код в песочнице.
2. Нажимает «Проверить».
3. Видит состояния: `Проверка...` → `Готово`.
4. Получает:
   - итоговую оценку `/10`;
   - список тестов (OK/FAIL + короткая причина);
   - номер попытки.
5. Можно повторить попытку после cooldown.

---

## 9) План внедрения по спринтам

## Спринт 1 (MVP)
- Миграции и модели для `practice_test_cases` и `practice_test_results`.
- Runner API с 3 типами тестов: DOM/CSS/behavior.
- Очередь + базовый job + сохранение результатов.
- UI карточка результатов в уроке.

## Спринт 2
- Required tests, версия тестов, rate limit, retry policy.
- Админка/CRUD для тест-кейсов преподавателя.
- Улучшенные сообщения ошибок.

## Спринт 3
- Snapshot checks, более быстрый runtime.
- Аналитика: средний балл, частота ошибок по тестам.
- A/B оптимизация подсказок и UX.

---

## 10) Observability и метрики

## Логи
- `submission_id`, `runner_job_id`, `user_id`, `lesson_practice_id`, duration, status.

## Метрики
- p50/p95 времени проверки;
- error rate runner;
- timeout rate;
- average attempts to pass;
- pass rate per test case.

## Алерты
- timeout rate > 10% за 10 минут;
- 5xx runner > 3% за 5 минут;
- очередь > N задач более X минут.

---

## 11) QA-стратегия

- Unit: расчёт score, required-tests логика.
- Feature: end-to-end submit → queue → result persist.
- Contract tests: Laravel ↔ Runner JSON schema.
- Security tests: попытки сетевого доступа, бесконечные циклы, memory abuse.
- Regression набор эталонных решений и анти-решений.

---

## 12) Риски и способы снижения

- **Риск:** runner нестабилен под нагрузкой.  
  **Снижение:** autoscaling, очередь, backpressure.
- **Риск:** ложные FAIL из-за хрупких тестов.  
  **Снижение:** review тест-кейсов, tolerant assertions.
- **Риск:** злоупотребление частыми попытками.  
  **Снижение:** cooldown, rate limits, per-user quotas.

---

## 13) Definition of Done (для фичи)

- Пользователь может пройти практику через тест-раннер и получить балл `/10`.
- Результаты корректно сохраняются и отображаются в уроке.
- Проходной балл влияет на завершение урока.
- Основные ошибки (timeout, runner down, invalid payload) обрабатываются и видны пользователю.
- Покрытие ключевых сценариев автотестами + контрактными тестами.
