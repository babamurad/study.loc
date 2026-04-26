# Профессиональные промты для реализации Варианта B (Тестовый раннер)

Ниже — готовые промты для работы с AI-командой (архитектор, backend, devops, QA, product).  
Каждый промт рассчитан на получение **практического deliverable**, а не абстрактного текста.

---

## 1) Архитектор: дизайн системы

**Промт:**
> Ты — Staff Engineer по платформенной архитектуре. Спроектируй production-ready решение для проверки фронтенд-практик через отдельный тест-раннер в Laravel LMS.
> 
> Контекст:
> - Оценка строго по шкале 0.0–10.0.
> - Есть lesson practices, test cases, submissions, test results.
> - Проверки идут асинхронно через очередь.
> 
> Что нужно в ответе:
> 1. C4-level архитектурная схема (контейнеры, ответственность, границы).
> 2. API-контракт Laravel ↔ Runner (request/response, ошибки, идемпотентность).
> 3. Выбор isolation подхода (docker/jail/firecracker) с trade-offs.
> 4. Non-functional requirements (SLO, latency budget, scalability).
> 5. Failure modes и стратегии graceful degradation.
> 6. Пошаговый migration path без downtime.
> 
> Формат: markdown, таблицы решений + список архитектурных ADR.

---

## 2) Backend (Laravel): реализация домена

**Промт:**
> Ты — Senior Laravel Engineer. Реализуй backend-часть тестового раннера для практик в существующем LMS проекте.
> 
> Требования:
> - Шкала 10-балльная.
> - Асинхронный запуск проверки через Queue Job.
> - Сохранение детальных результатов по каждому тесту.
> - Lesson completion gated by passed practice.
> 
> Нужен output:
> 1. Список миграций (таблицы, индексы, FK, enum/status).
> 2. Eloquent модели и связи.
> 3. Сервисы: `SubmissionScoringService`, `RunnerClient`, `PracticeEvaluationService`.
> 4. Job: `RunPracticeSubmissionJob` с retry policy и idempotency.
> 5. Контроллеры/экшены API и валидация FormRequest.
> 6. Примеры Pest тестов (unit + feature + contract mock).
> 7. Чеклист edge cases (timeout, runner 5xx, malformed response).
> 
> Пиши конкретный код-скелет на Laravel 12 и объясняй только сложные места.

---

## 3) Runner Engineer: движок проверок

**Промт:**
> Ты — Engineer, создающий изолированный runner для HTML/CSS/JS практик.
> 
> Задача:
> Реализовать сервис, который принимает код и тест-кейсы, исполняет их в sandbox и возвращает детальный JSON результат.
> 
> Условия:
> - Виды тестов: dom, css, behavior, console_errors.
> - Time limit: общий и per-test.
> - Никакого внешнего network доступа.
> - Предсказуемый output schema.
> 
> Что выдать:
> 1. Спецификация JSON schema input/output.
> 2. Псевдокод execution pipeline.
> 3. Модель ошибок (typed errors) и коды статусов.
> 4. Стратегия sandbox hardening.
> 5. Набор smoke тестов самого раннера.
> 
> Дай результат как technical spec v1.0.

---

## 4) DevOps/SRE: эксплуатация

**Промт:**
> Ты — SRE/DevOps lead. Подготовь production план деплоя тестового раннера и интеграции с Laravel.
> 
> Нужно:
> 1. Reference deployment architecture (k8s или docker swarm) с autoscaling.
> 2. Resource limits и QoS policy для runner pods.
> 3. Monitoring stack (metrics/logs/traces) + алерты.
> 4. Incident runbook для timeout spike / runner 5xx / queue backlog.
> 5. Security baseline (network policies, seccomp, secrets, image scanning).
> 6. Release strategy: canary + rollback.
> 
> Формат: ops playbook + actionable checklist.

---

## 5) QA Lead: тестовая стратегия

**Промт:**
> Ты — QA Lead. Составь полноформатную стратегию тестирования для практического тест-раннера.
> 
> Контекст:
> - Оценка 0–10.
> - Нужно исключить ложные PASS/FAIL.
> - Есть очереди, асинхронность и внешнее API runner.
> 
> В ответе нужны:
> 1. Test pyramid по уровням (unit/feature/e2e/contract/security/perf).
> 2. Критические тест-кейсы и негативные сценарии.
> 3. Regression suite и golden datasets.
> 4. Acceptance criteria по каждому бизнес-требованию.
> 5. Strategy для flakiness и deterministic test runs.
> 
> Сформируй output как QA test plan document.

---

## 6) Product Manager: фаза внедрения

**Промт:**
> Ты — Product Manager EdTech платформы. Подготовь план внедрения тест-раннера для практик с фокусом на learning outcomes.
> 
> Дано:
> - Шкала оценки: 10-балльная.
> - Есть MVP и дальнейшие спринты.
> 
> Нужно:
> 1. Разбить roadmap на MVP / Beta / GA.
> 2. Сформулировать KPI (pass rate, attempts to pass, lesson completion uplift).
> 3. Прописать UX-copy для состояний проверки и ошибок.
> 4. Сформировать приоритизацию backlog (RICE/WSJF).
> 5. Риски запуска и mitigation plan.
> 
> Формат: PRD-lite + release plan.

---

## 7) Prompt для генерации миграций и моделей (практический)

**Промт:**
> Сгенерируй для Laravel 12:
> - миграции `practice_test_cases` и `practice_test_results`,
> - модели с отношениями,
> - индексы для запросов по `submission_id`, `lesson_practice_id`, `user_id`,
> - enum/status константы,
> - фабрики для тестов.
> 
> Обязательные условия:
> - score в диапазоне 0.0–10.0;
> - детальные результаты каждого теста;
> - всё совместимо с Pest.
> 
> Дай готовые файлы к вставке, без псевдокода.

---

## 8) Prompt для API-контракта (ready-to-implement)

**Промт:**
> Подготовь финальный OpenAPI 3.1 контракт для Runner API:
> - `POST /api/v1/evaluate`
> - `GET /api/v1/jobs/{id}`
> - error schema
> - webhook callback schema
> 
> Добавь:
> - примеры payload;
> - поля для идемпотентности;
> - подпись HMAC и replay protection;
> - versioning policy.
> 
> Выведи только корректный YAML OpenAPI.

---

## 9) Prompt для threat modeling

**Промт:**
> Проведи threat modeling (STRIDE) для системы тест-раннера практик.
> 
> Оцени угрозы:
> - sandbox escape,
> - remote code execution abuse,
> - privilege escalation,
> - data exfiltration,
> - queue poisoning,
> - replay атак callback.
> 
> На выходе:
> 1. Таблица threat -> impact -> likelihood -> mitigation.
> 2. Security backlog с приоритетом P0/P1/P2.
> 3. Минимальный security baseline для GA.

---

## 10) Prompt для ретроспективы качества тестов

**Промт:**
> Проанализируй набор test cases практики и найди:
> - дубли,
> - хрупкие проверки,
> - проверки, дающие ложные FAIL,
> - проверки без учебной ценности.
> 
> Предложи улучшения:
> - более устойчивые assertions,
> - прозрачные сообщения для ученика,
> - перераспределение весов для честной оценки по 10-балльной шкале.
> 
> Верни результат как ревью-отчёт с конкретными правками.

---

## Рекомендации по использованию

- Для архитектуры и API сначала использовать промты №1 и №8.
- Для реализации backend — №2 и №7.
- Для запуска в прод — №4 и №9.
- Для стабилизации качества — №5 и №10.
- Для бизнес-синхронизации с командой — №6.
