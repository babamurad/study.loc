<div>
    <form wire:submit="save">
        <div style="display: grid; gap: 24px;">
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Название</label>
                <input type="text" wire:model="title" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.2); color: white;">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Описание</label>
                <textarea wire:model="description" rows="3" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.2); color: white;"></textarea>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Цель задания</label>
                <textarea wire:model="objective" rows="3" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.2); color: white;"></textarea>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Техническое задание</label>
                <textarea wire:model="technicalTask" rows="4" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.2); color: white;"></textarea>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Критерии проверки</label>
                <textarea wire:model="checkingCriteria" rows="3" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.2); color: white;"></textarea>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Изображение результата</label>
                <input type="file" wire:model="resultImage" accept="image/*" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.2); color: white;">
                @if($resultImage)
                    <div style="margin-top: 12px;">
                        <p style="color: #94a3b8; font-size: 14px;">Предпросмотр:</p>
                        <img src="{{ $resultImage->temporaryUrl() }}" style="max-width: 300px; border-radius: 8px; margin-top: 8px;">
                    </div>
                @elseif($existingResultImagePath)
                    <div style="margin-top: 12px;">
                        <p style="color: #94a3b8; font-size: 14px;">Текущее изображение:</p>
                        <img src="{{ asset('storage/' . $existingResultImagePath) }}" style="max-width: 300px; border-radius: 8px; margin-top: 8px;">
                    </div>
                @endif
                @error('resultImage') <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span> @enderror
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Макс. балл</label>
                    <input type="number" wire:model="maxScore" min="1" step="0.1" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.2); color: white;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Проходной</label>
                    <input type="number" wire:model="passScore" min="0" step="0.1" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.2); color: white;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">&nbsp;</label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" wire:model="isActive">
                        Активно
                    </label>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 32px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h4 style="margin: 0;">Тесты ({{ count($testCases) }})</h4>
                <button type="button" wire:click="addTestCase" style="padding: 8px 16px; background: linear-gradient(135deg, #6366f1, #7c3aed); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                    + Добавить тест
                </button>
            </div>
            
            @foreach($testCases as $index => $testCase)
                <div style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 16px; margin-bottom: 12px;">
                    <div style="display: grid; grid-template-columns: 1fr 120px 80px 40px; gap: 12px; align-items: end;">
                        <div>
                            <input type="text" wire:model="testCases.{{ $index }}.name" placeholder="Название теста" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.3); color: white; font-size: 14px;">
                        </div>
                        <div>
                            <select wire:model="testCases.{{ $index }}.type" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.3); color: white; font-size: 14px;">
                                <option value="dom">DOM</option>
                                <option value="css">CSS</option>
                                <option value="behavior">Behavior</option>
                                <option value="console_errors">Console</option>
                            </select>
                        </div>
                        <div>
                            <input type="number" wire:model="testCases.{{ $index }}.weight" step="0.5" min="0.5" placeholder="Вес" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.3); color: white; font-size: 14px;">
                        </div>
                        <button type="button" wire:click="removeTestCase('{{ $testCase['id'] }}')" style="padding: 8px; background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; border-radius: 6px; cursor: pointer;">
                            ✕
                        </button>
                    </div>
                    
                    <div style="margin-top: 12px;">
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #94a3b8;">
                            <input type="checkbox" wire:model="testCases.{{ $index }}.is_required">
                            Обязательный тест
                        </label>
                    </div>
                    
                    <div style="margin-top: 8px;">
                        <label style="display: block; font-size: 12px; color: #94a3b8; margin-bottom: 4px;">Скрипт (JSON)</label>
                        <textarea wire:model="testCases.{{ $index }}.script" rows="2" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.3); color: white; font-size: 12px; font-family: monospace;"></textarea>
                    </div>
                </div>
            @endforeach
            
            @if(count($testCases) === 0)
                <div style="text-align: center; padding: 32px; color: #94a3b8; background: rgba(0,0,0,0.1); border-radius: 12px; border: 1px dashed rgba(255,255,255,0.1);">
                    Пока нет тестов. Добавьте первый тест для проверки заданий.
                </div>
            @endif
        </div>
        
        <div style="margin-top: 24px; display: flex; gap: 12px;">
            <button type="submit" style="flex: 1; padding: 14px; background: linear-gradient(135deg, #22c55e, #16a34a); color: white; border: none; border-radius: 10px; font-weight: 700; cursor: pointer;">
                Сохранить практику
            </button>
        </div>
    </form>
    
    @if(session('success'))
        <div style="margin-top: 16px; padding: 12px; background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); border-radius: 8px; color: #22c55e; text-align: center;">
            {{ session('success') }}
        </div>
    @endif
</div>