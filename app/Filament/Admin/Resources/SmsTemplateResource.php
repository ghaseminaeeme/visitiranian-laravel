<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SmsTemplateResource\Pages;
use App\Models\SmsTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SmsTemplateResource extends Resource
{
    protected static ?string $model = SmsTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $navigationLabel = 'قالب‌های پیامک';

    protected static ?string $modelLabel = 'قالب پیامک';

    protected static ?string $pluralModelLabel = 'قالب‌های پیامک';

    protected static ?string $navigationGroup = 'پیامک';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('event_key')
                    ->label('کلید رویداد')
                    ->required()
                    ->maxLength(100)
                    ->unique(ignoreRecord: true)
                    ->helperText('مثلاً appointment_confirmed'),
                Forms\Components\Toggle::make('is_enabled')
                    ->label('فعال')
                    ->default(true),
                Forms\Components\Textarea::make('template_body')
                    ->label('متن قالب')
                    ->required()
                    ->rows(6)
                    ->helperText('از متغیرها مثل {patient_name} و {doctor_name} استفاده کنید'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event_key')
                    ->label('کلید رویداد')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_enabled')
                    ->label('فعال')
                    ->boolean(),
                Tables\Columns\TextColumn::make('template_body')
                    ->label('متن')
                    ->limit(60)
                    ->wrap(),
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
            'index' => Pages\ListSmsTemplates::route('/'),
            'create' => Pages\CreateSmsTemplate::route('/create'),
            'edit' => Pages\EditSmsTemplate::route('/{record}/edit'),
        ];
    }
}
