<div class="p-6">
    <div class="mb-8">
        <flux:heading size="xl" level="1">Панель преподавателя</flux:heading>
        <flux:subheading>Добро пожаловать! Здесь вы можете управлять своими уроками и курсами.</flux:subheading>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <flux:card class="flex items-center gap-4 p-4">
            <div class="p-3 rounded-lg bg-primary/10 text-primary">
                <flux:icon name="users" class="size-6" />
            </div>
            <div>
                <flux:heading size="sm" class="text-neutral-500 dark:text-neutral-400">Студентов</flux:heading>
                <div class="text-2xl font-bold mt-1">{{ $totalStudents }}</div>
            </div>
        </flux:card>

        <flux:card class="flex items-center gap-4 p-4">
            <div class="p-3 rounded-lg bg-primary/10 text-primary">
                <flux:icon name="academic-cap" class="size-6" />
            </div>
            <div>
                <flux:heading size="sm" class="text-neutral-500 dark:text-neutral-400">Курсов</flux:heading>
                <div class="text-2xl font-bold mt-1">{{ $totalCourses }}</div>
            </div>
        </flux:card>

        <flux:card class="flex items-center gap-4 p-4">
            <div class="p-3 rounded-lg bg-primary/10 text-primary">
                <flux:icon name="book-open" class="size-6" />
            </div>
            <div>
                <flux:heading size="sm" class="text-neutral-500 dark:text-neutral-400">Уроков</flux:heading>
                <div class="text-2xl font-bold mt-1">{{ $totalLessons }}</div>
            </div>
        </flux:card>

        <flux:card class="flex items-center gap-4 p-4">
            <div class="p-3 rounded-lg bg-primary/10 text-primary">
                <flux:icon name="check-circle" class="size-6" />
            </div>
            <div>
                <flux:heading size="sm" class="text-neutral-500 dark:text-neutral-400">Успешность</flux:heading>
                <div class="text-2xl font-bold mt-1">{{ $successRate }}%</div>
            </div>
        </flux:card>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <flux:card class="flex flex-col gap-4">
            <div>
                <flux:heading size="lg">Уроки</flux:heading>
                <flux:subheading>Управление учебными материалами</flux:subheading>
            </div>
            <div class="mt-auto">
                <flux:button href="{{ route('teacher.lessons.index') }}" variant="primary">Перейти к урокам</flux:button>
            </div>
        </flux:card>

        <flux:card class="flex flex-col gap-4">
            <div>
                <flux:heading size="lg">Курсы</flux:heading>
                <flux:subheading>Создание и редактирование курсов</flux:subheading>
            </div>
            <div class="mt-auto">
                <flux:button href="{{ route('teacher.courses.index') }}" variant="primary">Перейти к курсам</flux:button>
            </div>
        </flux:card>

        <flux:card class="flex flex-col gap-4">
            <div>
                <flux:heading size="lg">Модули</flux:heading>
                <flux:subheading>Управление разделами курсов</flux:subheading>
            </div>
            <div class="mt-auto">
                <flux:button href="{{ route('teacher.modules.index') }}" variant="primary">Перейти к модулям</flux:button>
            </div>
        </flux:card>

        <flux:card class="flex flex-col gap-4">
            <div>
                <flux:heading size="lg">Статистика</flux:heading>
                <flux:subheading>Прогресс учеников</flux:subheading>
            </div>
            <div class="mt-auto">
                <flux:button href="{{ route('teacher.students.index') }}" variant="primary">Перейти к статистике</flux:button>
            </div>
        </flux:card>
    </div>
</div>
