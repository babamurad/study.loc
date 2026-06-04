<div class="p-6 max-w-4xl mx-auto">
    <div class="mb-6">
        <flux:button href="{{ route('teacher.quizzes.index') }}" variant="ghost" icon="chevron-left" class="mb-2">Назад к списку</flux:button>
        <flux:heading size="xl" level="1">Новый тест</flux:heading>
        <flux:subheading>Создайте новый тест, к которому потом можно будет добавлять вопросы</flux:subheading>
    </div>

    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <div class="space-y-4">
                <flux:input wire:model="title" label="Название теста" placeholder="Например: Итоговый тест по модулю 1" />
                <flux:textarea wire:model="description" label="Описание теста (необязательно)" placeholder="Опишите, о чем этот тест..." rows="3" />
                
                <div class="w-1/3">
                    <flux:input type="number" wire:model="pass_threshold" label="Проходной балл (%)" min="0" max="100" />
                    <flux:subheading class="mt-1">Процент правильных ответов для успешного прохождения.</flux:subheading>
                </div>
            </div>

            <div class="flex justify-end gap-4 border-t pt-6">
                <flux:button href="{{ route('teacher.quizzes.index') }}" variant="ghost">Отмена</flux:button>
                <flux:button type="submit" variant="primary">Создать тест и добавить вопросы</flux:button>
            </div>
        </form>
    </flux:card>
</div>
