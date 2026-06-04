<div class="p-6 max-w-7xl mx-auto w-full">
    <div class="mb-6">
        <flux:button href="{{ route('quizzes.index') }}" variant="ghost" icon="chevron-left" class="mb-2">Все тесты</flux:button>
        <flux:heading size="xl" level="1">{{ $quiz->title }}</flux:heading>
        @if($quiz->description)
            <p class="text-zinc-600 dark:text-zinc-400 mt-2">{{ $quiz->description }}</p>
        @endif
    </div>

    <flux:card class="p-8">
        @if ($quizResult)
            <div class="text-center py-8">
                @if ($quizResult['passed'])
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <flux:icon name="check" class="w-8 h-8" />
                    </div>
                    <flux:heading size="xl" class="mb-2 text-green-600 dark:text-green-400">Тест успешно пройден!</flux:heading>
                    <p class="text-lg mb-6">Ваш результат: <span class="font-bold">{{ $quizResult['score'] }}%</span></p>
                @else
                    <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <flux:icon name="x-mark" class="w-8 h-8" />
                    </div>
                    <flux:heading size="xl" class="mb-2 text-red-600 dark:text-red-400">Тест не пройден</flux:heading>
                    <p class="text-lg mb-6">Ваш результат: <span class="font-bold">{{ $quizResult['score'] }}%</span>. Для сдачи нужно {{ $quiz->pass_threshold }}%.</p>
                @endif

                <div class="flex justify-center gap-4">
                    <flux:button wire:click="retakeQuiz" variant="outline">Пройти еще раз</flux:button>
                    <flux:button href="{{ route('quizzes.index') }}" variant="primary">Вернуться к списку</flux:button>
                </div>
            </div>

        @elseif ($quizInProgress)
            <div>
                @if ($questions->count() > 0)
                    @php $currentQuestion = $questions[$currentQuestionIndex]; @endphp
                    
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-4 text-sm text-zinc-500">
                            <span>Вопрос {{ $currentQuestionIndex + 1 }} из {{ $questions->count() }}</span>
                            <span>{{ round((($currentQuestionIndex) / $questions->count()) * 100) }}% завершено</span>
                        </div>
                        
                        <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2 mb-8">
                            <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: {{ (($currentQuestionIndex) / $questions->count()) * 100 }}%"></div>
                        </div>

                        <flux:heading size="lg" class="mb-6">{{ $currentQuestion->question }}</flux:heading>
                        
                        <div class="space-y-4">
                            <flux:radio.group wire:model.live="userAnswers.{{ $currentQuestion->id }}" class="flex flex-col gap-4">
                                @foreach ($currentQuestion->answers as $answer)
                                    <flux:radio value="{{ $answer->id }}" label="{{ $answer->answer }}" />
                                @endforeach
                            </flux:radio.group>
                        </div>
                    </div>

                    <div class="flex justify-between pt-6 border-t border-zinc-100 dark:border-zinc-800">
                        <flux:button wire:click="previousQuestion" :disabled="$currentQuestionIndex === 0" variant="ghost">
                            Назад
                        </flux:button>
                        
                        @if ($currentQuestionIndex < $questions->count() - 1)
                            <flux:button wire:click="nextQuestion" variant="primary">
                                Далее
                            </flux:button>
                        @else
                            <flux:button wire:click="submitQuiz" variant="primary" class="font-bold shadow-md hover:shadow-lg transition-shadow">
                                Завершить тест
                            </flux:button>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8">
                        <flux:text variant="subtle">В этом тесте пока нет вопросов.</flux:text>
                    </div>
                @endif
            </div>

        @else
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center mx-auto mb-6">
                    <flux:icon name="document-text" class="w-10 h-10" />
                </div>
                
                <flux:heading size="xl" class="mb-4">Готовы начать?</flux:heading>
                
                <div class="flex justify-center gap-8 mb-8 text-zinc-600 dark:text-zinc-400">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $questions->count() }}</div>
                        <div class="text-sm">Вопросов</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $quiz->pass_threshold }}%</div>
                        <div class="text-sm">Проходной балл</div>
                    </div>
                </div>

                <flux:button wire:click="startQuiz" variant="primary" class="w-full max-w-xs shadow-lg hover:shadow-xl transition-shadow py-3">
                    Начать тест
                </flux:button>
            </div>
        @endif
    </flux:card>
</div>
