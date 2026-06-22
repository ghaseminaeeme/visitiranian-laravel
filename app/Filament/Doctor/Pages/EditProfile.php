<?php

namespace App\Filament\Doctor\Pages;

use App\Filament\Doctor\Support\DoctorPanelOptions;
use App\Models\Doctor;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'پروفایل من';

    protected static ?string $title = 'ویرایش پروفایل';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.doctor.pages.edit-profile';

    public ?array $data = [];

    public Doctor $doctor;

    public function mount(): void
    {
        $doctor = auth()->user()?->doctor;

        abort_unless($doctor instanceof Doctor, 403);

        Gate::authorize('update', $doctor);

        $this->doctor = $doctor->load(['contactPhones', 'socialLinks']);

        $this->form->fill([
            'bio' => $this->doctor->bio,
            'address' => $this->doctor->address,
            'sms_mobile' => $this->doctor->sms_mobile,
            'contactPhones' => $this->doctor->contactPhones()
                ->ordered()
                ->get()
                ->map(fn ($phone) => [
                    'id' => $phone->id,
                    'phone' => $phone->phone,
                    'label' => $phone->label,
                    'sort_order' => $phone->sort_order,
                    'is_visible' => $phone->is_visible,
                ])
                ->all(),
            'socialLinks' => $this->doctor->socialLinks()
                ->ordered()
                ->get()
                ->map(fn ($link) => [
                    'id' => $link->id,
                    'platform' => $link->platform,
                    'url' => $link->url,
                    'sort_order' => $link->sort_order,
                ])
                ->all(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات عمومی')
                    ->schema([
                        Forms\Components\Textarea::make('bio')
                            ->label('بیوگرافی')
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('address')
                            ->label('آدرس')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\Placeholder::make('sms_mobile')
                            ->label('موبایل پیامک')
                            ->content(fn (): string => $this->doctor->sms_mobile ?: '—')
                            ->helperText('برای تغییر موبایل پیامک با پشتیبانی تماس بگیرید.'),
                    ]),
                Forms\Components\Section::make('شماره‌های تماس')
                    ->schema([
                        Forms\Components\Repeater::make('contactPhones')
                            ->label('شماره‌های تماس')
                            ->schema([
                                Forms\Components\TextInput::make('phone')
                                    ->label('شماره')
                                    ->tel()
                                    ->required()
                                    ->maxLength(20),
                                Forms\Components\TextInput::make('label')
                                    ->label('برچسب')
                                    ->maxLength(50)
                                    ->placeholder('مثلاً مطب، منشی'),
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('ترتیب')
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\Toggle::make('is_visible')
                                    ->label('نمایش عمومی')
                                    ->default(true),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('افزودن شماره')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('شبکه‌های اجتماعی')
                    ->schema([
                        Forms\Components\Repeater::make('socialLinks')
                            ->label('لینک‌های اجتماعی')
                            ->schema([
                                Forms\Components\Select::make('platform')
                                    ->label('پلتفرم')
                                    ->options(DoctorPanelOptions::socialPlatforms())
                                    ->required()
                                    ->native(false),
                                Forms\Components\TextInput::make('url')
                                    ->label('آدرس')
                                    ->url()
                                    ->required()
                                    ->maxLength(500),
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('ترتیب')
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('افزودن لینک')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        Gate::authorize('update', $this->doctor);

        $data = $this->form->getState();

        $this->doctor->update([
            'bio' => $data['bio'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        $this->syncContactPhones($data['contactPhones'] ?? []);
        $this->syncSocialLinks($data['socialLinks'] ?? []);

        Notification::make()
            ->title('پروفایل ذخیره شد')
            ->success()
            ->send();
    }

    /**
     * @param  array<int, array<string, mixed>>  $phones
     */
    protected function syncContactPhones(array $phones): void
    {
        $keptIds = [];

        foreach ($phones as $index => $phone) {
            $attributes = [
                'phone' => $phone['phone'],
                'label' => $phone['label'] ?? null,
                'sort_order' => $phone['sort_order'] ?? $index,
                'is_visible' => $phone['is_visible'] ?? true,
            ];

            if (! empty($phone['id'])) {
                $record = $this->doctor->contactPhones()->whereKey($phone['id'])->first();

                if ($record) {
                    $record->update($attributes);
                    $keptIds[] = $record->id;

                    continue;
                }
            }

            $created = $this->doctor->contactPhones()->create($attributes);
            $keptIds[] = $created->id;
        }

        $this->doctor->contactPhones()->whereNotIn('id', $keptIds)->delete();
    }

    /**
     * @param  array<int, array<string, mixed>>  $links
     */
    protected function syncSocialLinks(array $links): void
    {
        $keptIds = [];

        foreach ($links as $index => $link) {
            $attributes = [
                'platform' => $link['platform'],
                'url' => $link['url'],
                'sort_order' => $link['sort_order'] ?? $index,
            ];

            if (! empty($link['id'])) {
                $record = $this->doctor->socialLinks()->whereKey($link['id'])->first();

                if ($record) {
                    $record->update($attributes);
                    $keptIds[] = $record->id;

                    continue;
                }
            }

            $created = $this->doctor->socialLinks()->create($attributes);
            $keptIds[] = $created->id;
        }

        $this->doctor->socialLinks()->whereNotIn('id', $keptIds)->delete();
    }
}
