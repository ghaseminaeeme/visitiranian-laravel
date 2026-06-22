<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SmsSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'تنظیمات پیامک';

    protected static ?string $title = 'تنظیمات پیامک';

    protected static ?string $navigationGroup = 'تنظیمات';

    protected static ?int $navigationSort = 91;

    protected static string $view = 'filament.admin.pages.settings-page';

    public ?array $data = [];

    public function mount(): void
    {
        $keys = config('visitiranian.settings.sms', []);

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
                Forms\Components\Toggle::make('sms_enabled')
                    ->label('فعال‌سازی پیامک')
                    ->default(true),
                Forms\Components\Select::make('sms_provider')
                    ->label('سرویس‌دهنده')
                    ->options([
                        'kavenegar' => 'کاوه‌نگار',
                        'melipayamak' => 'ملی‌پیامک',
                        'smsir' => 'SMS.ir',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('sms_api_key')
                    ->label('کلید API')
                    ->password()
                    ->revealable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('sms_sender_number')
                    ->label('شماره ارسال‌کننده')
                    ->maxLength(20),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $keys = config('visitiranian.settings.sms', []);
        $data = $this->form->getState();

        foreach ($keys as $field => $key) {
            Setting::setValue($key, $data[$field] ?? null);
        }

        Notification::make()
            ->title('تنظیمات پیامک ذخیره شد')
            ->success()
            ->send();
    }
}
