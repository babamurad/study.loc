<div>
    <header>
        <a href="{{ route('home') }}" class="logo">
            <div class="logo-box">/</div>
            WebDev Course
        </a>
        <nav style="display: flex; gap: 16px;">
            <a href="{{ route('dashboard') }}" style="color: var(--text-main); text-decoration: none; font-weight: 500; padding: 8px 16px; border-radius: 8px; background: var(--glass-bg); border: 1px solid var(--glass-border);">Личный кабинет</a>
        </nav>
    </header>

    <main style="padding: 40px 0;">
        <div style="margin-bottom: 32px;">
            <a href="{{ route('home') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.875rem;">← На главную</a>
        </div>

        <div style="background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 24px; padding: 40px; margin-bottom: 32px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <div>
                    <h1 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 8px;">{{ $course->title }}</h1>
                    <p style="color: var(--text-muted);">{{ $course->description }}</p>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 2rem; font-weight: 800; color: var(--primary-light);">{{ $this->progressPercent }}%</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Прогресс</div>
                </div>
            </div>

            <div style="background: rgba(99, 102, 241, 0.1); border-radius: 12px; height: 8px; overflow: hidden;">
                <div style="background: linear-gradient(90deg, var(--primary), var(--secondary)); height: 100%; width: {{ $this->progressPercent }}%; transition: width 0.5s;"></div>
            </div>
        </div>

        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 24px;">Уроки</h2>

        <div style="display: flex; flex-direction: column; gap: 12px;">
            @foreach($course->lessons as $lesson)
                @php($status = $lessonStatuses[$lesson->id] ?? 'locked')
                <div style="background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 16px; padding: 24px; display: flex; align-items: center; gap: 16px;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; @if($status === 'completed') background: rgba(34, 197, 94, 0.2); @elseif($status === 'available') background: rgba(99, 102, 241, 0.2); @else background: rgba(100, 116, 139, 0.2); @endif">
                        @if($status === 'completed') ✅ @elseif($status === 'available') 🔓 @else 🔒 @endif
                    </div>
                    <div style="flex-grow: 1;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 4px;">Урок {{ $lesson->position }}</div>
                        <h3 style="font-size: 1.125rem; font-weight: 600;">{{ $lesson->title }}</h3>
                    </div>
                    @if($status === 'locked')
                        <span style="color: var(--text-muted); font-size: 0.875rem;">Завершите предыдущий урок</span>
                    @else
                        <a href="{{ route('lessons.show', ['course' => $course->id, 'lesson' => $lesson->id]) }}"
                           style="color: var(--primary-light); text-decoration: none; font-weight: 600; padding: 8px 16px; border-radius: 8px; background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.2); transition: 0.3s;">
                            {{ $status === 'completed' ? 'Повторить' : 'Начать' }}
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </main>
</div>