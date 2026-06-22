<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class DeveloperSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';

    protected static ?string $navigationLabel = 'تنظیمات توسعه‌دهنده';

    protected static ?string $title = 'تنظیمات توسعه‌دهنده';

    protected static ?string $navigationGroup = 'تنظیمات';

    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.admin.pages.settings-page';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->isDeveloper();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public function mount(): void
    {
        $keys = config('visitiranian.settings.developer', []);

        $data = [];
        foreach ($keys as $field => $key) {
            $data[$field] = Setting::getValue($key);
        }

        $this->form->fill($data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('debug_frontend')
                    ->label('حالت دیباگ فرانت‌اند')
                    ->helperText('نمایش اطلاعات دیباگ در محیط توسعه'),
                Forms\Components\Textarea::make('maintenance_message')
                    ->label('پیام حالت نگهداری')
                    ->rows(3)
                    ->maxLength(1000),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $keys = config('visitiranian.settings.developer', []);
        $data = $this->form->getState();

        foreach ($keys as $field => $key) {
            Setting::setValue($key, $data[$field] ?? null);
        }

        Notification::make()
            ->title('تنظیمات توسعه‌دهنده ذخیره شد')
            ->success()
            ->send();
    }
}
