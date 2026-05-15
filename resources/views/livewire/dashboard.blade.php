<div class="p-6">
    <div class="mb-8">
        <flux:heading size="xl" level="1">Добро пожаловать, {{ $user->name }}!</flux:heading>
        <flux:subheading>Здесь вы можете отслеживать свой прогресс и продолжать обучение.</flux:subheading>
    </div>

    @if ($courseProgress->isEmpty())
        <div class="py-20 text-center">
            <flux:heading size="lg">Пока нет доступных курсов</flux:heading>
            <flux:subheading class="mb-6">Скоро здесь появятся новые учебные материалы.</flux:subheading>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach ($courseProgress as $item)
                <flux:card class="flex flex-col h-full hover:shadow-lg transition-shadow duration-300">
                    <div class="flex-1">
                        <div class="flex items-start justify-between mb-2">
                            <flux:heading size="lg">{{ $item['course']->title }}</flux:heading>
                            @if ($item['percentage'] === 100)
                                <flux:badge color="success" class="shrink-0">Завершен</flux:badge>
                            @endif
                        </div>
                        
                        <p class="text-neutral-500 text-sm line-clamp-2 mb-6">
                            {{ $item['course']->description ?? 'Описание курса временно отсутствует.' }}
                        </p>
                        
                        <div class="mt-4">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-neutral-600 dark:text-neutral-400">Прогресс</span>
                                <span class="font-medium">{{ $item['percentage'] }}%</span>
                            </div>
                            <div class="w-full bg-neutral-200 dark:bg-neutral-700 rounded-full h-2.5 mb-2">
                                <div class="bg-indigo-600 dark:bg-indigo-500 h-2.5 rounded-full" style="width: {{ $item['percentage'] }}%"></div>
                            </div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                Пройдено {{ $item['completed_lessons'] }} из {{ $item['total_lessons'] }} уроков
                            </p>
                        </div>
                    </div>

                    <div class="mt-8 pt-4 border-t border-neutral-200 dark:border-neutral-700 flex justify-between items-center">
                        @if ($item['next_lesson'])
                            <flux:button wire:navigate href="{{ route('lessons.show', [$item['course'], $item['next_lesson']]) }}" variant="primary" size="sm" class="w-full">
                                {{ $item['completed_lessons'] > 0 ? 'Продолжить обучение' : 'Начать обучение' }}
                            </flux:button>
                        @elseif ($item['percentage'] === 100)
                            <flux:button wire:navigate href="{{ route('courses.show', $item['course']) }}" variant="outline" size="sm" class="w-full">
                                Посмотреть курс
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
