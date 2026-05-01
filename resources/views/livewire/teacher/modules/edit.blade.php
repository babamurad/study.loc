<div class="p-6 max-w-2xl mx-auto">
    <div class="mb-6">
        <flux:button href="{{ route('teacher.modules.index') }}" variant="ghost" icon="arrow-left" class="-ml-2">Назад к списку</flux:button>
        <flux:heading size="xl" class="mt-2">Редактировать модуль: {{ $module->title }}</flux:heading>
    </div>

    <flux:card>
        <div class="mb-6 border-b">
            <nav class="flex space-x-8" aria-label="Tabs">
                <button 
                    wire:click="$set('activeTab', 'content')"
                    class="{{ $activeTab === 'content' ? 'border-indigo-500 dark:border-indigo-400 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-500' }} 
                    py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                >
                    Содержимое
                </button>
                <button 
                    wire:click="$set('activeTab', 'practice')"
                    class="{{ $activeTab === 'practice' ? 'border-indigo-500 dark:border-indigo-400 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-500' }} 
                    py-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2"
                >
                    Практика
                    @if($practiceEnabled)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800/20 dark:text-green-300">
                            ✓
                        </span>
                    @endif
                </button>
            </nav>
        </div>

        @if($activeTab === 'content')
            <form wire:submit="save" class="space-y-6">
                <flux:field>
                    <flux:label>Название модуля</flux:label>
                    <flux:input wire:model="title" />
                    <flux:error name="title" />
                </flux:field>

                <flux:field>
                    <flux:label>Курс</flux:label>
                    <flux:select wire:model="course_id">
                        @foreach ($courses as $course)
                            <flux:select.option value="{{ $course->id }}">{{ $course->title }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="course_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Позиция (порядок)</flux:label>
                    <flux:input type="number" wire:model="position" min="1" />
                    <flux:description>Порядок отображения модуля в курсе.</flux:description>
                    <flux:error name="position" />
                </flux:field>

                <div class="flex justify-end space-x-2 border-t pt-6">
                    <flux:button href="{{ route('teacher.modules.index') }}" variant="ghost">Отмена</flux:button>
                    <flux:button type="submit" variant="primary">Сохранить изменения</flux:button>
                </div>
            </form>
        @elseif($activeTab === 'practice')
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <flux:checkbox wire:model.live="practiceEnabled" />
                        <flux:heading size="lg">Включить итоговую практику по разделу</flux:heading>
                    </div>
                </div>

                @if($practiceEnabled)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <flux:input wire:model="practiceTitle" label="Название практики" placeholder="Например: Итоговое задание по разделу" />
                        <flux:input type="number" wire:model="practiceMaxScore" label="Макс. балл" step="0.1" min="1" />
                        <flux:input type="number" wire:model="practicePassScore" label="Проходной балл" step="0.1" min="0" />
                    </div>

                    <flux:textarea wire:model="practiceDescription" label="Короткое описание" placeholder="Опишите задание..." rows="2" />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <flux:textarea wire:model="practiceObjective" label="Цель задания" rows="4" />
                            <flux:textarea wire:model="practiceCheckingCriteria" label="Критерии проверки" rows="4" />
                        </div>
                        <div class="space-y-4">
                            <flux:textarea wire:model="practiceTechnicalTask" label="Техническое задание" rows="9" />
                        </div>
                    </div>

                    <div class="border-t pt-6">
                        <flux:heading size="lg" class="mb-4">Изображение результата</flux:heading>
                        <div class="flex items-start gap-6">
                            <div class="flex-1">
                                <flux:input type="file" wire:model="practiceResultImage" label="Загрузить новое изображение" />
                            </div>
                            @if ($practiceResultImage)
                                <div class="w-48 h-32 rounded-lg overflow-hidden border flex items-center justify-center">
                                    <img src="{{ $practiceResultImage->temporaryUrl() }}" class="max-w-full max-h-full object-contain">
                                </div>
                            @elseif ($existingResultImagePath)
                                <div class="w-48 h-32 rounded-lg overflow-hidden border flex items-center justify-center">
                                    <img src="{{ asset('storage/' . $existingResultImagePath) }}" class="max-w-full max-h-full object-contain">
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="border-t pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <flux:heading size="lg">Тест-кейсы</flux:heading>
                            <flux:button wire:click="addTestCase" size="sm" variant="outline">+ Добавить тест</flux:button>
                        </div>

                        @foreach($practiceTestCases as $index => $testCase)
                            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4 mb-4 border">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                    <flux:input wire:model="practiceTestCases.{{ $index }}.name" label="Название теста" />
                                    <flux:select wire:model="practiceTestCases.{{ $index }}.type" label="Тип">
                                        <flux:select.option value="dom">DOM</flux:select.option>
                                        <flux:select.option value="css">CSS</flux:select.option>
                                        <flux:select.option value="behavior">Behavior</flux:select.option>
                                    </flux:select>
                                    <flux:input type="number" wire:model="practiceTestCases.{{ $index }}.weight" label="Вес" step="0.5" />
                                    <div class="flex items-end">
                                        <flux:button wire:click="removeTestCase('{{ $testCase['id'] }}')" variant="danger" size="sm">Удалить</flux:button>
                                    </div>
                                </div>
                                <flux:textarea wire:model="practiceTestCases.{{ $index }}.script" label="Скрипт проверки (JSON)" rows="2" />
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="flex justify-end gap-4 border-t pt-6">
                    <flux:button href="{{ route('teacher.modules.index') }}" variant="ghost">Отмена</flux:button>
                    <flux:button wire:click="save" variant="primary">Сохранить изменения</flux:button>
                </div>
            </div>
        @endif
    </flux:card>
</div>
