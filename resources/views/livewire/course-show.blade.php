<div>
    <header>
        <a href="{{ route('home') }}" class="logo">
            <div class="logo-box">/</div>
            WebDev Course
        </a>
        <nav style="display: flex; gap: 16px;">
            @auth
                <a href="{{ route('dashboard') }}" style="color: var(--text-main); text-decoration: none; font-weight: 500; padding: 8px 16px; border-radius: 8px; background: var(--glass-bg); border: 1px solid var(--glass-border);">Личный кабинет</a>
            @else
                <a href="{{ route('login') }}" style="color: var(--text-main); text-decoration: none; font-weight: 500; padding: 8px 16px; border-radius: 8px; background: var(--glass-bg); border: 1px solid var(--glass-border);">Войти</a>
            @endauth
        </nav>
    </header>

    <main style="padding: 40px 0;">
        {{-- Course Header --}}
        <div style="background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 24px; padding: 40px; margin-bottom: 32px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <div>
                    <h1 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 8px;">{{ $course->title }}</h1>
                    <p style="color: var(--text-muted);">{{ $course->description }}</p>
                </div>
                @auth
                <div style="text-align: right; flex-shrink: 0; padding-left: 24px;">
                    <div style="font-size: 2rem; font-weight: 800; color: var(--primary-light);">{{ $this->progressPercent }}%</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Прогресс</div>
                </div>
                @endauth
            </div>
            
            {{-- Progress Bar --}}
            @auth
            <div style="background: rgba(100, 116, 139, 0.2); border-radius: 12px; height: 8px; overflow: hidden;">
                <div style="background: linear-gradient(90deg, var(--primary), var(--secondary)); height: 100%; width: {{ $this->progressPercent }}%; transition: width 0.5s;"></div>
            </div>
            @endauth
        </div>

        {{-- Modules and Lessons --}}
        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 24px;">Содержание курса</h2>

        <div style="display: flex; flex-direction: column; gap: 24px;">
            @foreach($course->modules as $module)
                <div wire:key="module-{{ $module->id }}">
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 16px;">{{ $module->title }}</h3>
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @foreach($module->lessons()->orderBy('position')->get() as $lesson)
                            @php($status = $lessonStatuses[$lesson->id] ?? 'locked')
                            <div style="background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 16px; padding: 24px; display: flex; align-items: center; gap: 16px; opacity: {{ $status === 'locked' ? '0.6' : '1' }};">
                                {{-- Status Icon --}}
                                <div style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: white;
                                    @if($status === 'completed') background: #22c55e;
                                    @elseif($status === 'available') background: #6366f1;
                                    @else background: #64748b; @endif">
                                    @if($status === 'completed')
                                        <i class="fa fa-check"></i>
                                    @elseif($status === 'available')
                                        <i class="fa fa-play"></i>
                                    @else
                                        <i class="fa fa-lock"></i>
                                    @endif
                                </div>
                                {{-- Lesson Info --}}
                                <div style="flex-grow: 1;">
                                    <h4 style="font-size: 1.125rem; font-weight: 600;">{{ $lesson->title }}</h4>
                                </div>
                                {{-- Status Badge & Action Button --}}
                                <div>
                                    @if($status === 'locked')
                                        <span style="font-size: 0.875rem; font-weight: 500; padding: 6px 12px; border-radius: 99px; background: rgba(100, 116, 139, 0.2); color: #475569;">Заблокирован</span>
                                    @elseif($status === 'available')
                                        <a href="{{ route('lessons.show', ['course' => $course->id, 'lesson' => $lesson->id]) }}"
                                           style="text-decoration: none; font-weight: 600; padding: 8px 16px; border-radius: 12px; background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                                            Начать
                                        </a>
                                    @elseif($status === 'completed')
                                         <a href="{{ route('lessons.show', ['course' => $course->id, 'lesson' => $lesson->id]) }}"
                                           style="text-decoration: none; font-weight: 600; padding: 8px 16px; border-radius: 12px; background: rgba(34, 197, 94, 0.1); color: #16a34a;">
                                            Пройден
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Module Practice --}}
                    @foreach($module->practices as $practice)
                        <div style="margin-top: 12px; background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(124, 58, 237, 0.1)); border: 1px solid rgba(99, 102, 241, 0.3); border-radius: 16px; padding: 24px; display: flex; align-items: center; gap: 16px;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: white; background: linear-gradient(135deg, #6366f1, #7c3aed);">
                                <i class="fa fa-code"></i>
                            </div>
                            <div style="flex-grow: 1;">
                                <h4 style="font-size: 1.125rem; font-weight: 700; color: #a5b4fc;">💻 Итоговая практика: {{ $practice->title }}</h4>
                                <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 4px;">{{ Str::limit($practice->description, 100) }}</p>
                            </div>
                            <div>
                                @php($practicePassed = $practice->isPassedBy(auth()->user()))
                                @if($practicePassed)
                                    <span style="font-size: 0.875rem; font-weight: 600; color: #22c55e; background: rgba(34, 197, 94, 0.1); padding: 6px 12px; border-radius: 99px;">✓ Выполнено</span>
                                @else
                                    <a href="{{ route('lessons.show', ['course' => $course->id, 'lesson' => $module->lessons()->first()->id]) }}#practice-module-{{ $practice->id }}"
                                       style="text-decoration: none; font-weight: 700; padding: 8px 20px; border-radius: 12px; background: #6366f1; color: white; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);">
                                        Начать проект
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </main>
</div>
