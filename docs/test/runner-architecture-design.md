# Архитектура тестового раннера — ADR и дизайн

## ADR-001: Асинхронная проверка практик через выделенный runner

**Статус**: Proposed  
**Контекст**: LMS должна проверять фронтенд-практики (HTML/CSS/JS) с оценкой 0.0–10.0 в изолированной среде.  
**Решение**: Архитектура с async queue + isolated runner service.  
**Последствия**: Добавление очереди, внешнего API runner, таблиц для submissions и results.

---

## 1) C4 Архитектурная схема

### Level 1: System Context

```
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│   Student       │     │   Laravel LMS    │     │   Runner API    │
│   Browser       │────▶│   (Backend)      │────▶│   (Sandbox)     │
│   (Livewire)    │     │   + Queue        │     │   (Puppeteer)   │
└─────────────────┘     └──────────────────┘     └─────────────────┘
        │                       │                        │
        ▼                       ▼                        ▼
  UI: Code Editor        DB: PostgreSQL            Docker/Jail
  + Results View       + Redis (queue)          + No network
```

### Level 2: Container Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                        LMS Application                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────────┐ │
│  │ Livewire     │    │ API Layer    │    │ Queue Worker     │ │
│  │ Components   │    │ Controllers  │    │ (Horizon/Database)│
│  │              │    │              │    │                  │ │
│  │ - Lessons    │    │ - /submit    │    │ Jobs:            │ │
│  │ - Practice  │    │ - /status    │    │ - RunPractice   │ │
│  │ - Results   │    │ - /callback  │    │ - Retry logic   │ │
│  └──────────────┘    └──────────────┘    └──────────────────┘ │
│         │                   │                      │            │
│         └───────────────────┼──────────────────────┘            │
│                             │                                   │
│         ┌───────────────────┴───────────────────────┐            │
│         │         Domain Services                   │            │
│         │  - PracticeEvaluationService            │            │
│         │  - SubmissionScoringService            │            │
│         │  - RunnerClient (HTTP)                 │            │
│         └────────────────────────────────────────┘            │
│                                                               │
└───────────────────────────────────────────────────────────────┘
                              │
                              │ HTTP POST /api/v1/evaluate
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    Runner Service (separate)                    │
├─────────────────────────────────────────────────────────────────┤
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────────┐ │
│  │ API Server  │    │ Sandbox      │    │ Test Executor   │ │
│  │ (FastAPI/  │───▶│ (Puppeteer   │───▶│ (DOM/CSS/JS)    │ │
│  │  Express)  │    │ in Docker)   │    │                  │ │
│  └──────────────┘    └──────────────┘    └──────────────────┘ │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
```

### Level 3: Component Responsibilities

| Component | Responsibility | Public API |
|-----------|---------------|------------|
| `LessonPractice` | Модель практики | `submit()`, `results()` |
| `PracticeSubmission` |attempt tracking| `submit()`, `complete()` |
| `RunPracticeSubmissionJob`| Queue job с retry | `handle()`, `failed()` |
| `RunnerClient` | HTTP client | `evaluate()`, `pollStatus()` |
| `PracticeEvaluationService`| Orchestration| `runEvaluation()` |
| `SubmissionScoringService`| Score calculation| `calculate()` |

---

## 2) API Contract: Laravel ↔ Runner

### 2.1 POST /api/v1/evaluate

**Request:**

```json
{
  "submission_id": 12345,
  "idempotency_key": "unique-key-per-attempt",
  "profile": "frontend_html_css_js_v1",
  "code": {
    "html": "<div class=\"card\">...</div>",
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
    },
    {
      "id": 3,
      "name": "No console errors",
      "type": "console_errors",
      "weight": 1.0,
      "timeout_ms": 500,
      "script": {}
    }
  ],
  "limits": {
    "total_timeout_ms": 5000,
    "memory_mb": 128,
    "cpu_ms": 3000
  }
}
```

**Success Response (202 Accepted):**

```json
{
  "submission_id": 12345,
  "runner_job_id": "job_abc123",
  "status": "running",
  "estimated_duration_ms": 3500,
  "webhook_url": "https://lms.internal/api/v1/callback/job_abc123"
}
```

**Error Response (4xx/5xx):**

```json
{
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Invalid test type: 'invalid_type'",
    "details": [
      { "field": "tests[0].type", "message": "Must be one of: dom, css, behavior, console_errors" }
    ]
  }
}
```

### 2.2 GET /api/v1/jobs/{runner_job_id}

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
      "message": "Selector .card found"
    },
    {
      "id": 2,
      "passed": false,
      "earned_score": 0,
      "duration_ms": 45,
      "message": "Expected width: 300px, got: 200px"
    }
  ],
  "score": 6.67,
  "passed": false,
  "runner_version": "1.0.0"
}
```

### 2.3 Webhook Callback (optional)

```json
{
  "runner_job_id": "job_abc123",
  "status": "completed",
  "signature": "sha256=abc123...",
  "timestamp": 1700000000,
  "payload": { ...same as GET response... }
}
```

### 2.4 Error Codes

| Code | HTTP Status | Description |
|------|-------------|-------------|
| `VALIDATION_ERROR` | 400 | Invalid request body |
| `QUOTA_EXCEEDED` | 429 | Rate limit hit |
| `RUNNER_UNAVAILABLE` | 503 | Runner is down |
| `TIMEOUT` | 504 | Execution timeout |
| `SANDBOX_ERROR` | 500 | Sandbox internal error |

