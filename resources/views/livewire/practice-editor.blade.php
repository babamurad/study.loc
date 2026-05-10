<div class="practice-display">
    <style>
        .practice-display {
            --primary: #6366f1;
            --secondary: #7c3aed;
            --text-main: #e2e8f0;
            --text-muted: #94a3b8;
            --bg-card: rgba(255,255,255,0.05);
            --border-card: rgba(255,255,255,0.1);
        }
        
        .task-card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 20px;
            padding: 32px;
            margin-bottom: 24px;
            backdrop-filter: blur(10px);
        }

        .task-section-title {
            color: var(--primary);
            font-weight: 800;
            font-size: 1.1rem;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .task-content-box {
            color: var(--text-main);
            font-size: 1.05rem;
            line-height: 1.7;
            background: rgba(0,0,0,0.2);
            padding: 24px;
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .result-preview {
            margin-top: 32px;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--border-card);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
    </style>
    
    <div class="practice-header" style="margin-bottom: 32px;">
        <h3 style="font-size: 1.8rem; font-weight: 800; display: flex; align-items: center; gap: 12px; color: white;">
            <span style="background: linear-gradient(135deg, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">💻</span>
            Практическое задание
        </h3>
        @if($practice->description)
            <p style="color: var(--text-muted); font-size: 1.1rem; margin-top: 8px;">{!! $practice->description !!}</p>
        @endif
    </div>

    <div style="display: grid; grid-template-columns: 1fr; gap: 32px;">
        {{-- Goal & Technical Task --}}
        <div class="task-card">
            <div style="margin-bottom: 40px;">
                <h4 class="task-section-title">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Цель задания
                </h4>
                <div class="task-content-box">
                    {!! nl2br(e($practice->objective)) !!}
                </div>
            </div>

            <div>
                <h4 class="task-section-title" style="color: var(--secondary);">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Техническое задание
                </h4>
                <div class="task-content-box">
                    {!! nl2br(e($practice->technical_task)) !!}
                </div>
            </div>
        </div>

        {{-- Result Preview --}}
        @if($practice->result_image_path)
            <div class="task-card">
                <h4 class="task-section-title" style="color: var(--text-main);">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Ожидаемый результат
                </h4>
                <div class="result-preview">
                    <img src="{{ asset('storage/' . $practice->result_image_path) }}" style="width: 100%; display: block;" alt="Образец результата">
                </div>
            </div>
        @endif

        {{-- Criteria --}}
        @if($practice->checking_criteria)
            <div class="task-card" style="border-left: 4px solid var(--primary);">
                <h4 class="task-section-title">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Критерии успешного выполнения
                </h4>
                <div style="color: var(--text-muted); font-size: 1rem; line-height: 1.6;">
                    {!! nl2br(e($practice->checking_criteria)) !!}
                </div>
            </div>
        @endif
    </div>

    {{-- Редактор кода --}}
    <div class="task-card" style="margin-top: 32px; padding: 0; overflow: hidden; border: 1px solid var(--primary);">
        <div style="display: flex; background: rgba(0,0,0,0.3); border-bottom: 1px solid var(--border-card);">
            <button wire:click="setActiveTab('html')" style="flex: 1; padding: 16px; background: {{ $activeTab === 'html' ? 'rgba(99, 102, 241, 0.2)' : 'transparent' }}; color: {{ $activeTab === 'html' ? 'white' : 'var(--text-muted)' }}; border: none; border-bottom: {{ $activeTab === 'html' ? '2px solid var(--primary)' : '2px solid transparent' }}; font-weight: 600; cursor: pointer; transition: all 0.2s;">HTML</button>
            <button wire:click="setActiveTab('css')" style="flex: 1; padding: 16px; background: {{ $activeTab === 'css' ? 'rgba(99, 102, 241, 0.2)' : 'transparent' }}; color: {{ $activeTab === 'css' ? 'white' : 'var(--text-muted)' }}; border: none; border-bottom: {{ $activeTab === 'css' ? '2px solid var(--primary)' : '2px solid transparent' }}; font-weight: 600; cursor: pointer; transition: all 0.2s;">CSS</button>
            <button wire:click="setActiveTab('js')" style="flex: 1; padding: 16px; background: {{ $activeTab === 'js' ? 'rgba(99, 102, 241, 0.2)' : 'transparent' }}; color: {{ $activeTab === 'js' ? 'white' : 'var(--text-muted)' }}; border: none; border-bottom: {{ $activeTab === 'js' ? '2px solid var(--primary)' : '2px solid transparent' }}; font-weight: 600; cursor: pointer; transition: all 0.2s;">JS</button>
        </div>

        <div style="padding: 24px; background: #1e1e1e;">
            @if($activeTab === 'html')
                <textarea wire:model="htmlCode" style="width: 100%; height: 300px; background: transparent; color: #d4d4d4; border: none; font-family: monospace; font-size: 14px; outline: none; resize: vertical;" spellcheck="false" placeholder="<!-- Ваш HTML код -->"></textarea>
            @elseif($activeTab === 'css')
                <textarea wire:model="cssCode" style="width: 100%; height: 300px; background: transparent; color: #d4d4d4; border: none; font-family: monospace; font-size: 14px; outline: none; resize: vertical;" spellcheck="false" placeholder="/* Ваш CSS код */"></textarea>
            @elseif($activeTab === 'js')
                <textarea wire:model="jsCode" style="width: 100%; height: 300px; background: transparent; color: #d4d4d4; border: none; font-family: monospace; font-size: 14px; outline: none; resize: vertical;" spellcheck="false" placeholder="// Ваш JS код"></textarea>
            @endif
        </div>
        
        <div style="padding: 16px 24px; background: rgba(0,0,0,0.2); border-top: 1px solid var(--border-card); display: flex; justify-content: space-between; align-items: center;">
            <div style="color: var(--text-muted); font-size: 0.9rem;">
                Попыток: {{ $attemptCount }} 
                @if($bestSubmission)
                    <span style="margin-left: 12px; color: {{ $bestSubmission->passed ? '#22c55e' : '#eab308' }}">Лучший результат: {{ $bestSubmission->score }}/10</span>
                @endif
            </div>
            <button wire:click="submit" wire:loading.attr="disabled" class="submit-practice-btn" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.3s; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);">
                <span wire:loading.remove wire:target="submit">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
                <span wire:loading wire:target="submit">
                    <svg class="animate-spin" width="20" height="20" fill="none" viewBox="0 0 24 24" style="animation: spin 1s linear infinite;"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <style>@keyframes spin { 100% { transform: rotate(360deg); } }</style>
                </span>
                <span wire:loading.remove wire:target="submit">Проверить решение</span>
                <span wire:loading wire:target="submit">Отправка...</span>
            </button>
        </div>
    </div>

    {{-- Статус проверки (когда выполняется) --}}
    @if($isRunning)
        <div wire:poll.2s="checkStatus" class="task-card" style="margin-top: 24px; text-align: center; border-color: #eab308; background: rgba(234, 179, 8, 0.05);">
            <div style="margin-bottom: 16px;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#eab308" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="animation: spin 2s linear infinite; margin: 0 auto;"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg>
            </div>
            <h4 style="color: #eab308; font-size: 1.2rem; font-weight: bold; margin-bottom: 8px;">Решение проверяется...</h4>
            <p style="color: var(--text-muted);">Это может занять несколько секунд. Пожалуйста, подождите.</p>
            
            @if(count($testResults) > 0)
                <div style="margin-top: 24px; text-align: left; max-width: 600px; margin-left: auto; margin-right: auto;">
                    @foreach($testResults as $result)
                        <div style="padding: 12px; margin-bottom: 8px; background: rgba(0,0,0,0.2); border-radius: 8px; display: flex; align-items: center; gap: 12px;">
                            @if($result['status'] === 'completed')
                                @if($result['passed'])
                                    <span style="color: #22c55e;">✅</span>
                                @else
                                    <span style="color: #ef4444;">❌</span>
                                @endif
                            @else
                                <span style="color: #eab308;">⏳</span>
                            @endif
                            <span style="color: var(--text-main);">{{ $result['name'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- Результаты --}}
    @if($showResults && $currentSubmission)
        <div class="task-card" id="practice-results" style="margin-top: 24px; border-color: {{ $currentSubmission->passed ? '#22c55e' : '#ef4444' }}; background: {{ $currentSubmission->passed ? 'rgba(34, 197, 94, 0.05)' : 'rgba(239, 68, 68, 0.05)' }};">
            <div style="text-align: center; margin-bottom: 24px;">
                @if($currentSubmission->passed)
                    <div style="width: 64px; height: 64px; background: #22c55e; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; box-shadow: 0 0 20px rgba(34, 197, 94, 0.4);">
                        <svg width="32" height="32" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 style="color: #22c55e; font-size: 1.5rem; font-weight: 800; margin-bottom: 8px;">Решение принято!</h3>
                    <p style="color: var(--text-main); font-size: 1.1rem;">Оценка: <strong>{{ $currentSubmission->score }}</strong> из 10</p>
                @else
                    <div style="width: 64px; height: 64px; background: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; box-shadow: 0 0 20px rgba(239, 68, 68, 0.4);">
                        <svg width="32" height="32" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                    <h3 style="color: #ef4444; font-size: 1.5rem; font-weight: 800; margin-bottom: 8px;">Задание не выполнено</h3>
                    <p style="color: var(--text-main); font-size: 1.1rem;">Оценка: <strong>{{ $currentSubmission->score }}</strong> из 10 (проходной балл: {{ $practice->pass_score }})</p>
                    
                    @if($currentSubmission->error_message)
                        <div style="margin-top: 16px; padding: 16px; background: rgba(0,0,0,0.3); border-radius: 8px; color: #fca5a5; font-family: monospace; text-align: left; overflow-x: auto;">
                            {{ $currentSubmission->error_message }}
                        </div>
                    @endif
                @endif
            </div>

            @if($currentSubmission->status === 'completed' && count($testResults) > 0)
                <h4 style="color: var(--text-main); font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; border-bottom: 1px solid var(--border-card); padding-bottom: 8px;">Результаты тестов:</h4>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @foreach($testResults as $result)
                        <div style="padding: 16px; background: rgba(0,0,0,0.2); border-left: 4px solid {{ $result['passed'] ? '#22c55e' : '#ef4444' }}; border-radius: 8px;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: {{ $result['message'] ? '8px' : '0' }};">
                                <strong style="color: var(--text-main);">{{ $result['name'] }}</strong>
                                <span style="font-size: 0.9rem; color: var(--text-muted);">
                                    Вес: {{ $result['earned_weight'] }} 
                                    @if($result['passed'])
                                        <span style="color: #22c55e; margin-left: 8px;">Пройден</span>
                                    @else
                                        <span style="color: #ef4444; margin-left: 8px;">Провален</span>
                                    @endif
                                </span>
                            </div>
                            @if($result['message'])
                                <div style="color: {{ $result['passed'] ? '#86efac' : '#fca5a5' }}; font-size: 0.95rem; font-family: monospace; white-space: pre-wrap;">{{ $result['message'] }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <div style="margin-top: 32px; text-align: center;">
                <button wire:click="retake" style="background: transparent; color: var(--text-main); border: 1px solid var(--border-card); padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s;">
                    Скрыть результаты
                </button>
            </div>
            @if($currentSubmission->passed)
                <script>
                    setTimeout(() => {
                        window.scrollTo({
                            top: document.getElementById('practice-results').offsetTop - 100,
                            behavior: 'smooth'
                        });
                    }, 100);
                </script>
            @endif
        </div>
    @endif
</div>