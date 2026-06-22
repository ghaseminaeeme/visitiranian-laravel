<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AppointmentResource\Pages;
use App\Filament\Support\JalaliFormatter;
use App\Models\Appointment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'نوبت‌ها';

    protected static ?string $modelLabel = 'نوبت';

    protected static ?string $pluralModelLabel = 'نوبت‌ها';

    protected static ?string $navigationGroup = 'مدیریت پزشکان';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('doctor_id')
                    ->label('پزشک')
                    ->relationship('doctor', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\DateTimePicker::make('starts_at')
                    ->label('شروع')
                    ->required()
                    ->seconds(false),
                Forms\Components\DateTimePicker::make('ends_at')
                    ->label('پایان')
                    ->required()
                    ->seconds(false),
                Forms\Components\TextInput::make('patient_name')
                    ->label('نام بیمار')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('patient_phone')
                    ->label('موبایل بیمار')
                    ->tel()
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('patient_national_code')
                    ->label('کد ملی')
                    ->maxLength(10),
                Forms\Components\TextInput::make('tracking_code')
                    ->label('کد پیگیری')
                    ->maxLength(8)
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'confirmed' => 'تأیید شده',
                        'cancelled' => 'لغو شده',
                        'completed' => 'انجام شده',
                        'no_show' => 'عدم حضور',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('cancellation_reason')
                    ->label('دلیل لغو')
                    ->rows(2)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tracking_code')
                    ->label('کد پیگیری')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('doctor.name')
                    ->label('پزشک')
                    ->searchable()
                    ->sortable(),
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
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'confirmed' => 'تأیید شده',
                        'cancelled' => 'لغو شده',
                        'completed' => 'انجام شده',
                        'no_show' => 'عدم حضور',
                        default => $state,
                    })
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
                Tables\Filters\SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'confirmed' => 'تأیید شده',
                        'cancelled' => 'لغو شده',
                        'completed' => 'انجام شده',
                        'no_show' => 'عدم حضور',
                    ]),
                Tables\Filters\SelectFilter::make('doctor_id')
                    ->label('پزشک')
                    ->relationship('doctor', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
