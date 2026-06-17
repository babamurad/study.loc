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

    <div class="flex flex-wrap items-center gap-3 mb-6">
        <flux:input wire:model.live="search" placeholder="Поиск..." class="w-48" />

        <flux:select wire:model.live="course_id" placeholder="Все курсы" class="w-40">
            <flux:select.option value="">Все курсы</flux:select.option>
            @foreach ($courses as $course)
                <flux:select.option value="{{ $course->id }}">{{ $course->title }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="module_id" :disabled="!$course_id" placeholder="Все модули" class="w-40">
            <flux:select.option value="">Все модули</flux:select.option>
            @foreach ($modules as $module)
                <flux:select.option value="{{ $module->id }}">{{ $module->title }}</flux:select.option>
            @endforeach
        </flux:select>

        @if ($course_id || $module_id || $search)
            <flux:button wire:click="$set('course_id', null); $set('module_id', null); $set('search', '')" variant="ghost" size="sm">
                Сбросить
            </flux:button>
        @endif
    </div>

    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50/50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th wire:click="sortBy('title')" class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200 cursor-pointer select-none group/th hover:bg-black/5 dark:hover:bg-white/5 hover:text-zinc-900 dark:hover:text-white transition-colors">
                            <div class="flex items-center gap-1">
                                Название
                                @if($sortField === 'title')
                                    <flux:icon name="chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" class="size-4" />
                                @else
                                    <flux:icon name="chevron-up-down" class="size-4 opacity-0 group-hover/th:opacity-50 transition-opacity" />
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('course_id')" class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200 hidden sm:table-cell cursor-pointer select-none group/th hover:bg-black/5 dark:hover:bg-white/5 hover:text-zinc-900 dark:hover:text-white transition-colors">
                            <div class="flex items-center gap-1">
                                Курс
                                @if($sortField === 'course_id')
                                    <flux:icon name="chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" class="size-4" />
                                @else
                                    <flux:icon name="chevron-up-down" class="size-4 opacity-0 group-hover/th:opacity-50 transition-opacity" />
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('module_id')" class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200 hidden lg:table-cell cursor-pointer select-none group/th hover:bg-black/5 dark:hover:bg-white/5 hover:text-zinc-900 dark:hover:text-white transition-colors">
                            <div class="flex items-center gap-1">
                                Модуль
                                @if($sortField === 'module_id')
                                    <flux:icon name="chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" class="size-4" />
                                @else
                                    <flux:icon name="chevron-up-down" class="size-4 opacity-0 group-hover/th:opacity-50 transition-opacity" />
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('position')" class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200 cursor-pointer select-none group/th hover:bg-black/5 dark:hover:bg-white/5 hover:text-zinc-900 dark:hover:text-white transition-colors">
                            <div class="flex items-center gap-1">
                                Поз.
                                @if($sortField === 'position')
                                    <flux:icon name="chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" class="size-4" />
                                @else
                                    <flux:icon name="chevron-up-down" class="size-4 opacity-0 group-hover/th:opacity-50 transition-opacity" />
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('is_published')" class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200 cursor-pointer select-none group/th hover:bg-black/5 dark:hover:bg-white/5 hover:text-zinc-900 dark:hover:text-white transition-colors">
                            <div class="flex items-center gap-1">
                                Статус
                                @if($sortField === 'is_published')
                                    <flux:icon name="chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" class="size-4" />
                                @else
                                    <flux:icon name="chevron-up-down" class="size-4 opacity-0 group-hover/th:opacity-50 transition-opacity" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200 text-right">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($lessons as $lesson)
                        <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('teacher.lessons.edit', [$lesson, 'page' => $lessons->currentPage()]) }}" class="text-zinc-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors">
                                    {{ $lesson->title }}
                                </a>
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
        <flux:text variant="subtle">Всего: {{ $lessons->total() }} уроков</flux:text>
    </div>

    <div>
        {{ $lessons->links() }}
    </div>
</div>
