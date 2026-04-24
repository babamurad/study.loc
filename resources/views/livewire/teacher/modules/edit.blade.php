<div class="p-6 max-w-2xl mx-auto">
    <div class="mb-6">
        <flux:button href="{{ route('teacher.modules.index') }}" variant="ghost" icon="arrow-left" class="-ml-2">Назад к списку</flux:button>
        <flux:heading size="xl" class="mt-2">Редактировать модуль: {{ $module->title }}</flux:heading>
    </div>

    <form wire:submit="save" class="space-y-6">
        <flux:field>
            <flux:label>Название модуля</flux:label>
            <flux:input wire:model="title" />
            <flux:error name="title" />
        </flux:field>

        <flux:field>
            <flux:label>Курс</flux:label>
            <flux:select wire:model="course_id">
                @foreach ($courses as $course)
                    <flux:select.option value="{{ $course->id }}">{{ $course->title }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:error name="course_id" />
        </flux:field>

        <flux:field>
            <flux:label>Позиция (порядок)</flux:label>
            <flux:input type="number" wire:model="position" min="1" />
            <flux:description>Порядок отображения модуля в курсе.</flux:description>
            <flux:error name="position" />
        </flux:field>

        <div class="flex justify-end space-x-2">
            <flux:button href="{{ route('teacher.modules.index') }}" variant="ghost">Отмена</flux:button>
            <flux:button type="submit" variant="primary">Сохранить изменения</flux:button>
        </div>
    </form>
</div>
