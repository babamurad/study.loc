<div>
    @if(session('success'))
        <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); color: #22c55e; padding: 16px 24px; border-radius: 12px; margin-bottom: 24px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 16px 24px; border-radius: 12px; margin-bottom: 24px;">
            {{ session('error') }}
        </div>
    @endif

    <header>
        <a href="{{ route('courses.show', $course->id) }}" class="logo" style="font-size: 1rem;">
            ← {{ $course->title }}
        </a>
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="font-size: 0.875rem; color: var(--text-muted);">{{ $this->progressPercent }}% завершено</div>
        </div>
    </header>

    <main style="padding: 40px 0;">
        <div style="max-width: 800px;">
            <div style="margin-bottom: 24px;">
                <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px;">
                    Урок {{ $lesson->position }} из {{ $course->lessons->count() }}
                </div>
                <h1 style="font-size: 2rem; font-weight: 800;">{{ $lesson->title }}</h1>
            </div>

            <div style="background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 24px; padding: 40px; margin-bottom: 32px;">
                <div style="font-size: 1rem; line-height: 1.8;">
                    {!! $lesson->content !!}
                </div>
            </div>

            @if($this->justCompleted)
                <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); border-radius: 16px; padding: 24px; margin-bottom: 24px;">
                    <h3 style="color: #22c55e; font-size: 1.125rem; margin-bottom: 8px;">✅ Урок завершён!</h3>
                    @if($this->nextLesson)
                        <p style="color: var(--text-muted); margin-bottom: 16px;">Следующий урок: <strong>{{ $this->nextLesson->title }}</strong></p>
                        <a href="{{ route('lessons.show', ['course' => $course->id, 'lesson' => $this->nextLesson->id]) }}"
                           style="display: inline-block; color: var(--bg); background: #22c55e; padding: 12px 24px; border-radius: 12px; text-decoration: none; font-weight: 600;">
                            Перейти к следующему уроку →
                        </a>
                    @else
                        <p style="color: var(--text-muted);">Поздравляем! Вы завершили этот курс!</p>
                    @endif
                </div>
            @endif

            @if(!$lesson->isCompletedBy(auth()->user()) && !$this->justCompleted)
                <form method="POST" action="{{ route('lessons.complete', $lesson->id) }}">
                    @csrf
                    <button type="submit"
                            style="color: white; background: linear-gradient(135deg, var(--primary), var(--secondary)); padding: 16px 32px; border-radius: 12px; border: none; font-size: 1rem; font-weight: 600; cursor: pointer; box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3); transition: 0.3s;">
                        Завершить урок
                    </button>
                </form>
            @endif
        </div>
    </main>
</div>