<div class="p-6 max-w-5xl mx-auto w-full pb-24">
    <!-- Header Section -->
    <div class="mb-10 text-center relative z-10">
        <flux:button href="{{ route('quizzes.index') }}" variant="ghost" icon="chevron-left" class="absolute left-0 top-1/2 -translate-y-1/2 hidden md:inline-flex opacity-70 hover:opacity-100">
            Все тесты
        </flux:button>
        <h1 class="text-4xl md:text-5xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-600 dark:from-indigo-400 dark:via-purple-400 dark:to-indigo-400 mb-4 drop-shadow-sm">
            {{ $quiz->title }}
        </h1>
        @if($quiz->description)
            <p class="text-lg text-zinc-600 dark:text-zinc-400 max-w-2xl mx-auto leading-relaxed">
                {{ $quiz->description }}
            </p>
        @endif
    </div>

    <!-- Main Card -->
    <div class="relative bg-white dark:bg-zinc-900/80 rounded-[2rem] shadow-2xl border border-zinc-200/60 dark:border-zinc-800/60 backdrop-blur-xl overflow-hidden">
        
        <!-- Decorative Glows -->
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 opacity-80"></div>
        <div class="absolute -top-32 -right-32 w-64 h-64 bg-indigo-500/10 dark:bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -left-32 w-64 h-64 bg-purple-500/10 dark:bg-purple-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative p-8 sm:p-12 z-10">
            @if ($quizResult)
                <div class="text-center py-12 animate-in fade-in slide-in-from-bottom-8 duration-700">
                    @if ($quizResult['passed'])
                        <div class="relative inline-flex items-center justify-center w-24 h-24 mb-8">
                            <div class="absolute inset-0 bg-green-500/20 rounded-full blur-xl animate-pulse"></div>
                            <div class="relative bg-gradient-to-br from-green-400 to-emerald-600 text-white rounded-full flex items-center justify-center w-24 h-24 shadow-xl shadow-green-500/30">
                                <flux:icon name="check" class="w-12 h-12" />
                            </div>
                        </div>
                        <h2 class="text-4xl font-black mb-4 text-zinc-900 dark:text-white">Поздравляем! 🎉</h2>
                        <p class="text-xl text-zinc-600 dark:text-zinc-400 mb-10">
                            Тест успешно пройден с результатом <span class="font-bold text-green-600 dark:text-green-400">{{ $quizResult['score'] }}%</span>
                        </p>
                    @else
                        <div class="relative inline-flex items-center justify-center w-24 h-24 mb-8">
                            <div class="absolute inset-0 bg-red-500/20 rounded-full blur-xl animate-pulse"></div>
                            <div class="relative bg-gradient-to-br from-red-500 to-rose-600 text-white rounded-full flex items-center justify-center w-24 h-24 shadow-xl shadow-red-500/30">
                                <flux:icon name="x-mark" class="w-12 h-12" />
                            </div>
                        </div>
                        <h2 class="text-4xl font-black mb-4 text-zinc-900 dark:text-white">Не сдавайтесь! 💪</h2>
                        <p class="text-xl text-zinc-600 dark:text-zinc-400 mb-10">
                            Ваш результат <span class="font-bold text-red-600 dark:text-red-400">{{ $quizResult['score'] }}%</span>. Для сдачи нужно {{ $quiz->pass_threshold }}%.
                        </p>
                    @endif

                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <button wire:click="retakeQuiz" class="px-8 py-4 rounded-xl font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-800 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-all hover:-translate-y-1">
                            Пройти еще раз
                        </button>
                        <a href="{{ route('quizzes.index') }}" class="px-8 py-4 rounded-xl font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-1 text-center">
                            Вернуться к каталогу
                        </a>
                    </div>
                </div>

            @elseif ($quizInProgress)
                <div class="animate-in fade-in duration-500">
                    @if ($questions->count() > 0)
                        @php 
                            $currentQuestion = $questions[$currentQuestionIndex]; 
                            $progress = (($currentQuestionIndex) / $questions->count()) * 100;
                        @endphp
                        
                        <!-- Progress Bar -->
                        <div class="mb-10">
                            <div class="flex justify-between items-end mb-3">
                                <span class="text-sm font-bold text-zinc-500 dark:text-zinc-400 tracking-wider uppercase">Вопрос {{ $currentQuestionIndex + 1 }} из {{ $questions->count() }}</span>
                                <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400">{{ round($progress) }}% завершено</span>
                            </div>
                            <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-3 p-0.5 overflow-hidden border border-zinc-200/50 dark:border-zinc-700/50">
                                <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-indigo-500 h-full rounded-full transition-all duration-700 ease-out relative" style="width: {{ $progress }}%">
                                    <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Question text -->
                        <h2 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white mb-8 leading-tight">
                            {{ $currentQuestion->question }}
                        </h2>
                        
                        <!-- Answers (Custom Premium Radio Cards) -->
                        <div class="space-y-4 mb-12">
                            @foreach ($currentQuestion->answers as $answer)
                                @php
                                    $isSelected = isset($userAnswers[$currentQuestion->id]) && $userAnswers[$currentQuestion->id] == $answer->id;
                                @endphp
                                <label class="group relative flex items-center p-5 sm:p-6 rounded-2xl cursor-pointer transition-all duration-300 {{ $isSelected ? 'bg-indigo-50/80 dark:bg-indigo-900/30 border-indigo-500 shadow-md shadow-indigo-500/10' : 'bg-white dark:bg-zinc-800/40 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 hover:border-indigo-300 dark:hover:border-indigo-700 hover:shadow-lg' }} border-2">
                                    
                                    <div class="flex-shrink-0 mr-4">
                                        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-colors {{ $isSelected ? 'border-indigo-600 dark:border-indigo-400' : 'border-zinc-400 dark:border-zinc-500 group-hover:border-indigo-400' }}">
                                            <div class="w-3 h-3 rounded-full bg-indigo-600 dark:bg-indigo-400 transition-transform duration-300 {{ $isSelected ? 'scale-100' : 'scale-0' }}"></div>
                                        </div>
                                    </div>
                                    
                                    <input type="radio" 
                                           name="answer-{{ $currentQuestion->id }}" 
                                           value="{{ $answer->id }}" 
                                           wire:model.live="userAnswers.{{ $currentQuestion->id }}"
                                           class="hidden">
                                           
                                    <span class="text-lg font-medium transition-colors {{ $isSelected ? 'text-indigo-900 dark:text-indigo-100' : 'text-zinc-700 dark:text-zinc-300 group-hover:text-zinc-900 dark:group-hover:text-white' }}">
                                        {{ $answer->answer }}
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        <!-- Footer Navigation -->
                        <div class="flex justify-between items-center pt-6 border-t border-zinc-200 dark:border-zinc-800">
                            <button wire:click="previousQuestion" @if($currentQuestionIndex === 0) disabled @endif class="px-6 py-3 rounded-xl font-bold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                Назад
                            </button>
                            
                            @if ($currentQuestionIndex < $questions->count() - 1)
                                <button wire:click="nextQuestion" class="px-8 py-3 rounded-xl font-bold text-white bg-zinc-900 dark:bg-white dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-zinc-100 transition-all hover:-translate-y-1 shadow-lg shadow-zinc-900/20 dark:shadow-white/20">
                                    Далее
                                </button>
                            @else
                                <button wire:click="submitQuiz" class="px-8 py-3 rounded-xl font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-1">
                                    Завершить тест
                                </button>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-16">
                            <div class="w-20 h-20 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-6 text-zinc-400">
                                <flux:icon name="inbox" class="w-10 h-10" />
                            </div>
                            <h3 class="text-2xl font-bold text-zinc-900 dark:text-white mb-2">Вопросов нет</h3>
                            <p class="text-zinc-500 dark:text-zinc-400">В этом тесте пока не добавлены вопросы.</p>
                        </div>
                    @endif
                </div>

            @else
                <div class="text-center py-16 px-4 animate-in fade-in zoom-in-95 duration-500">
                    <div class="relative inline-block mb-8">
                        <div class="absolute inset-0 bg-indigo-500/20 rounded-full blur-2xl animate-pulse"></div>
                        <div class="relative w-28 h-28 bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/50 dark:to-purple-900/50 rounded-full flex items-center justify-center shadow-xl border border-indigo-200 dark:border-indigo-800">
                            <flux:icon name="academic-cap" class="w-14 h-14 text-indigo-600 dark:text-indigo-400" />
                        </div>
                    </div>
                    
                    <h2 class="text-3xl sm:text-4xl font-black mb-6 text-zinc-900 dark:text-white">Готовы бросить себе вызов?</h2>
                    
                    <div class="flex flex-wrap justify-center gap-6 sm:gap-12 mb-12">
                        <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl p-6 min-w-[140px] shadow-sm border border-zinc-100 dark:border-zinc-800">
                            <div class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-br from-indigo-500 to-purple-600 mb-2">{{ $questions->count() }}</div>
                            <div class="text-sm font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Вопросов</div>
                        </div>
                        <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl p-6 min-w-[140px] shadow-sm border border-zinc-100 dark:border-zinc-800">
                            <div class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-br from-indigo-500 to-purple-600 mb-2">{{ $quiz->pass_threshold }}%</div>
                            <div class="text-sm font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Проходной<br>балл</div>
                        </div>
                    </div>

                    <button wire:click="startQuiz" class="group relative inline-flex items-center justify-center px-10 py-5 font-bold text-white transition-all duration-300 bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-600 rounded-2xl hover:scale-105 hover:shadow-2xl hover:shadow-indigo-500/40 bg-[length:200%_auto] hover:bg-[right_center]">
                        <span class="text-xl">Начать тестирование</span>
                        <flux:icon name="arrow-right" class="w-6 h-6 ml-3 transition-transform group-hover:translate-x-1" />
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
