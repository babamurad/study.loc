<div class="p-6">
    <div class="mb-6">
        <flux:button href="{{ route('teacher.lessons.index') }}" variant="ghost" icon="chevron-left" class="mb-2">Назад к списку</flux:button>
        <flux:heading size="xl" level="1">Редактирование урока</flux:heading>
        <flux:subheading>Внесите необходимые изменения в урок</flux:subheading>
    </div>

    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:select wire:model.live="course_id" label="Курс" placeholder="Выберите курс">
                    @foreach ($courses as $course)
                        <flux:select.option value="{{ $course->id }}">{{ $course->title }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model="module_id" label="Модуль" placeholder="Выберите модуль" :disabled="!$course_id">
                    @foreach ($modules as $module)
                        <flux:select.option value="{{ $module->id }}">{{ $module->title }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input wire:model.live="title" label="Заголовок" placeholder="Введите название урока" />
                <flux:input wire:model="slug" label="Slug (URL)" placeholder="URL-адрес урока" />
            </div>

            <flux:textarea wire:model="content" label="Содержимое" placeholder="Текст урока (поддерживается HTML/Markdown)" rows="10" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                <flux:input type="number" wire:model="position" label="Позиция" />
                <flux:checkbox wire:model="is_published" label="Опубликован" />
            </div>

            <div class="flex justify-end gap-4 border-t pt-6">
                <flux:button href="{{ route('teacher.lessons.index') }}" variant="ghost">Отмена</flux:button>
                <flux:button type="submit" variant="primary">Сохранить изменения</flux:button>
            </div>
        </form>
    </flux:card>
</div>
