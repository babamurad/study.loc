<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl" level="1">Прогресс учеников</flux:heading>
            <flux:subheading>Отслеживание успеваемости студентов по курсам</flux:subheading>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-3 mb-6">
        <flux:input wire:model.live="search" placeholder="Поиск ученика..." class="w-64" icon="magnifying-glass" />
    </div>

    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 rounded-xl overflow-hidden shadow-sm mb-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50/50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Имя / Email</th>
                        <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Статус</th>
                        <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Общий прогресс</th>
                        <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200 text-right">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800" x-data="{ activeStudent: null }">
                    @forelse ($students as $student)
                        @php
                            $totalLessons = $courses->sum('lessons_count');
                            $studentCompleted = $student->completedLessons->count();
                            $totalPercent = $totalLessons > 0 ? round(($studentCompleted / $totalLessons) * 100) : 0;
                        @endphp
                        <tr wire:key="student-{{ $student->id }}" class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors {{ ! $student->is_active ? 'opacity-60 bg-zinc-50/30 dark:bg-zinc-900/30' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <flux:avatar :name="$student->name" :initials="$student->initials()" size="sm" />
                                    <div>
                                        <div class="text-zinc-900 dark:text-white font-medium">{{ $student->name }}</div>
                                        <flux:text variant="subtle" size="sm" class="dark:text-zinc-400">{{ $student->email }}</flux:text>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:switch 
                                    wire:click="toggleActive({{ $student->id }})" 
                                    :checked="(bool) $student->is_active" 
                                />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2 w-48">
                                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $totalPercent }}%"></div>
                                    </div>
                                    <flux:text variant="subtle" size="sm" class="dark:text-zinc-400">{{ $totalPercent }}%</flux:text>
                                </div>
                                <flux:text variant="subtle" size="xs" class="dark:text-zinc-500">{{ $studentCompleted }} из {{ $totalLessons }} уроков</flux:text>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <flux:dropdown>
                                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="right" />
                                        <flux:menu>
                                            <flux:menu.item wire:navigate href="{{ route('teacher.students.dashboard', $student) }}" icon="eye">Дашборд ученика</flux:menu.item>
                                            <flux:menu.item wire:navigate href="{{ route('teacher.students.progress', $student) }}" icon="chart-bar">История тестов и практик</flux:menu.item>
                                            <flux:menu.separator />
                                            <flux:menu.item 
                                                wire:click="deleteStudent({{ $student->id }})" 
                                                wire:confirm="Вы уверены, что хотите удалить этого ученика? Это действие необратимо и удалит весь его прогресс."
                                                variant="danger" 
                                                icon="trash"
                                            >
                                                Удалить ученика
                                            </flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                    <flux:button @click="activeStudent === {{ $student->id }} ? activeStudent = null : activeStudent = {{ $student->id }}" 
                                                 variant="ghost" 
                                                 size="sm"
                                                 icon="chevron-down">
                                        Курсы
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                        <tr wire:key="student-courses-{{ $student->id }}" x-show="activeStudent === {{ $student->id }}" x-cloak class="bg-zinc-50/50 dark:bg-zinc-800/20">
                            <td colspan="4" class="px-6 py-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-2">
                                    @foreach ($courses as $course)
                                        @php
                                            $courseCompleted = $student->completedLessons->where('course_id', $course->id)->count();
                                            $coursePercent = $course->lessons_count > 0 ? round(($courseCompleted / $course->lessons_count) * 100) : 0;
                                        @endphp
                                        <flux:card class="p-4 flex flex-col h-full">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <flux:heading size="sm">{{ $course->title }}</flux:heading>
                                                    <flux:text variant="subtle" size="xs" class="dark:text-zinc-400">{{ $courseCompleted }} / {{ $course->lessons_count }} уроков</flux:text>
                                                </div>
                                                <flux:badge size="sm" color="{{ $coursePercent === 100 ? 'green' : ($coursePercent > 0 ? 'blue' : 'zinc') }}">
                                                    {{ $coursePercent }}%
                                                </flux:badge>
                                            </div>
                                            <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1.5 mt-2 mb-4">
                                                <div class="bg-{{ $coursePercent === 100 ? 'green' : 'blue' }}-600 h-1.5 rounded-full" style="width: {{ $coursePercent }}%"></div>
                                            </div>

                                            <div class="space-y-1 mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                                                @foreach ($course->lessons as $lesson)
                                                    @php
                                                        $isCompleted = $student->completedLessons->contains('id', $lesson->id);
                                                    @endphp
                                                    <div class="flex items-center gap-2">
                                                        <div class="size-4 rounded-full border flex items-center justify-center {{ $isCompleted ? 'bg-green-100 border-green-200 text-green-600 dark:bg-green-900/30 dark:border-green-800' : 'bg-zinc-50 border-zinc-200 text-zinc-400 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-400' }}">
                                                            @if($isCompleted)
                                                                <flux:icon name="check" class="size-2.5" />
                                                            @else
                                                                <span class="text-[10px] font-bold">{{ $loop->iteration }}</span>
                                                            @endif
                                                        </div>
                                                        <flux:text size="xs" class="{{ $isCompleted ? 'text-zinc-900 dark:text-zinc-100 font-medium' : 'text-zinc-500 dark:text-zinc-400' }}">
                                                            {{ $loop->iteration }}. {{ $lesson->title }}
                                                        </flux:text>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </flux:card>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <flux:text variant="subtle">Студенты не найдены</flux:text>
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
        <flux:text variant="subtle">Всего: {{ $students->total() }} студентов</flux:text>
    </div>

    <div>
        {{ $students->links() }}
    </div>
</div>
