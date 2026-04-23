<div>
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

    <header style="display: flex; align-items: center; justify-content: space-between;">
        <a href="{{ route('courses.show', $course->id) }}" style="font-size: 1rem; text-decoration: none; color: var(--text-muted); ">
            ← {{ $course->title }}
        </a>
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="font-size: 0.875rem; color: var(--text-muted);">{{ $progressPercent }}% завершено</div>
        </div>
    </header>

    <main style="padding: 40px 0;">
        <div style="max-width: 800px; margin: auto;">
            <div style="margin-bottom: 24px;">
                <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px;">
                    Урок {{ $lesson->position }} из {{ $course->lessons->count() }}
                </div>
                <h1 style="font-size: 2.5rem; font-weight: 800;">{{ $lesson->title }}</h1>
            </div>

            <div style="background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 24px; padding: 40px; margin-bottom: 32px; font-size: 1.125rem; line-height: 1.8;">
                {!! $lesson->content !!}
            </div>

            {{-- Quiz Section --}}
            @if ($quiz)
                <div style="background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 24px; padding: 40px; margin-bottom: 32px;">
                    <h2 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 24px;">Проверка знаний</h2>

                    @if ($quizResult)
                        <div style="text-align: center;">
                            @if ($quizResult['passed'])
                                <h3 style="color: #22c55e; font-size: 1.25rem; font-weight: bold; margin-bottom: 8px;">✅ Тест пройден!</h3>
                                <p>Ваш результат: {{ $quizResult['score'] }}%</p>
                            @else
                                <h3 style="color: #ef4444; font-size: 1.25rem; font-weight: bold; margin-bottom: 8px;">❌ Тест не пройден</h3>
                                <p>Ваш результат: {{ $quizResult['score'] }}%. Попробуйте еще раз.</p>
                                <button wire:click="retakeQuiz" style="color: white; background: linear-gradient(135deg, var(--primary), var(--secondary)); padding: 12px 24px; border-radius: 12px; border: none; font-size: 1rem; font-weight: 600; cursor: pointer; margin-top: 16px;">
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
                                        <label style="display: block; padding: 16px; border: 1px solid var(--glass-border); border-radius: 12px; margin-bottom: 12px; cursor: pointer; background: {{ isset($userAnswers[$currentQuestion->id]) && $userAnswers[$currentQuestion->id] == $answer->id ? 'rgba(99, 102, 241, 0.1)' : 'transparent' }};">
                                            <input type="radio" name="answer-{{ $currentQuestion->id }}" value="{{ $answer->id }}" wire:model.live="userAnswers.{{ $currentQuestion->id }}">
                                            {{ $answer->answer }}
                                        </label>
                                    @endforeach
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-top: 24px;">
                                    <button wire:click="previousQuestion" @if($currentQuestionIndex === 0) disabled @endif style="padding: 12px 24px; border-radius: 12px; border: 1px solid var(--glass-border); background: transparent; cursor: pointer;">
                                        Назад
                                    </button>
                                    @if ($currentQuestionIndex < $questions->count() - 1)
                                        <button wire:click="nextQuestion" style="padding: 12px 24px; border-radius: 12px; border: 1px solid var(--glass-border); background: transparent; cursor: pointer;">
                                            Далее
                                        </button>
                                    @else
                                        <button wire:click="submitQuiz" style="color: white; background: linear-gradient(135deg, var(--primary), var(--secondary)); padding: 12px 24px; border-radius: 12px; border: none; font-size: 1rem; font-weight: 600; cursor: pointer;">
                                            Завершить тест
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @else
                         <div style="text-align: center;">
                            <p style="margin-bottom: 16px;">{{ $quiz->description }}</p>
                            <button wire:click="startQuiz" style="color: white; background: linear-gradient(135deg, var(--primary), var(--secondary)); padding: 12px 24px; border-radius: 12px; border: none; font-size: 1rem; font-weight: 600; cursor: pointer;">
                                Начать тест
                            </button>
                        </div>
                    @endif
                </div>
            @endif


            @if($justCompleted)
                <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); border-radius: 16px; padding: 24px; margin-bottom: 24px; text-align: center;">
                    <h3 style="color: #22c55e; font-size: 1.25rem; font-weight: bold; margin-bottom: 8px;">✅ Урок завершён!</h3>
                    @if($nextLesson)
                        <p style="color: var(--text-muted); margin-bottom: 16px;">Следующий урок: <strong>{{ $this->nextLesson->title }}</strong></p>
                        <a href="{{ route('lessons.show', ['course' => $this->course, 'lesson' => $this->nextLesson->id]) }}"
                           style="display: inline-block; color: white; background: #22c55e; padding: 12px 24px; border-radius: 12px; text-decoration: none; font-weight: 600;">
                            Перейти к следующему уроку →
                        </a>
                    @else
                        <p style="color: var(--text-muted);">Поздравляем! Вы завершили этот курс!</p>
                    @endif
                </div>
            @endif

            @if(!$lesson->isCompletedBy(auth()->user()) && !$justCompleted)
                 <div style="text-align: center;">
                    @php
                        $latestAttempt = $quiz ? App\Models\UserQuizAttempt::where('user_id', auth()->id())->where('lesson_quiz_id', $quiz->id)->latest()->first() : null;
                        $quizPassed = $latestAttempt && $latestAttempt->passed;
                    @endphp

                    @if(!$quiz || $quizPassed)
                        <button type="button" wire:click="complete" wire:loading.attr="disabled"
                                style="color: white; background: linear-gradient(135deg, var(--primary), var(--secondary)); padding: 16px 32px; border-radius: 12px; border: none; font-size: 1rem; font-weight: 600; cursor: pointer; box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3); transition: 0.3s;">
                            <span wire:loading.remove wire:target="complete">Завершить урок</span>
                            <span wire:loading wire:target="complete">Завершаем...</span>
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </main>
</div>
