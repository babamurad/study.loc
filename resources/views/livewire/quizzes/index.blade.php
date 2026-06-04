<div class="p-6 max-w-7xl mx-auto pb-24 relative">
    <!-- Decorative background blobs -->
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-indigo-500/10 dark:bg-indigo-500/20 rounded-full blur-3xl pointer-events-none -translate-y-1/2"></div>
    <div class="absolute top-40 right-1/4 w-96 h-96 bg-purple-500/10 dark:bg-purple-500/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="mb-12 relative z-10 text-center">
        <h1 class="text-4xl md:text-5xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-600 dark:from-indigo-400 dark:via-purple-400 dark:to-indigo-400 mb-4 drop-shadow-sm">
            Каталог тестов
        </h1>
        <p class="text-lg text-zinc-600 dark:text-zinc-400 max-w-2xl mx-auto">
            Проверьте свои знания, бросьте себе вызов и поднимите свои навыки на новый уровень
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 relative z-10">
        @forelse ($quizzes as $quiz)
            @php
                $quizAttempts = $attempts->get($quiz->id, collect());
                $isPassed = $quizAttempts->where('passed', true)->isNotEmpty();
                $bestScore = $quizAttempts->max('score');
            @endphp
            
            <div class="group relative flex flex-col h-full bg-white dark:bg-zinc-900/80 rounded-3xl shadow-xl hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-300 hover:-translate-y-2 border border-zinc-200/60 dark:border-zinc-800/60 backdrop-blur-xl overflow-hidden">
                <!-- Hover Glow -->
                <div class="absolute inset-0 bg-gradient-to-b from-indigo-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                <div class="p-8 flex flex-col flex-1 relative">
                    <div class="flex justify-between items-start mb-6 gap-4">
                        <h2 class="text-2xl font-bold text-zinc-900 dark:text-white leading-tight group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            {{ $quiz->title }}
                        </h2>
                        <div class="flex-shrink-0 mt-1">
                            @if($isPassed)
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400 border border-green-200 dark:border-green-800/50 shadow-sm">Пройден</span>
                            @elseif($quizAttempts->isNotEmpty())
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700 dark:bg-orange-900/50 dark:text-orange-400 border border-orange-200 dark:border-orange-800/50 shadow-sm">Не сдан</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800/50 shadow-sm">Новый</span>
                            @endif
                        </div>
                    </div>
                    
                    <p class="text-zinc-600 dark:text-zinc-400 mb-8 line-clamp-3 text-sm leading-relaxed flex-1">
                        {{ $quiz->description ?? 'Описание отсутствует. Начните тест, чтобы узнать подробности.' }}
                    </p>
                    
                    <div class="space-y-3 mb-8 bg-zinc-50 dark:bg-zinc-800/50 p-4 rounded-2xl border border-zinc-100 dark:border-zinc-700/50">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-zinc-500 dark:text-zinc-400 flex items-center gap-2">
                                <flux:icon name="document-text" class="w-4 h-4" /> Вопросов
                            </span>
                            <span class="font-black text-zinc-900 dark:text-white">{{ $quiz->questions_count }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-zinc-500 dark:text-zinc-400 flex items-center gap-2">
                                <flux:icon name="check-badge" class="w-4 h-4" /> Проходной балл
                            </span>
                            <span class="font-black text-zinc-900 dark:text-white">{{ $quiz->pass_threshold }}%</span>
                        </div>
                        @if($quizAttempts->isNotEmpty())
                            <div class="flex justify-between items-center text-sm pt-3 mt-3 border-t border-zinc-200 dark:border-zinc-700/50">
                                <span class="text-zinc-500 dark:text-zinc-400 flex items-center gap-2">
                                    <flux:icon name="trophy" class="w-4 h-4" /> Лучший результат
                                </span>
                                <span class="font-black {{ $isPassed ? 'text-green-600 dark:text-green-400' : 'text-orange-600 dark:text-orange-400' }}">{{ $bestScore }}%</span>
                            </div>
                        @endif
                    </div>

                    <a href="{{ route('quizzes.show', $quiz) }}" class="w-full inline-flex justify-center items-center px-6 py-3.5 rounded-xl font-bold transition-all duration-300 {{ $isPassed ? 'border-2 border-indigo-600 text-indigo-700 hover:bg-indigo-50 dark:border-indigo-500 dark:text-indigo-400 dark:hover:bg-indigo-500/20 shadow-sm' : 'bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white shadow-lg shadow-indigo-500/30' }}">
                        {{ $isPassed ? 'Пройти заново' : ($quizAttempts->isNotEmpty() ? 'Попробовать еще раз' : 'Начать тестирование') }}
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-20 bg-white/50 dark:bg-zinc-900/50 backdrop-blur-sm rounded-3xl border border-dashed border-zinc-300 dark:border-zinc-700">
                <div class="w-24 h-24 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-6 text-zinc-400">
                    <flux:icon name="inbox" class="w-12 h-12" />
                </div>
                <h3 class="text-2xl font-bold text-zinc-900 dark:text-white mb-2">Каталог пуст</h3>
                <p class="text-zinc-500 dark:text-zinc-400">В данный момент нет доступных тестов.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-12 relative z-10">
        {{ $quizzes->links() }}
    </div>
</div>
