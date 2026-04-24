<div>
    <header>
        <a href="{{ route('home') }}" class="logo">
            <div class="logo-box">/</div>
            WebDev Course
        </a>
        <nav style="display: flex; gap: 16px;">
            @auth
                <a href="{{ route('dashboard') }}" style="color: var(--text-main); text-decoration: none; font-weight: 500; padding: 8px 16px; border-radius: 8px; background: var(--glass-bg); border: 1px solid var(--glass-border); transition: 0.3s;">Личный кабинет</a>
            @else
                <a href="{{ route('login') }}" style="color: var(--text-main); text-decoration: none; font-weight: 500; padding: 8px 16px; border-radius: 8px; transition: 0.3s;">Войти</a>
                <a href="{{ route('register') }}" style="color: var(--text-main); text-decoration: none; font-weight: 500; padding: 8px 16px; border-radius: 8px; background: linear-gradient(135deg, var(--primary), var(--secondary)); box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2); transition: 0.3s;">Регистрация</a>
            @endauth
        </nav>
    </header>

    <main>
        <section class="hero">
            <span class="badge">Курс для начинающих 🚀</span>
            <h1>Стань мастером <span>Web-разработки</span></h1>
            <p>Пройди путь от создания первой HTML-страницы до разработки современных адаптивных сайтов на профессиональном уровне.</p>
        </section>

        <div class="grid">
            @if($currentLesson)
            <a href="{{ route('lessons.show', ['course' => $course, 'lesson' => $currentLesson]) }}" class="card">
                <div class="card-icon">📖</div>
                <div class="roadmap-week">Неделя {{ $currentLesson->module->position }}</div>
                <h3>Текущий урок: {{ $currentLesson->title }}</h3>
                <p>{{ Str::limit($currentLesson->content, 100) }}</p>
                <div class="card-footer">
                    Перейти к уроку
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </div>
            </a>
            @endif

            <a href="/playground.html" class="card">
                <div class="card-icon">🚀</div>
                <div class="roadmap-week">Инструмент</div>
                <h3>Песочница</h3>
                <p>Интерактивная среда для экспериментов с кодом. Пиши HTML и сразу видишь результат.</p>
                <div class="card-footer">
                    Открыть редактор
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </div>
            </a>
        </div>

        <section class="section-title">
            <span class="badge">Твоя траектория</span>
            <h2>Дорожная карта обучения</h2>
        </section>

        <div class="roadmap">
            @foreach($course->modules as $module)
                @php
                    $firstLesson = $module->lessons->first();
                    $status = $user ? $lessonAccessService->getStatus($user, $firstLesson) : ($module->position === 1 ? 'available' : 'locked');
                    $isActive = $currentLesson && $currentLesson->module_id === $module->id;
                    $isLocked = $status === 'locked';
                    $isCompleted = $status === 'completed';
                    $lessonCount = $module->lessons->count();
                @endphp

                <a href="{{ $isLocked ? '#' : route('lessons.show', ['course' => $course->id, 'lesson' => $firstLesson->id]) }}" 
                   class="roadmap-item {{ $isActive ? 'active' : '' }} {{ $isCompleted ? 'completed' : '' }}"
                   @if($isLocked) onclick="return false;" style="opacity: 0.6; cursor: not-allowed;" @endif
                >
                    <div class="roadmap-content">
                        <span class="roadmap-week">Неделя {{ $module->position }}</span>
                        <h4 class="roadmap-title">{{ $module->title }}</h4>
                        <p class="roadmap-desc">
                            {{ $lessonCount }} {{ $lessonCount % 10 == 1 && $lessonCount % 100 != 11 ? 'урок' : ($lessonCount % 10 >= 2 && $lessonCount % 10 <= 4 && ($lessonCount % 100 < 10 || $lessonCount % 100 >= 20) ? 'урока' : 'уроков') }}
                            &bull; 
                            @if($isLocked)
                                <span style="color: var(--text-muted);">Заблокировано</span>
                            @elseif($isCompleted)
                                <span style="color: #22c55e;">Пройдено</span>
                            @else
                                <span style="color: var(--primary-light);">Доступно</span>
                            @endif
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    </main>

    <footer>
        <p>&copy; {{ date('Y') }} WebDev Academy &bull; Создано для обучения</p>
    </footer>
</div>
