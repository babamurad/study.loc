<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl" level="1">Тесты (Quizzes)</flux:heading>
            <flux:subheading>Список всех тестов для проверки знаний</flux:subheading>
        </div>
        <flux:button href="{{ route('teacher.quizzes.create') }}" variant="primary" icon="plus">Создать тест</flux:button>
    </div>

    @if (session()->has('success'))
        <flux:callout variant="success" class="mb-6">
            {{ session('success') }}
        </flux:callout>
    @endif

    <div class="flex flex-wrap items-center gap-3 mb-6">
        <flux:input wire:model.live="search" placeholder="Поиск по названию..." class="w-64" />

        @if ($search)
            <flux:button wire:click="$set('search', '')" variant="ghost" size="sm">
                Сбросить
            </flux:button>
        @endif
    </div>

    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 rounded-xl overflow-hidden shadow-sm mb-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50/50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Название</th>
                        <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Описание</th>
                        <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Вопросов</th>
                        <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Проходной балл</th>
                        <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200 text-right">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($quizzes as $quiz)
                        <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('teacher.quizzes.edit', $quiz) }}" class="text-zinc-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors">
                                    {{ $quiz->title }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-500 dark:text-zinc-400">
                                {{ Str::limit($quiz->description, 50) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:badge variant="neutral" size="sm">{{ $quiz->questions_count }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:text variant="subtle">{{ $quiz->pass_threshold }}%</flux:text>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex justify-end gap-2">
                                    <flux:button href="{{ route('teacher.quizzes.edit', $quiz) }}" icon="pencil-square" size="sm" variant="ghost" inset="top bottom" />
                                    <flux:button wire:click="deleteQuiz({{ $quiz->id }})" 
                                                 wire:confirm="Вы уверены, что хотите удалить этот тест?"
                                                 icon="trash" size="sm" variant="ghost" color="red" inset="top bottom" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <flux:text variant="subtle">Тесты не найдены</flux:text>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <flux:text variant="subtle">Показывать по:</flux:text>
            <flux:select wire:model.live="perPage" class="w-20">
                <flux:select.option value="5">5</flux:select.option>
                <flux:select.option value="10">10</flux:select.option>
                <flux:select.option value="25">25</flux:select.option>
                <flux:select.option value="50">50</flux:select.option>
            </flux:select>
        </div>
        <flux:text variant="subtle">Всего: {{ $quizzes->total() }} тестов</flux:text>
    </div>

    <div>
        {{ $quizzes->links() }}
    </div>
</div>
