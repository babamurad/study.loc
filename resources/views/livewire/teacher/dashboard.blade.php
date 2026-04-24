<div class="p-6">
    <div class="mb-8">
        <flux:heading size="xl" level="1">Панель преподавателя</flux:heading>
        <flux:subheading>Добро пожаловать! Здесь вы можете управлять своими уроками и курсами.</flux:subheading>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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

        <flux:card class="flex flex-col gap-4 opacity-50">
            <div>
                <flux:heading size="lg">Статистика</flux:heading>
                <flux:subheading>Прогресс учеников (Скоро)</flux:subheading>
            </div>
            <div class="mt-auto">
                <flux:button disabled>В разработке</flux:button>
            </div>
        </flux:card>
    </div>
</div>
