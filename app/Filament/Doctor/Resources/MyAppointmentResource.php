<?php

namespace App\Filament\Doctor\Resources;

use App\Filament\Doctor\Concerns\HasAuthenticatedDoctor;
use App\Filament\Doctor\Resources\MyAppointmentResource\Pages;
use App\Filament\Doctor\Support\DoctorPanelOptions;
use App\Filament\Support\JalaliFormatter;
use App\Models\Appointment;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyAppointmentResource extends Resource
{
    use HasAuthenticatedDoctor;

    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'نوبت‌های من';

    protected static ?string $modelLabel = 'نوبت';

    protected static ?string $pluralModelLabel = 'نوبت‌های من';

    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        return static::scopeToAuthenticatedDoctor(parent::getEloquentQuery());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات نوبت')
                    ->schema([
                        Forms\Components\TextInput::make('tracking_code')
                            ->label('کد پیگیری')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('شروع')
                            ->disabled()
                            ->seconds(false),
                        Forms\Components\DateTimePicker::make('ends_at')
                            ->label('پایان')
                            ->disabled()
                            ->seconds(false),
                        Forms\Components\TextInput::make('patient_name')
                            ->label('نام بیمار')
                            ->disabled(),
                        Forms\Components\TextInput::make('patient_phone')
                            ->label('موبایل بیمار')
                            ->disabled(),
                        Forms\Components\TextInput::make('patient_national_code')
                            ->label('کد ملی')
                            ->disabled(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('مدیریت وضعیت')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('وضعیت')
                            ->options(DoctorPanelOptions::appointmentStatuses())
                            ->required()
                            ->native(false)
                            ->live(),
                        Forms\Components\Textarea::make('cancellation_reason')
                            ->label('دلیل لغو')
                            ->rows(2)
                            ->visible(fn (Get $get): bool => $get('status') === 'cancelled')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tracking_code')
                    ->label('کد پیگیری')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('patient_name')
                    ->label('بیمار')
                    ->searchable(),
                Tables\Columns\TextColumn::make('patient_phone')
                    ->label('موبایل'),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('زمان')
                    ->formatStateUsing(fn ($state) => JalaliFormatter::dateTime($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => DoctorPanelOptions::appointmentStatuses()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'cancelled' => 'danger',
                        'completed' => 'info',
                        'no_show' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('starts_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('starts_at')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('از تاریخ'),
                        Forms\Components\DatePicker::make('date_to')
                            ->label('تا تاریخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('starts_at', '>=', $date),
                            )
                            ->when(
                                $data['date_to'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('starts_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['date_from'] ?? null) {
                            $indicators['date_from'] = 'از '.JalaliFormatter::date(Carbon::parse($data['date_from']));
                        }

                        if ($data['date_to'] ?? null) {
                            $indicators['date_to'] = 'تا '.JalaliFormatter::date(Carbon::parse($data['date_to']));
                        }

                        return $indicators;
                    }),
                Tables\Filters\SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options(DoctorPanelOptions::appointmentStatuses()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyAppointments::route('/'),
            'edit' => Pages\EditMyAppointment::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
