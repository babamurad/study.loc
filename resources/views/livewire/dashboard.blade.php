<div class="p-6">
    <div class="mb-8">
        <flux:heading size="xl" level="1">Добро пожаловать, {{ $user->name }}!</flux:heading>
        <flux:subheading>Здесь вы можете отслеживать свой прогресс и продолжать обучение.</flux:subheading>
    </div>

    @if ($student)
        <div class="mb-8 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4 flex items-center gap-3 text-amber-800 dark:text-amber-200">
            <flux:icon name="eye" class="size-5" />
            <div>
                <strong>Режим просмотра:</strong> Вы просматриваете дашборд от лица ученика {{ $student->name }}.
            </div>
            <flux:button href="{{ route('teacher.students.index') }}" wire:navigate size="sm" variant="subtle" class="ml-auto bg-amber-100 dark:bg-amber-900/50 hover:bg-amber-200 dark:hover:bg-amber-800/50">
                Вернуться к списку
            </flux:button>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <flux:select wire:model.live="statusFilter" class="w-full sm:w-auto min-w-40">
            <option value="all">Все статусы</option>
            <option value="in_progress">В процессе</option>
            <option value="not_started">Не начатые</option>
            <option value="completed">Завершенные</option>
        </flux:select>

        <flux:select wire:model.live="sortOrder" class="w-full sm:w-auto">
            <option value="progress_desc">По убыванию прогресса</option>
            <option value="progress_asc">По возрастанию прогресса</option>
            <option value="title_asc">По названию (А-Я)</option>
            <option value="title_desc">По названию (Я-А)</option>
        </flux:select>
    </div>

    @if ($courseProgress->isEmpty())
        <div class="py-20 text-center bg-neutral-50 dark:bg-neutral-800/50 rounded-xl border border-neutral-200 dark:border-neutral-800">
            <flux:heading size="lg">Курсы не найдены</flux:heading>
            <flux:subheading class="mb-6 mt-2">Попробуйте изменить параметры фильтрации.</flux:subheading>
            @if($statusFilter !== 'all')
                <flux:button wire:click="$set('statusFilter', 'all')" variant="outline" size="sm">Сбросить фильтр</flux:button>
            @endif
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach ($courseProgress as $item)
                @php
                    $isCompleted = $item['percentage'] === 100;
                    $isNotStarted = $item['percentage'] === 0;
                @endphp
                
                <flux:card class="flex flex-col h-full hover:shadow-lg transition-all duration-300 {{ $isCompleted ? 'bg-emerald-50/10 dark:bg-emerald-900/10 border-emerald-200/50 dark:border-emerald-800/50' : '' }}">
                    <div class="flex-1">
                        <div class="flex items-start justify-between mb-2">
                            <flux:heading size="lg" class="{{ $isCompleted ? 'text-emerald-900 dark:text-emerald-100' : '' }}">
                                {{ $item['course']->title }}
                            </flux:heading>
                            @if ($isCompleted)
                                <flux:badge color="success" class="shrink-0 ml-2">Завершен</flux:badge>
                            @elseif ($isNotStarted)
                                <flux:badge class="shrink-0 ml-2">Новый</flux:badge>
                            @endif
                        </div>
                        
                        <p class="text-sm line-clamp-2 mb-6 {{ $isCompleted ? 'text-emerald-700/80 dark:text-emerald-300/80' : 'text-neutral-500' }}">
                            {{ $item['course']->description ?? 'Описание курса временно отсутствует.' }}
                        </p>
                        
                        <div class="mt-4">
                            @if ($isNotStarted)
                                <div class="flex items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400 mt-6">
                                    <flux:icon name="document-text" class="size-4" />
                                    <span>Курс состоит из {{ $item['total_lessons'] }} уроков</span>
                                </div>
                            @else
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="{{ $isCompleted ? 'text-emerald-700 dark:text-emerald-400' : 'text-neutral-600 dark:text-neutral-400' }}">Прогресс</span>
                                    <span class="font-medium {{ $isCompleted ? 'text-emerald-800 dark:text-emerald-300' : '' }}">{{ $item['percentage'] }}%</span>
                                </div>
                                <div class="w-full bg-neutral-200 dark:bg-neutral-700 rounded-full h-2.5 mb-2 overflow-hidden">
                                    <div class="h-2.5 rounded-full transition-all duration-500 {{ $isCompleted ? 'bg-emerald-500 dark:bg-emerald-400' : 'bg-indigo-600 dark:bg-indigo-500' }}" style="width: {{ $item['percentage'] }}%"></div>
                                </div>
                                <p class="text-xs {{ $isCompleted ? 'text-emerald-600 dark:text-emerald-500' : 'text-neutral-500 dark:text-neutral-400' }}">
                                    Пройдено {{ $item['completed_lessons'] }} из {{ $item['total_lessons'] }} уроков
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-8 pt-4 border-t flex justify-between items-center {{ $isCompleted ? 'border-emerald-200 dark:border-emerald-800/50' : 'border-neutral-200 dark:border-neutral-700' }}">
                        @if ($item['next_lesson'])
                            <flux:button wire:navigate href="{{ route('lessons.show', [$item['course'], $item['next_lesson']]) }}" variant="{{ $isNotStarted ? 'primary' : 'primary' }}" size="sm" class="w-full">
                                {{ $isNotStarted ? 'Начать обучение' : 'Продолжить обучение' }}
                            </flux:button>
                        @elseif ($isCompleted)
                            <flux:button wire:navigate href="{{ route('courses.show', $item['course']) }}" variant="outline" size="sm" class="w-full">
                                Посмотреть материалы
                            </flux:button>
                        @else
                            <flux:button wire:navigate href="{{ route('courses.show', $item['course']) }}" variant="primary" size="sm" class="w-full">
                                Перейти к курсу
                            </flux:button>
                        @endif
                    </div>
                </flux:card>
            @endforeach
        </div>
    @endif
</div>
