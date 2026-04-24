<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <flux:heading size="xl" level="1">Курсы</flux:heading>
        <flux:button href="{{ route('teacher.courses.create') }}" variant="primary" icon="plus">Создать курс</flux:button>
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
                        <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Слизняк (Slug)</th>
                        <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Модулей</th>
                        <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Статус</th>
                        <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200 text-right">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($courses as $course)
                        <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:text font="medium" class="text-zinc-900 dark:text-white">{{ $course->title }}</flux:text>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:text variant="subtle">{{ $course->slug }}</flux:text>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:button href="{{ route('teacher.modules.index', ['course_id' => $course->id]) }}" variant="ghost" size="sm" inset="top bottom">
                                    <flux:badge variant="neutral" size="sm" icon="square-3-stack-3d">{{ $course->modules_count }}</flux:badge>
                                </flux:button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($course->is_published)
                                    <flux:badge color="green" size="sm" variant="pill">Опубликован</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm" variant="pill">Черновик</flux:badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex justify-end gap-2">
                                    <flux:button href="{{ route('teacher.courses.edit', $course) }}" icon="pencil-square" size="sm" variant="ghost" inset="top bottom" />
                                    <flux:button wire:click="deleteCourse({{ $course->id }})" 
                                                 wire:confirm="Вы уверены, что хотите удалить этот курс? Все модули и уроки в нем также будут удалены."
                                                 icon="trash" size="sm" variant="ghost" color="red" inset="top bottom" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <flux:text variant="subtle">Курсы не найдены</flux:text>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $courses->links() }}
    </div>
</div>