---

## 3) Isolation Approach: Trade-offs

### Comparison Matrix

| Approach | Startup Time | Isolation | Resource Limits | Complexity | Suitable For |
|----------|--------------|-----------|-----------------|------------|--------------|
| **Docker ( recommended)** | 1-3s | Strong (cgroups/namespace) | Precise (CPU/RAM) | Medium | Production |
| **jail/chroot** | <1s | Medium | Limited | Low | Simple JS only |
| **Firecracker-microVM** | 2-5s | Very Strong | Precise | High | Multi-tenant SaaS |
| **Node.js sandbox** | <100ms | Weak (no kernel隔离) | Poor | Low | Dev/MVP only |

### Recommended: Docker + Puppeteer

```dockerfile
# Runner Container
FROM node:20-alpine

# Install Puppeteer + dependencies
RUN apk add --no-cache \
    chromium \
    chromium-chromedriver \
    libstdc++ \
    libcrypto3 \
    libssl3 \
    ttf-freefont

# Security: run as non-root
RUN adduser -D runner && chown -R runner:runner /app
USER runner

# Resource limits (set by orchestrator)
# CPU: 0.5 cores, RAM: 256MB
```

**Security Hardening:**
- No network access (`--network=none` on k8s)
- Read-only filesystem where possible
- Seccomp profile (block `execve`, `mount`, etc.)
- Timeout: max 30s per test, 60s total for submission

---

## 4) Non-Functional Requirements

### Performance

| Metric | Target | Threshold (SLO) |
|--------|--------|------------------|
| p50 latency (simple test) | <2s | 3s |
| p95 latency | <5s | 8s |
| p99 latency | <10s | 15s |
| Throughput | 10 req/min per runner | 50 req/min with scale-out |
| Availability | 99.9% | 99.5% |

### Scalability

- **Horizontal**: Runner pods stateless, add via K8s HPA
- **Queue**: Redis + Horizon for async processing
- **Database**: Connection pooling (max 10 per worker)

### Reliability

- **Retry Logic**: 3 attempts with exponential backoff (1s, 2s, 4s)
- **Timeout**: Job timeout > runner timeout (30s vs 20s)
- **Idempotency**: `submission_id` + `attempt_no` as key

---

## 5) Failure Modes & Graceful Degradation

| Failure Mode | Detection | Degradation Strategy |
|-------------|-----------|---------------------|
| Runner 5xx | HTTP 5xx from client | Retry via queue (max 3x) |
| Runner timeout | Job exceeds timeout | Mark `timeout`, show message |
| Queue full | Redis OOM or backup > N | Return 503 "Try later" |
| DB unavailable | Connection error | Logerror, mark `system_error` |
| Sandbox escape attempt | Suspicious syscall | Kill container, mark `blocked` |

### Fallback UI States

| Backend State | User Message |
|--------------|--------------|
| `pending` | "Ожидание проверки..." |
| `running` | "Проверка... ({n}/10)" |
| `completed` | Show score + details |
| `failed` | "Ошибка проверки. Попробуйте позже." |
| `timeout` | "Превышено время. Оптимизируйте код." |
| `rate_limited` | "Подождите {cooldown} секунд" |

---

## 6) Migration Path (Zero-Downtime)

### Phase 1: Database Schema (Week 1)

```sql
-- Add nullable columns (no migration of existing data)
ALTER TABLE lesson_practices ADD COLUMN is_active BOOLEAN DEFAULT true;
ALTER TABLE lesson_practices ADD COLUMN runner_profile VARCHAR(50);
ALTER TABLE lesson_practices ADD COLUMN max_score DECIMAL(3,1) DEFAULT 10.0;
ALTER TABLE lesson_practices ADD COLUMN pass_score DECIMAL(3,1) DEFAULT 7.0;
```

### Phase 2: Backend Services (Week 2)

- Deploy `RunnerClient` service
- Deploy `RunPracticeSubmissionJob` with `tries=0` initially
- Feature flag: `enable_runner=false`

### Phase 3: Shadow Mode (Week 3)

- Flag `enable_runner=true` for 5% of users
- Compare results: old logic vs new runner
- Log diffs but don't expose to user

### Phase 4: Rollout (Week 4)

- 10% → 25% → 50% → 100%
- Monitor error rate, latency
- Quick rollback: set flag to `false`

### Phase 5: Cleanup (Week 5)

- Remove old validation logic
- Deprecate legacy columns
- Archive old data if needed

---

## 7) ADR Summary Table

| ADR | Decision | Rationale |
|-----|----------|-----------|
| ADR-001 | Async queue + isolated runner | Safety + scalability |
| ADR-002 | Docker + Puppeteer for sandbox | Balanced isolation/complexity |
| ADR-003 | HTTP polling (not webhook) | Simpler failure handling |
| ADR-004 | Score = (earned/max)*10 | Matches existing quiz logic |
| ADR-005 | 3 retries with backoff | Industry standard |

---

## 8) Next Steps

1. **Implement Phase 1**: Database migrations
2. **Implement Phase 2**: Backend services + job
3. **Deploy Runner**: Docker container with Puppeteer
4. **A/B Test**: Shadow mode comparison
5. **Full Rollout**: Enable for all practices