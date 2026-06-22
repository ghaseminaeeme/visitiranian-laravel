<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'تنظیمات عمومی';

    protected static ?string $title = 'تنظیمات عمومی';

    protected static ?string $navigationGroup = 'تنظیمات';

    protected static ?int $navigationSort = 90;

    protected static string $view = 'filament.admin.pages.settings-page';

    public ?array $data = [];

    public function mount(): void
    {
        $keys = config('visitiranian.settings.general', []);

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
                Forms\Components\Section::make('اطلاعات سایت')
                    ->schema([
                        Forms\Components\TextInput::make('site_name')
                            ->label('نام سایت')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('site_tagline')
                            ->label('شعار سایت')
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('تماس')
                    ->schema([
                        Forms\Components\TextInput::make('contact_email')
                            ->label('ایمیل تماس')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('contact_phone')
                            ->label('تلفن تماس')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('support_whatsapp')
                            ->label('واتساپ پشتیبانی')
                            ->tel()
                            ->maxLength(20),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $keys = config('visitiranian.settings.general', []);
        $data = $this->form->getState();

        foreach ($keys as $field => $key) {
            Setting::setValue($key, $data[$field] ?? null);
        }

        Notification::make()
            ->title('تنظیمات ذخیره شد')
            ->success()
            ->send();
    }
}
