<div class="p-6 max-w-2xl mx-auto">
    <div class="mb-6">
        <flux:button href="{{ route('teacher.courses.index') }}" variant="ghost" icon="arrow-left" class="-ml-2">Назад к списку</flux:button>
        <flux:heading size="xl" class="mt-2">Редактировать курс: {{ $course->title }}</flux:heading>
    </div>

    <form wire:submit="save" class="space-y-6">
        <flux:field>
            <flux:label>Название курса</flux:label>
            <flux:input wire:model="title" />
            <flux:error name="title" />
        </flux:field>

        <flux:field>
            <flux:label>Слизняк (Slug)</flux:label>
            <flux:input wire:model="slug" />
            <flux:description>Используется в URL-адресе курса.</flux:description>
            <flux:error name="slug" />
        </flux:field>

        <flux:field>
            <flux:label>Описание</flux:label>
            <flux:textarea wire:model="description" rows="5" />
            <flux:error name="description" />
        </flux:field>

        <flux:field>
            <div class="flex items-center justify-between">
                <div>
                    <flux:label>Опубликовать</flux:label>
                    <flux:description>Сделать курс видимым для студентов.</flux:description>
                </div>
                <flux:switch wire:model="is_published" />
            </div>
        </flux:field>

        <div class="flex justify-end space-x-2">
            <flux:button href="{{ route('teacher.courses.index') }}" variant="ghost">Отмена</flux:button>
            <flux:button type="submit" variant="primary">Сохранить изменения</flux:button>
        </div>
    </form>
</div>
