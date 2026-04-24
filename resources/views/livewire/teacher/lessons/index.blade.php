<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl" level="1">Уроки</flux:heading>
            <flux:subheading>Список всех уроков в системе</flux:subheading>
        </div>
        <flux:button href="{{ route('teacher.lessons.create') }}" variant="primary" icon="plus">Создать урок</flux:button>
    </div>

    @if (session()->has('success'))
        <flux:callout variant="success" class="mb-6">
            {{ session('success') }}
        </flux:callout>
    @endif

    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50/50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Название</th>
                        <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200 hidden sm:table-cell">Курс</th>
                        <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200 hidden lg:table-cell">Модуль</th>
                        <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Поз.</th>
                        <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Статус</th>
                        <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200 text-right">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($lessons as $lesson)
                        <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:text font="medium" class="text-zinc-900 dark:text-white">{{ $lesson->title }}</flux:text>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                <flux:text variant="subtle">{{ $lesson->course?->title ?? '-' }}</flux:text>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                <flux:text variant="subtle">{{ $lesson->module?->title ?? '-' }}</flux:text>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:badge variant="neutral" size="sm">{{ $lesson->position }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($lesson->is_published)
                                    <flux:badge color="green" size="sm" variant="pill">Опубликован</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm" variant="pill">Черновик</flux:badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex justify-end gap-2">
                                    <flux:button href="{{ route('teacher.lessons.edit', [$lesson, 'page' => $lessons->currentPage()]) }}" icon="pencil-square" size="sm" variant="ghost" inset="top bottom" />
                                    <flux:button wire:click="deleteLesson({{ $lesson->id }})" 
                                                 wire:confirm="Вы уверены, что хотите удалить этот урок?"
                                                 icon="trash" size="sm" variant="ghost" color="red" inset="top bottom" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <flux:text variant="subtle">Уроки не найдены</flux:text>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $lessons->links() }}
    </div>
</div>
