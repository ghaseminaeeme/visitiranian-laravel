<?php

namespace App\Filament\Doctor\Resources;

use App\Filament\Doctor\Concerns\HasAuthenticatedDoctor;
use App\Filament\Doctor\Resources\DoctorScheduleResource\Pages;
use App\Filament\Doctor\Support\DoctorPanelOptions;
use App\Filament\Support\JalaliFormatter;
use App\Models\DoctorSchedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DoctorScheduleResource extends Resource
{
    use HasAuthenticatedDoctor;

    protected static ?string $model = DoctorSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'برنامه هفتگی';

    protected static ?string $modelLabel = 'برنامه';

    protected static ?string $pluralModelLabel = 'برنامه‌های هفتگی';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return static::scopeToAuthenticatedDoctor(parent::getEloquentQuery());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('day_of_week')
                    ->label('روز هفته')
                    ->options(DoctorPanelOptions::daysOfWeek())
                    ->required()
                    ->native(false),
                Forms\Components\TimePicker::make('start_time')
                    ->label('ساعت شروع')
                    ->required()
                    ->seconds(false),
                Forms\Components\TimePicker::make('end_time')
                    ->label('ساعت پایان')
                    ->required()
                    ->seconds(false)
                    ->after('start_time'),
                Forms\Components\TextInput::make('slot_duration_minutes')
                    ->label('مدت هر نوبت (دقیقه)')
                    ->numeric()
                    ->default(30)
                    ->minValue(5)
                    ->maxValue(240)
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('day_of_week')
                    ->label('روز')
                    ->formatStateUsing(fn (int $state): string => DoctorPanelOptions::daysOfWeek()[$state] ?? (string) $state)
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('شروع')
                    ->time('H:i'),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('پایان')
                    ->time('H:i'),
                Tables\Columns\TextColumn::make('slot_duration_minutes')
                    ->label('مدت نوبت')
                    ->suffix(' دقیقه'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('به‌روزرسانی')
                    ->formatStateUsing(fn ($state) => JalaliFormatter::dateTime($state))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('day_of_week')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('فعال'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListDoctorSchedules::route('/'),
            'create' => Pages\CreateDoctorSchedule::route('/create'),
            'edit' => Pages\EditDoctorSchedule::route('/{record}/edit'),
        ];
    }
}
