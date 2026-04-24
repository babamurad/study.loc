<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl" level="1">Модули</flux:heading>
            @if ($course)
                <flux:subheading class="mt-1">
                    Курс: <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $course->title }}</span>
                    <flux:button variant="ghost" size="sm" href="{{ route('teacher.modules.index') }}" icon="x-mark" class="ml-2">Сбросить</flux:button>
                </flux:subheading>
            @endif
        </div>
        <flux:button href="{{ route('teacher.modules.create', ['course_id' => $course_id]) }}" variant="primary" icon="plus">Создать модуль</flux:button>
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
                        <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Курс</th>
                        <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Позиция</th>
                        <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Уроков</th>
                        <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200 text-right">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($modules as $module)
                        <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:text font="medium" class="text-zinc-900 dark:text-white">{{ $module->title }}</flux:text>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:text variant="subtle">{{ $module->course->title }}</flux:text>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:badge variant="neutral" size="sm">{{ $module->position }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:badge variant="neutral" size="sm" icon="book-open">{{ $module->lessons_count }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex justify-end gap-2">
                                    <flux:button href="{{ route('teacher.modules.edit', $module) }}" icon="pencil-square" size="sm" variant="ghost" inset="top bottom" />
                                    <flux:button wire:click="deleteModule({{ $module->id }})" 
                                                 wire:confirm="Вы уверены, что хотите удалить этот модуль? Все уроки в нем также будут удалены."
                                                 icon="trash" size="sm" variant="ghost" color="red" inset="top bottom" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <flux:text variant="subtle">Модули не найдены</flux:text>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $modules->links() }}
    </div>
</div>
