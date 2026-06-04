<div class="p-6 max-w-7xl mx-auto">
    <div class="mb-8">
        <flux:heading size="xl" level="1">Каталог тестов</flux:heading>
        <flux:subheading>Проверьте свои знания, проходя тесты по различным темам</flux:subheading>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($quizzes as $quiz)
            @php
                $quizAttempts = $attempts->get($quiz->id, collect());
                $isPassed = $quizAttempts->where('passed', true)->isNotEmpty();
                $bestScore = $quizAttempts->max('score');
            @endphp
            
            <flux:card class="flex flex-col h-full hover:shadow-md transition-shadow">
                <div class="flex-1">
                    <div class="flex justify-between items-start mb-4">
                        <flux:heading size="lg">{{ $quiz->title }}</flux:heading>
                        @if($isPassed)
                            <flux:badge color="green" variant="pill">Пройден</flux:badge>
                        @elseif($quizAttempts->isNotEmpty())
                            <flux:badge color="orange" variant="pill">Не пройден</flux:badge>
                        @else
                            <flux:badge color="zinc" variant="pill">Новый</flux:badge>
                        @endif
                    </div>
                    
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-6 line-clamp-3">
                        {{ $quiz->description ?? 'Описание отсутствует' }}
                    </p>
                    
                    <div class="space-y-2 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-zinc-500">Вопросов:</span>
                            <span class="font-medium">{{ $quiz->questions_count }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-zinc-500">Проходной балл:</span>
                            <span class="font-medium">{{ $quiz->pass_threshold }}%</span>
                        </div>
                        @if($quizAttempts->isNotEmpty())
                            <div class="flex justify-between text-sm">
                                <span class="text-zinc-500">Лучший результат:</span>
                                <span class="font-medium {{ $isPassed ? 'text-green-600' : 'text-orange-600' }}">{{ $bestScore }}%</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-auto pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:button href="{{ route('quizzes.show', $quiz) }}" variant="primary" class="w-full">
                        {{ $isPassed ? 'Пройти заново' : ($quizAttempts->isNotEmpty() ? 'Попробовать еще раз' : 'Начать тест') }}
                    </flux:button>
                </div>
            </flux:card>
        @empty
            <div class="col-span-full text-center py-12 bg-gray-50 dark:bg-gray-800/30 rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
                <flux:text variant="subtle" class="mb-4">Доступных тестов пока нет.</flux:text>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $quizzes->links() }}
    </div>
</div>
