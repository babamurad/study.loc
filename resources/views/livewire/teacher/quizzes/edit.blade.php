<div class="p-6 max-w-7xl mx-auto">
    <div class="mb-6">
        <flux:button href="{{ route('teacher.quizzes.index') }}" variant="ghost" icon="chevron-left" class="mb-2">Назад к списку</flux:button>
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl" level="1">Редактирование теста</flux:heading>
                <flux:subheading>Настройте вопросы и варианты ответов</flux:subheading>
            </div>
            <flux:button wire:click="save" variant="primary">Сохранить все изменения</flux:button>
        </div>
    </div>

    @if (session()->has('success'))
        <flux:callout variant="success" class="mb-6">
            {{ session('success') }}
        </flux:callout>
    @endif

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Настройки теста -->
        <div class="w-full lg:w-1/3 space-y-6 flex-shrink-0">
            <flux:card>
                <flux:heading size="lg" class="mb-4">Основные настройки</flux:heading>
                <div class="space-y-4">
                    <flux:input wire:model="title" label="Название теста" />
                    <flux:textarea wire:model="description" label="Описание" rows="3" />
                    <flux:input type="number" wire:model="pass_threshold" label="Проходной балл (%)" min="0" max="100" />
                </div>
            </flux:card>
            
            <flux:card>
                <flux:heading size="lg" class="mb-2">Связанные уроки</flux:heading>
                <div class="space-y-2">
                    @forelse($quiz->lessons as $lesson)
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            <a href="{{ route('teacher.lessons.edit', $lesson) }}" class="text-blue-600 hover:underline flex items-center gap-1">
                                <flux:icon name="link" class="size-3" />
                                {{ $lesson->title }}
                            </a>
                        </div>
                    @empty
                        <flux:text variant="subtle" size="sm">Этот тест пока не привязан ни к одному уроку.</flux:text>
                    @endforelse
                </div>
            </flux:card>
        </div>

        <!-- Вопросы -->
        <div class="w-full lg:w-2/3 space-y-6">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">Вопросы теста ({{ count($questions) }})</flux:heading>
                <flux:button wire:click="addQuestion" variant="outline" size="sm" icon="plus">Добавить вопрос</flux:button>
            </div>

            @forelse ($questions as $qIndex => $question)
                <flux:card>
                    <div class="flex justify-between items-center mb-4">
                        <flux:heading size="md">Вопрос {{ $qIndex + 1 }}</flux:heading>
                        <flux:button wire:click="removeQuestion({{ $qIndex }})" variant="danger" size="sm" icon="trash" />
                    </div>
                    
                    <div class="mb-4">
                        <flux:textarea wire:model="questions.{{ $qIndex }}.question" placeholder="Введите текст вопроса..." rows="2" />
                    </div>

                    <div class="space-y-3 bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg border border-gray-100 dark:border-gray-700">
                        <div class="flex justify-between items-center mb-2">
                            <flux:heading size="sm">Варианты ответов</flux:heading>
                            <flux:button wire:click="addAnswer({{ $qIndex }})" variant="ghost" size="xs" icon="plus">Добавить вариант</flux:button>
                        </div>

                        @foreach ($question['answers'] as $aIndex => $answer)
                            <div class="flex items-center gap-3">
                                <div class="pt-2">
                                    <input type="radio" 
                                           name="correct_answer_{{ $qIndex }}" 
                                           wire:click="setCorrectAnswer({{ $qIndex }}, {{ $aIndex }})"
                                           {{ $answer['is_correct'] ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                           title="Отметить как правильный">
                                </div>
                                <div class="flex-1">
                                    <flux:input wire:model="questions.{{ $qIndex }}.answers.{{ $aIndex }}.answer" placeholder="Вариант ответа" />
                                </div>
                                <div>
                                    <flux:button wire:click="removeAnswer({{ $qIndex }}, {{ $aIndex }})" variant="danger" size="sm" icon="x-mark" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                </flux:card>
            @empty
                <div class="text-center py-12 bg-gray-50 dark:bg-gray-800/30 rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
                    <flux:text variant="subtle" class="mb-4">В этом тесте пока нет вопросов.</flux:text>
                    <flux:button wire:click="addQuestion" variant="primary" icon="plus">Добавить первый вопрос</flux:button>
                </div>
            @endforelse

            @if(count($questions) > 0)
                <div class="pt-4 flex justify-center">
                    <flux:button wire:click="addQuestion" variant="outline" icon="plus" class="w-full md:w-auto">
                        Добавить еще один вопрос
                    </flux:button>
                </div>
            @endif
        </div>
    </div>
</div>
