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
</div>