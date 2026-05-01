<div class="lesson-page-wrapper">
    <style>
        .lesson-page-wrapper { background-color: transparent !important; }
        .lesson-content { line-height: 1.8; color: var(--text-main, #e2e8f0); }
        .lesson-content section {
            background: var(--glass-bg, rgba(255,255,255,0.05));
            backdrop-filter: blur(12px);
            padding: 40px;
            border-radius: 24px;
            margin-bottom: 64px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--glass-border, rgba(255,255,255,0.1));
        }
        .lesson-content h2 { margin-bottom: 32px; font-size: 1.75rem; display: flex; align-items: center; gap: 12px; color: var(--text-main, #ffffff); }
        .lesson-content h3 { font-size: 1.4rem; margin: 48px 0 24px; color: var(--text-main, #ffffff); font-weight: 700; border-bottom: 1px solid var(--glass-border, rgba(255,255,255,0.1)); padding-bottom: 12px; }
        .lesson-content p { margin-bottom: 24px; font-size: 1.125rem; }
        .lesson-content ul, .lesson-content ol { margin-bottom: 24px; padding-left: 24px; font-size: 1.125rem; }
        .lesson-content li { margin-bottom: 12px; }
        
        .lesson-header-new { text-align: center; margin-bottom: 60px; }
        .lesson-header-new h1 { font-size: 2.5rem; font-weight: 800; color: var(--text-main, #ffffff); margin-bottom: 16px; letter-spacing: -0.02em; }
        .lesson-header-new .lead { font-size: 1.125rem; color: var(--text-muted, #94a3b8); max-width: 600px; margin: 0 auto; }
        .lesson-header-new .badge { display: inline-block; background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.3); color: var(--primary-light, #a5b4fc); padding: 4px 12px; border-radius: 20px; font-weight: 600; font-size: 0.85rem; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.05em; }

        .finish-lesson-btn {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            display: block;
            color: white;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            padding: 16px 32px;
            border-radius: 9999px;
            border: none;
            font-size: 1.125rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
            transition: all 0.3s ease;
        }
        .finish-lesson-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(79, 70, 229, 0.6);
        }
        .finish-lesson-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
    </style>
    <div class="lesson-container">
        @if (session('success'))
            <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); color: #22c55e; padding: 16px 24px; border-radius: 12px; margin-bottom: 24px;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 16px 24px; border-radius: 12px; margin-bottom: 24px;">
                {{ session('error') }}
            </div>
        @endif

        @php
            // Attempt to extract lead text from content if it contains <p class="lead">
            preg_match('/<p class="lead">(.*?)<\/p>/s', $lesson->content, $matches);
            $leadText = $matches[1] ?? '';
        @endphp

        <div style="margin-bottom: 32px;">
            <a href="{{ route('home') }}" style="text-decoration: none; color: var(--primary-light, #a5b4fc); font-weight: 600; display: inline-flex; align-items: center; gap: 8px; font-size: 1rem;">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Назад на главную
            </a>
        </div>

        <header class="lesson-header-new">
            <span class="badge">
                Неделя {{ $lesson->module->position }}
            </span>

            <h1>
                {{ str_replace('Неделя ' . $lesson->module->position . ': ', '', $lesson->module->title) }}
            </h1>

            @if($leadText)
                <p class="lead">
                    {{ $leadText }}
                </p>
            @endif
        </header>

        <div style="max-width: 600px; margin: 0 auto 40px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 8px;">
                <span style="font-size: 0.75rem; color: var(--text-muted, #94a3b8); text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em;">Ваш прогресс</span>
                <span style="font-size: 0.875rem; font-weight: 800; color: var(--primary-light, #a5b4fc);">{{ $this->progressPercent }}%</span>
            </div>
            <div style="background: rgba(255, 255, 255, 0.05); border-radius: 20px; height: 8px; overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.05);">
                <div style="background: linear-gradient(90deg, #6366f1, #a855f7); height: 100%; width: {{ $this->progressPercent }}%; transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 0 15px rgba(99, 102, 241, 0.5);"></div>
            </div>
        </div>

        @if($modulePractice)
            <div class="module-practice-container" style="margin-bottom: 64px;">
                <section style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(124, 58, 237, 0.05)); border: 1px solid var(--primary-light); padding: 40px; border-radius: 24px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);">
                    <div style="text-align: center; margin-bottom: 40px;">
                        <span style="background: var(--primary-lesson); color: white; padding: 4px 16px; border-radius: 20px; font-size: 0.875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 16px; display: inline-block;">Итоговый проект модуля</span>
                        <h2 style="font-size: 2rem; justify-content: center;">{{ $modulePractice->title }}</h2>
                    </div>

                    @livewire(App\Livewire\PracticeEditor::class, ['practice' => $modulePractice])
                </section>
            </div>
        @endif

        <main>
            @foreach($lesson->module->lessons()->orderBy('position')->get() as $m_lesson)
                @php
                    $isActive = $m_lesson->id === $lesson->id;
                    $isCompleted = $m_lesson->isCompletedBy(auth()->user());
                    
                    // Clean content: remove lead paragraph and outer section tags
                    $m_content = preg_replace('/<p class="lead">.*?<\/p>/s', '', $m_lesson->content);
                    $m_content = preg_replace('/^<section>(.*)<\/section>$/s', '$1', trim((string)$m_content));
                @endphp

                <div class="lesson-content" id="lesson-{{ $m_lesson->id }}">
                    <section style="{{ $isActive ? 'border-color: var(--primary-light, #a5b4fc); box-shadow: 0 0 0 1px var(--primary-light, #a5b4fc), 0 20px 25px -5px rgba(0, 0, 0, 0.2);' : '' }}">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
                            <h2>
                                <span>
                                    @if($m_lesson->position % 3 == 1) 🛠️ 
                                    @elseif($m_lesson->position % 3 == 2) 📝 
                                    @else 📋 
                                    @endif
                                </span> 
                                {{ $m_lesson->title }}
                            </h2>
                            @if($isCompleted)
                                <span style="background: rgba(34, 197, 94, 0.1); color: #22c55e; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">ПРОЙДЕНО</span>
                            @elseif($isActive)
                                <span style="background: rgba(79, 70, 229, 0.1); color: var(--primary-lesson); padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">ИЗУЧАЕТСЯ</span>
                            @endif
                        </div>

                        @if($isActive)
                            <div>
                                {!! $m_content !!}
                            </div>
                        @endif

                        {{-- Only show Quiz and Complete button for the ACTIVE lesson --}}
                        @if($isActive)
                            <div style="margin-top: 40px; border-top: 1px solid var(--border-lesson); padding-top: 40px;">
                                {{-- Quiz Section --}}
                                @if ($quiz)
                                    <div style="margin-top: 40px;">
                                        <h3 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 24px;">Проверка знаний</h3>

                                        @if ($quizResult)
                                            <div style="text-align: center;">
                                                @if ($quizResult['passed'])
                                                    <h3 style="color: #22c55e; font-size: 1.25rem; font-weight: bold; margin-bottom: 8px;">✅ Тест пройден!</h3>
                                                    <p>Ваш результат: {{ $quizResult['score'] }}%</p>
                                                @else
                                                    <h3 style="color: #ef4444; font-size: 1.25rem; font-weight: bold; margin-bottom: 8px;">❌ Тест не пройден</h3>
                                                    <p>Ваш результат: {{ $quizResult['score'] }}%. Попробуйте еще раз.</p>
                                                    <button wire:click="retakeQuiz" style="color: white; background: linear-gradient(135deg, var(--primary-lesson), #7c3aed); padding: 12px 24px; border-radius: 12px; border: none; font-size: 1rem; font-weight: 600; cursor: pointer; margin-top: 16px;">
                                                        Пройти еще раз
                                                    </button>
                                                @endif
                                            </div>
                                        @elseif ($quizInProgress)
                                            <div>
                                                @if ($questions->count() > 0)
                                                    @php $currentQuestion = $questions[$currentQuestionIndex]; @endphp
                                                    <div style="margin-bottom: 24px;">
                                                        <p style="font-size: 1.125rem; font-weight: 600;">Вопрос {{ $currentQuestionIndex + 1 }} из {{ $questions->count() }}</p>
                                                        <p>{{ $currentQuestion->question }}</p>
                                                    </div>
                                                    <div>
                                                        @foreach ($currentQuestion->answers as $answer)
                                                            <label style="display: block; padding: 16px; border: 1px solid var(--border-lesson); border-radius: 12px; margin-bottom: 12px; cursor: pointer; background: {{ isset($userAnswers[$currentQuestion->id]) && $userAnswers[$currentQuestion->id] == $answer->id ? 'rgba(99, 102, 241, 0.1)' : 'transparent' }};">
                                                                <input type="radio" name="answer-{{ $currentQuestion->id }}" value="{{ $answer->id }}" wire:model.live="userAnswers.{{ $currentQuestion->id }}">
                                                                {{ $answer->answer }}
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                    <div style="display: flex; justify-content: space-between; margin-top: 24px;">
                                                        <button wire:click="previousQuestion" @if($currentQuestionIndex === 0) disabled @endif style="padding: 12px 24px; border-radius: 12px; border: 1px solid var(--border-lesson); background: transparent; cursor: pointer;">
                                                            Назад
                                                        </button>
                                                        @if ($currentQuestionIndex < $questions->count() - 1)
                                                            <button wire:click="nextQuestion" style="padding: 12px 24px; border-radius: 12px; border: 1px solid var(--border-lesson); background: transparent; cursor: pointer;">
                                                                Далее
                                                            </button>
                                                        @else
                                                            <button wire:click="submitQuiz" style="color: white; background: linear-gradient(135deg, var(--primary-lesson), #7c3aed); padding: 12px 24px; border-radius: 12px; border: none; font-size: 1rem; font-weight: 600; cursor: pointer;">
                                                                Завершить тест
                                                            </button>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div style="text-align: center;">
                                                <p style="margin-bottom: 16px;">{{ $quiz->description }}</p>
                                                <button wire:click="startQuiz" style="color: white; background: linear-gradient(135deg, var(--primary-lesson), #7c3aed); padding: 12px 24px; border-radius: 12px; border: none; font-size: 1rem; font-weight: 600; cursor: pointer;">
                                                    Начать тест
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- Practice Section --}}
                                @if ($practice && $practice->is_active)
                                    <div style="margin-top: 40px;">
                                        <h3 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 24px;">💻 Практическое задание</h3>
                                        
                                        @livewire(App\Livewire\PracticeEditor::class, ['practice' => $practice])
                                    </div>
                                @endif

                                @if($justCompleted)
                                    <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); border-radius: 16px; padding: 24px; margin-top: 24px; text-align: center;">
                                        <h3 style="color: #22c55e; font-size: 1.25rem; font-weight: bold; margin-bottom: 8px;">✅ Урок завершён!</h3>
                                        @if($this->nextLesson)
                                            <p style="color: var(--text-muted-lesson); margin-bottom: 16px;">Следующий урок: <strong>{{ $this->nextLesson->title }}</strong></p>
                                            <a href="{{ route('lessons.show', ['course' => $this->course, 'lesson' => $this->nextLesson->id]) }}"
                                               style="display: inline-block; color: white; background: #22c55e; padding: 12px 24px; border-radius: 12px; text-decoration: none; font-weight: 600;">
                                                Перейти к следующему уроку →
                                            </a>
                                        @else
                                            <p style="color: var(--text-muted-lesson);">Поздравляем! Вы завершили этот курс!</p>
                                        @endif
                                    </div>
                                @endif

                                @if(!$isCompleted && !$justCompleted)
                                    <div style="text-align: center; margin-top: 40px;">
                                        @php
                                            $latestAttempt = $quiz ? App\Models\UserQuizAttempt::where('user_id', auth()->id())->where('lesson_quiz_id', $quiz->id)->latest()->first() : null;
                                            $quizPassed = $latestAttempt && $latestAttempt->passed;
                                            
                                            $practicePassed = null;
                                            $practiceBestScore = null;
                                            if ($practice && $practice->is_active) {
                                                $practicePassed = $practice->isPassedBy(auth()->user());
                                                $bestPractice = $practice->submissions()->where('user_id', auth()->id())->where('passed', true)->orderByDesc('score')->first();
                                                $practiceBestScore = $bestPractice?->score;
                                            }
                                        @endphp

                                        @if($practice && $practice->is_active)
                                            <div style="background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.3); border-radius: 16px; padding: 24px; margin-bottom: 24px;">
                                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                                                    <h3 style="font-size: 1.25rem; font-weight: bold; margin: 0;">💻 Практическое задание</h3>
                                                    @if($practicePassed)
                                                        <span style="background: rgba(34, 197, 94, 0.2); color: #22c55e; padding: 4px 12px; border-radius: 20px; font-size: 0.875rem; font-weight: 600;">
                                                            ✓ Выполнено ({{ $practiceBestScore }}/10)
                                                        </span>
                                                    @else
                                                        <span style="background: rgba(239, 68, 68, 0.2); color: #ef4444; padding: 4px 12px; border-radius: 20px; font-size: 0.875rem; font-weight: 600;">
                                                            Не выполнено
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                                @if($practice->description)
                                                    <p style="color: var(--text-muted-lesson); margin-bottom: 16px; font-size: 0.95rem;">{{ $practice->description }}</p>
                                                @endif
                                                
                                                <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                                                    <a href="{{ route('lessons.show', ['course' => $course, 'lesson' => $lesson]) }}#practice"
                                                       style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 12px; text-decoration: none; font-weight: 600; background: linear-gradient(135deg, var(--primary-lesson), #7c3aed); color: white;">
                                                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                                                        {{ $practicePassed ? 'Повторить практику' : 'Выполнить практику' }}
                                                    </a>
                                                </div>
                                            </div>
                                        @endif

                                        @if(!$quiz || $quizPassed)
                                            <button type="button" wire:click="complete" wire:loading.attr="disabled" class="finish-lesson-btn">
                                                <span wire:loading.remove wire:target="complete">Завершить урок</span>
                                                <span wire:loading wire:target="complete">Завершаем...</span>
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @else
                            <div style="margin-top: 20px; text-align: center;">
                                <a href="{{ route('lessons.show', ['course' => $this->course->id ?? $lesson->module->course_id, 'lesson' => $m_lesson->id]) }}" 
                                   style="text-decoration: none; color: var(--primary-lesson); font-weight: 600; font-size: 0.9rem;">
                                    @if($isCompleted)
                                        Открыть этот урок для повторения →
                                    @else
                                        Открыть этот урок для прохождения →
                                    @endif
                                </a>
                            </div>
                        @endif
                    </section>
                </div>
            @endforeach
        </main>

        <footer style="text-align: center; color: var(--text-muted, #94a3b8); font-size: 0.875rem; margin-top: 80px; padding: 40px 0; border-top: 1px solid var(--glass-border, rgba(255,255,255,0.1));">
            <p>&copy; 2026 WebDev Academy &bull; <span style="color: var(--primary-light, #a5b4fc); font-weight: 600;">Путь к мастерству</span> &bull; Неделя {{ $lesson->module->position }}</p>
        </footer>
    </div>

    <a href="/playground.html" target="_blank" style="position: fixed; bottom: 30px; right: 30px; background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; padding: 16px 28px; border-radius: 50px; text-decoration: none; font-weight: 700; display: flex; align-items: center; gap: 10px; box-shadow: 0 15px 35px rgba(99, 102, 241, 0.4); z-index: 100; transition: 0.3s; border: 1px solid rgba(255,255,255,0.1);">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 12l10 10 10-10L12 2zm0 2.83L19.17 12 12 19.17 4.83 12 12 4.83zM12 8.5L8.5 12l3.5 3.5 3.5-3.5L12 8.5z"></path></svg>
        Открыть песочницу
    </a>
</div>
