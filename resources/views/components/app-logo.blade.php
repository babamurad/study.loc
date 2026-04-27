@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="To Front" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-12 items-center justify-center rounded-md bg-[#fafafa] dark:bg-zinc-900 text-zinc-900 dark:text-white">
            <x-app-logo-icon class="size-10" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="To Front" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-12 items-center justify-center rounded-md bg-[#fafafa] dark:bg-zinc-900 text-zinc-900 dark:text-white">
            <x-app-logo-icon class="size-10" />
        </x-slot>
    </flux:brand>
@endif
