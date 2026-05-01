<div class="practice-editor">
    <style>
        .practice-editor {
            --primary: #6366f1;
            --secondary: #7c3aed;
            --success: #22c55e;
            --error: #ef4444;
            --text-main: #e2e8f0;
            --text-muted: #94a3b8;
            --bg-card: rgba(255,255,255,0.05);
            --border-card: rgba(255,255,255,0.1);
        }
        
        .editor-tabs {
            display: flex;
            gap: 4px;
            background: var(--bg-card);
            padding: 4px;
            border-radius: 12px;
            margin-bottom: 16px;
        }
        
        .editor-tab {
            flex: 1;
            padding: 10px 16px;
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-weight: 600;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.2s;
        }
        
        .editor-tab:hover {
            color: var(--text-main);
        }
        
        .editor-tab.active {
            background: var(--primary);
            color: white;
        }
        
        .code-editor {
            width: 100%;
            min-height: 250px;
            background: rgba(0,0,0,0.3);
            border: 1px solid var(--border-card);
            border-radius: 12px;
            color: var(--text-main);
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 14px;
            padding: 16px;
            resize: vertical;
        }
        
        .code-editor:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        .submit-btn {
            width: 100%;
            padding: 16px 32px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .submit-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
        }
        
        .submit-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .result-card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 16px;
            padding: 24px;
            margin-top: 24px;
        }
        
        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .score-badge {
            font-size: 2rem;
            font-weight: 800;
        }
        
        .score-badge.passed {
            color: var(--success);
        }
        
        .score-badge.failed {
            color: var(--error);
        }
        
        .test-result {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 8px;
            background: rgba(0,0,0,0.2);
        }
        
        .test-result.passed {
            border-left: 3px solid var(--success);
        }
        
        .test-result.failed {
            border-left: 3px solid var(--error);
        }
        
        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
    
    <div class="practice-section" style="margin-top: 40px;">
        <h3 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 16px;">
            <span style="margin-right: 8px;">💻</span>
            Практическое задание
        </h3>
        
        @if($practice->description)
            <p style="color: var(--text-muted); margin-bottom: 20px;">{!! $practice->description !!}</p>
        @endif

        <div style="display: grid; grid-cols: 1; md:grid-cols-2 gap: 24px; margin-bottom: 32px;">
            <div style="background: var(--bg-card); border: 1px solid var(--border-card); border-radius: 16px; padding: 24px;">
                <h4 style="color: var(--primary); font-weight: 700; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Цель задания
                </h4>
                <div style="color: var(--text-main); font-size: 0.95rem; line-height: 1.6;">
                    {!! nl2br(e($practice->objective)) !!}
                </div>
                
                @if($practice->checking_criteria)
                    <h4 style="color: var(--secondary); font-weight: 700; margin-top: 24px; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Критерии проверки
                    </h4>
                    <div style="color: var(--text-main); font-size: 0.95rem; line-height: 1.6;">
                        {!! nl2br(e($practice->checking_criteria)) !!}
                    </div>
                @endif
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--border-card); border-radius: 16px; padding: 24px;">
                <h4 style="color: var(--primary); font-weight: 700; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Техническое задание
                </h4>
                <div style="color: var(--text-main); font-size: 0.95rem; line-height: 1.6; background: rgba(0,0,0,0.2); padding: 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
                    {!! nl2br(e($practice->technical_task)) !!}
                </div>

                @if($practice->result_image_path)
                    <div style="margin-top: 24px;">
                        <h4 style="color: var(--text-main); font-weight: 700; margin-bottom: 12px;">Результат (образец):</h4>
                        <div style="border-radius: 12px; overflow: hidden; border: 1px solid var(--border-card);">
                            <img src="{{ asset('storage/' . $practice->result_image_path) }}" style="width: 100%; display: block;" alt="Результат">
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <div style="background: var(--bg-card); border: 1px solid var(--border-card); border-radius: 16px; padding: 24px;">
            <div class="editor-tabs">
                <button type="button" class="editor-tab active" wire:click="setActiveTab('html')">HTML</button>
                <button type="button" class="editor-tab" wire:click="setActiveTab('css')">CSS</button>
                <button type="button" class="editor-tab" wire:click="setActiveTab('js')">JavaScript</button>
            </div>
            
            @if($activeTab === 'html')
                <textarea 
                    wire:model.live="htmlCode"
                    class="code-editor"
                    placeholder="Введите HTML код..."
                    rows="10"
                ></textarea>
            @elseif($activeTab === 'css')
                <textarea 
                    wire:model.live="cssCode"
                    class="code-editor"
                    placeholder="/* Введите CSS код... */"
                    rows="10"
                ></textarea>
            @elseif($activeTab === 'js')
                <textarea 
                    wire:model.live="jsCode"
                    class="code-editor"
                    placeholder="// Введите JavaScript код..."
                    rows="10"
                ></textarea>
            @endif
            
            @if(!$isRunning && !$showResults)
                <button 
                    type="button" 
                    class="submit-btn" 
                    wire:click="submit"
                    style="margin-top: 20px;"
                >
                    Проверить задание
                </button>
            @elseif($isRunning)
                <div style="text-align: center; padding: 20px; color: var(--text-muted);">
                    <div class="spinner" style="margin: 0 auto 12px;"></div>
                    <p>Проверка решения...</p>
                    <p style="font-size: 0.875rem;">Попытка #{{ $attemptCount }}</p>
                </div>
            @endif
            
            @if($showResults && $currentSubmission)
                <div class="result-card">
                    <div class="result-header">
                        <div>
                            <h4 style="margin-bottom: 4px;">
                                @if($currentSubmission->passed)
                                    <span style="color: var(--success);">✓ Задание выполнено!</span>
                                @else
                                    <span style="color: var(--error);">✗ Не выполнено</span>
                                @endif
                            </h4>
                            <p style="color: var(--text-muted); font-size: 0.875rem;">
                                Попытка #{{ $currentSubmission->attempt_no }}
                            </p>
                        </div>
                        <div class="score-badge {{ $currentSubmission->passed ? 'passed' : 'failed' }}">
                            {{ $currentSubmission->score ?? '—' }}/10
                        </div>
                    </div>
                    
                    @if(count($testResults) > 0)
                        <div style="margin-bottom: 16px;">
                            <p style="font-weight: 600; margin-bottom: 12px;">Результаты проверки:</p>
                            @foreach($testResults as $result)
                                <div class="test-result {{ $result['passed'] ? 'passed' : 'failed' }}">
                                    <span>{{ $result['passed'] ? '✓' : '✗' }}</span>
                                    <div style="flex: 1;">
                                        <p style="font-weight: 600;">{{ $result['name'] }}</p>
                                        @if($result['message'])
                                            <p style="font-size: 0.875rem; color: var(--text-muted);">{{ $result['message'] }}</p>
                                        @endif
                                    </div>
                                    <span style="color: var(--text-muted); font-size: 0.875rem;">
                                        +{{ $result['earned_weight'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    
                    <button 
                        type="button" 
                        class="submit-btn" 
                        wire:click="retake"
                        style="margin-top: 16px;"
                    >
                        Попробовать снова
                    </button>
                </div>
            @endif
            
            @if($showResults && $currentSubmission && in_array($currentSubmission->status, ['failed', 'timeout']))
                <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 12px; padding: 16px; margin-top: 16px; color: var(--error);">
                    <p style="font-weight: 600;">⚠️ Ошибка проверки</p>
                    <p style="font-size: 0.875rem; margin-top: 4px;">{{ $currentSubmission->error_message ?? 'Попробуйте ещё раз через несколько секунд.' }}</p>
                </div>
            @endif
        </div>
    </div>
    
    <script>
        setInterval(() => {
            @if($isRunning)
                @this.checkStatus();
            @endif
        }, 2000);
    </script>
</div>