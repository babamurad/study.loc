<div class="p-6">
    <div class="mb-6">
        <flux:button href="{{ route('teacher.lessons.index') }}" variant="ghost" icon="chevron-left" class="mb-2">Назад к списку</flux:button>
        <flux:heading size="xl" level="1">Редактирование урока</flux:heading>
        <flux:subheading>Внесите необходимые изменения в урок</flux:subheading>
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:select wire:model.live="course_id" label="Курс" placeholder="Выберите курс">
                        @foreach ($courses as $course)
                            <flux:select.option value="{{ $course->id }}">{{ $course->title }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="module_id" label="Модуль" placeholder="Выберите модуль" :disabled="!$course_id">
                        @foreach ($modules as $module)
                            <flux:select.option value="{{ $module->id }}">{{ $module->title }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input wire:model.live="title" label="Заголовок" placeholder="Введите название урока" />
                    <flux:input wire:model="slug" label="Slug (URL)" placeholder="URL-адрес урока" />
                </div>

                <flux:textarea wire:model="content" label="Содержимое" placeholder="Текст урока (поддерживается HTML/Markdown)" rows="10" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                    <flux:input type="number" wire:model="position" label="Позиция" />
                    <flux:checkbox wire:model="is_published" label="Опубликован" />
                </div>

                <div class="flex justify-end gap-4 border-t pt-6">
                    <flux:button href="{{ route('teacher.lessons.index') }}" variant="ghost">Отмена</flux:button>
                    <flux:button type="submit" variant="primary">Сохранить изменения</flux:button>
                </div>
            </form>
        @elseif($activeTab === 'practice')
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <flux:checkbox wire:model.live="practiceEnabled" />
                        <flux:heading size="lg" class="text-gray-900 dark:text-gray-100">Включить практическое задание</flux:heading>
                    </div>
                </div>

                @if($practiceEnabled)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <flux:input wire:model="practiceTitle" label="Название практики" placeholder="Например: Создание карточки" />
                        <flux:input type="number" wire:model="practiceMaxScore" label="Макс. балл" step="0.1" min="1" />
                        <flux:input type="number" wire:model="practicePassScore" label="Проходной балл" step="0.1" min="0" />
                    </div>

                    <flux:textarea wire:model="practiceDescription" label="Описание для ученика" placeholder="Опишите задание..." rows="3" />

                    <div class="border-t pt-6">
<div class="flex items-center justify-between mb-4">
                            <flux:heading size="lg" class="text-gray-900 dark:text-gray-100">Тест-кейсы</flux:heading>
                            <flux:button wire:click="addTestCase" size="sm" variant="outline">
                                + Добавить тест
                            </flux:button>
                        </div>

                        @if(count($practiceTestCases) === 0)
                            <div class="text-center py-8 text-gray-500 bg-gray-100 dark:bg-gray-800/50 rounded-lg">
                                <p>Нет тест-кейсов. Добавьте первый тест для проверки заданий учеников.</p>
                            </div>
                        @endif

                        @foreach($practiceTestCases as $index => $testCase)
                            <div class="bg-gray-100 dark:bg-gray-800/50 rounded-lg p-4 mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                    <flux:input 
                                        wire:model="practiceTestCases.{{ $index }}.name" 
                                        label="Название теста" 
                                        placeholder="Например: Карточка существует" 
                                    />
                                    <flux:select wire:model="practiceTestCases.{{ $index }}.type" label="Тип">
                                        <flux:select.option value="dom">DOM - проверка элемента</flux:select.option>
                                        <flux:select.option value="css">CSS - проверка стилей</flux:select.option>
                                        <flux:select.option value="behavior">Behavior - проверка поведения</flux:select.option>
                                        <flux:select.option value="console_errors">Console - ошибки консоли</flux:select.option>
                                    </flux:select>
                                    <flux:input 
                                        type="number" 
                                        wire:model="practiceTestCases.{{ $index }}.weight" 
                                        label="Вес (балл)" 
                                        step="0.5" 
                                        min="0.5" 
                                    />
                                    <div class="flex items-end">
                                        <flux:checkbox wire:model="practiceTestCases.{{ $index }}.is_required" label="Обязательный" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Скрипт проверки (JSON)</label>
                                        <textarea 
                                            wire:model="practiceTestCases.{{ $index }}.script"
                                            rows="3"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm font-mono text-sm bg-white dark:bg-gray-900 dark:text-gray-300"
                                            placeholder='{"selector": ".card", "exists": true}'
                                        ></textarea>
                                    </div>
                                    <div class="flex items-start justify-end">
                                        <flux:button wire:click="removeTestCase('{{ $testCase['id'] }}')" size="sm" variant="danger" class="mt-6">
                                            Удалить тест
                                        </flux:button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="bg-blue-50 rounded-lg p-4 mt-6">
                            <h4 class="font-medium text-blue-900 mb-2">Примеры скриптов:</h4>
                            <div class="text-sm text-blue-800 space-y-1 font-mono">
                                <p>DOM: {"selector": ".card", "exists": true}</p>
                                <p>CSS: {"selector": ".card", "property": "width", "expected": "300px"}</p>
                                <p>Behavior: {"selector": ".btn", "event": "click", "expectedClass": "active"}</p>
                                <p>Console: {} (проверяет отсутствие ошибок)</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <p>Включите практику, чтобы добавить тестовые задания для учеников.</p>
                    </div>
                @endif

                <div class="flex justify-end gap-4 border-t pt-6">
                    <flux:button href="{{ route('teacher.lessons.index') }}" variant="ghost">Отмена</flux:button>
                    <flux:button wire:click="save" variant="primary">Сохранить изменения</flux:button>
                </div>
            </div>
        @endif
    </flux:card>
</div>