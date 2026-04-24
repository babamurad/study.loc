<div class="p-6">
    <div class="mb-8 text-center">
        <flux:heading size="xl" level="1">Ваши курсы</flux:heading>
        <flux:subheading>Выберите курс, чтобы продолжить обучение</flux:subheading>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse ($courses as $course)
            <flux:card class="flex flex-col h-full hover:shadow-lg transition-shadow duration-300">
                <div class="aspect-video bg-neutral-100 dark:bg-neutral-800 rounded-lg mb-4 flex items-center justify-center">
                    <flux:icon name="academic-cap" size="xl" class="text-neutral-400" />
                </div>
                
                <div class="flex-1">
                    <flux:heading size="lg" class="mb-2">{{ $course->title }}</flux:heading>
                    <p class="text-neutral-500 text-sm line-clamp-3 mb-4">
                        {{ $course->description ?? 'Описание курса временно отсутствует.' }}
                    </p>
                </div>

                <div class="mt-auto pt-4 border-t border-neutral-200 dark:border-neutral-700 flex justify-between items-center">
                    <span class="text-xs font-medium text-neutral-400">
                        {{ $course->lessons_count ?? $course->lessons()->count() }} уроков
                    </span>
                    <flux:button href="{{ route('courses.show', $course) }}" variant="primary" size="sm">Начать обучение</flux:button>
                </div>
            </flux:card>
        @empty
            <div class="col-span-full py-20 text-center">
                <flux:heading size="lg">Курсы пока не добавлены</flux:heading>
                <flux:subheading>Следите за обновлениями, скоро здесь появятся новые учебные материалы.</flux:subheading>
            </div>
        @endforelse
    </div>
</div>
