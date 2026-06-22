<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PageResource\Pages;
use App\Filament\Support\JalaliFormatter;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'صفحات';

    protected static ?string $modelLabel = 'صفحه';

    protected static ?string $pluralModelLabel = 'صفحات';

    protected static ?string $navigationGroup = 'محتوا و تبلیغات';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('محتوا')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('عنوان')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true),
                        Forms\Components\TextInput::make('slug')
                            ->label('نامک')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\RichEditor::make('body')
                            ->label('متن')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_published')
                            ->label('منتشر شده'),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('تاریخ انتشار')
                            ->seconds(false),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('سئو')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label('عنوان متا')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('meta_description')
                            ->label('توضیحات متا')
                            ->rows(2)
                            ->maxLength(500),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('نامک'),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('منتشر')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('انتشار')
                    ->formatStateUsing(fn ($state) => JalaliFormatter::dateTime($state)),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('به‌روزرسانی')
                    ->formatStateUsing(fn ($state) => JalaliFormatter::dateTime($state))
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
