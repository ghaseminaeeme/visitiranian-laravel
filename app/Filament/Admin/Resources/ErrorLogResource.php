<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ErrorLogResource\Pages;
use App\Filament\Support\JalaliFormatter;
use App\Models\ErrorLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ErrorLogResource extends Resource
{
    protected static ?string $model = ErrorLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-bug-ant';

    protected static ?string $navigationLabel = 'لاگ خطاها';

    protected static ?string $modelLabel = 'لاگ خطا';

    protected static ?string $pluralModelLabel = 'لاگ خطاها';

    protected static ?string $navigationGroup = 'سیستم';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('level')
                    ->label('سطح')
                    ->disabled(),
                Forms\Components\Textarea::make('message')
                    ->label('پیام')
                    ->disabled()
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('url')
                    ->label('آدرس')
                    ->disabled(),
                Forms\Components\Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'new' => 'جدید',
                        'investigating' => 'در حال بررسی',
                        'resolved' => 'حل شده',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('resolved_note')
                    ->label('یادداشت حل مشکل')
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('resolved_at')
                    ->label('زمان حل')
                    ->seconds(false),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('level')
                    ->label('سطح')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'error', 'critical' => 'danger',
                        'warning' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('message')
                    ->label('پیام')
                    ->limit(50)
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('url')
                    ->label('آدرس')
                    ->limit(30)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'جدید',
                        'investigating' => 'در حال بررسی',
                        'resolved' => 'حل شده',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('occurred_at')
                    ->label('زمان وقوع')
                    ->formatStateUsing(fn ($state) => JalaliFormatter::dateTime($state))
                    ->sortable(),
            ])
            ->defaultSort('occurred_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'new' => 'جدید',
                        'investigating' => 'در حال بررسی',
                        'resolved' => 'حل شده',
                    ]),
                Tables\Filters\SelectFilter::make('level')
                    ->label('سطح')
                    ->options([
                        'debug' => 'Debug',
                        'info' => 'Info',
                        'warning' => 'Warning',
                        'error' => 'Error',
                        'critical' => 'Critical',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListErrorLogs::route('/'),
            'view' => Pages\ViewErrorLog::route('/{record}'),
            'edit' => Pages\EditErrorLog::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
