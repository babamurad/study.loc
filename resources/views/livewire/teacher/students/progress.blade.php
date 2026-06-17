<div class="p-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <flux:button href="{{ route('teacher.students.index') }}" wire:navigate icon="arrow-left" variant="ghost" />
            <div>
                <flux:heading size="xl" level="1">Прогресс ученика: {{ $student->name }}</flux:heading>
                <flux:subheading>История прохождения тестов и практических заданий</flux:subheading>
            </div>
        </div>
        <flux:button href="{{ route('teacher.students.dashboard', $student) }}" wire:navigate variant="primary" icon="eye">
            Дашборд ученика
        </flux:button>
    </div>

    <div class="mt-8 mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <flux:icon name="document-text" class="size-5 text-zinc-500" />
                <flux:heading size="lg">Квизы (Тесты)</flux:heading>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <flux:select wire:model.live="quizFilter" class="w-full sm:w-auto min-w-40">
                    <option value="all">Все статусы</option>
                    <option value="passed">Сданы</option>
                    <option value="failed">Не сданы</option>
                </flux:select>
                <flux:select wire:model.live="quizSort" class="w-full sm:w-auto min-w-40">
                    <option value="date_desc">Сначала новые</option>
                    <option value="date_asc">Сначала старые</option>
                    <option value="score_desc">По убыванию баллов</option>
                </flux:select>
            </div>
        </div>
    </div>

    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 rounded-xl overflow-hidden shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-zinc-50/50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700">
                        <tr>
                            <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Тест / Курс</th>
                            <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200 text-center">Оценка</th>
                            <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200 text-center">Статус</th>
                            <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200 text-right">Дата</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($quizAttempts as $attempt)
                            @php
                                $isPassed = $attempt->passed;
                            @endphp
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors {{ $isPassed ? 'bg-emerald-50/50 dark:bg-emerald-900/10' : 'bg-red-50/30 dark:bg-red-900/10' }}">
                                <td class="px-6 py-4">
                                    <div class="text-zinc-900 dark:text-white font-medium">{{ $attempt->quiz?->title ?? 'Удаленный тест' }}</div>
                                    <flux:text variant="subtle" size="sm" class="dark:text-zinc-400">{{ $attempt->quiz?->course?->title ?? 'Без курса' }}</flux:text>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    {{ $attempt->score }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($attempt->passed)
                                        <flux:badge color="green" size="sm">Сдан</flux:badge>
                                    @else
                                        <flux:badge color="red" size="sm">Не сдан</flux:badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $attempt->created_at->format('d.m.Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-zinc-500">
                                    Ученик еще не проходил квизы
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
    </div>

    <div class="mt-12 mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <flux:icon name="code-bracket" class="size-5 text-zinc-500" />
                <flux:heading size="lg">Практические задания</flux:heading>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <flux:select wire:model.live="practiceFilter" class="w-full sm:w-auto min-w-40">
                    <option value="all">Все статусы</option>
                    <option value="passed">Сданы</option>
                    <option value="failed">Не сданы</option>
                </flux:select>
                <flux:select wire:model.live="practiceSort" class="w-full sm:w-auto min-w-40">
                    <option value="date_desc">Сначала новые</option>
                    <option value="date_asc">Сначала старые</option>
                </flux:select>
            </div>
        </div>
    </div>

    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 rounded-xl overflow-hidden shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-zinc-50/50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700">
                        <tr>
                            <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Практика / Урок</th>
                            <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200 text-center">Попытка</th>
                            <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200 text-center">Статус проверки</th>
                            <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200 text-center">Результат</th>
                            <th class="px-6 py-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200 text-right">Дата</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($practiceSubmissions as $submission)
                            @php
                                $isCompleted = $submission->status === 'completed';
                                $isPassed = $isCompleted && $submission->passed;
                                $isFailed = $submission->status === 'failed' || ($isCompleted && !$submission->passed);
                            @endphp
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors {{ $isPassed ? 'bg-emerald-50/50 dark:bg-emerald-900/10' : ($isFailed ? 'bg-red-50/30 dark:bg-red-900/10' : '') }}">
                                <td class="px-6 py-4">
                                    <div class="text-zinc-900 dark:text-white font-medium">{{ $submission->practice->title }}</div>
                                    <flux:text variant="subtle" size="sm" class="dark:text-zinc-400">
                                        @if($submission->practice->practicable)
                                            {{ class_basename($submission->practice->practicable_type) }}: {{ $submission->practice->practicable->title }}
                                        @else
                                            Без привязки
                                        @endif
                                    </flux:text>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    #{{ $submission->attempt_no }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusColors = [
                                            'pending' => 'zinc',
                                            'running' => 'blue',
                                            'completed' => 'green',
                                            'failed' => 'red',
                                            'timeout' => 'orange',
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Ожидает',
                                            'running' => 'В процессе',
                                            'completed' => 'Завершено',
                                            'failed' => 'Ошибка',
                                            'timeout' => 'Тайм-аут',
                                        ];
                                    @endphp
                                    <flux:badge color="{{ $statusColors[$submission->status] ?? 'zinc' }}" size="sm">
                                        {{ $statusLabels[$submission->status] ?? $submission->status }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($submission->status === 'completed')
                                        @if ($submission->passed)
                                            <flux:badge color="green" size="sm">Пройдено</flux:badge>
                                        @else
                                            <flux:badge color="red" size="sm">Не пройдено</flux:badge>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $submission->created_at->format('d.m.Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-zinc-500">
                                    Ученик еще не отправлял решения практических заданий
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
    </div>
</div>
