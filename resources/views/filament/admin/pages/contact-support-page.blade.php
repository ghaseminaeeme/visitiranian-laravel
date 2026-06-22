<x-filament-panels::page>
    <form wire:submit="submit" class="space-y-6">
        {{ $this->form }}

        <x-filament::button type="submit">
            ارسال تیکت
        </x-filament::button>
    </form>
</x-filament-panels::page>
