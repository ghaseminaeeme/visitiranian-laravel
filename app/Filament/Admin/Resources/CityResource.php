<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CityResource\Pages;
use App\Models\City;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'شهرها';

    protected static ?string $modelLabel = 'شهر';

    protected static ?string $pluralModelLabel = 'شهرها';

    protected static ?string $navigationGroup = 'اطلاعات پایه';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات شهر')
                    ->schema([
                        Forms\Components\Select::make('province_id')
                            ->label('استان')
                            ->relationship('province', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('نام')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true),
                        Forms\Components\TextInput::make('slug')
                            ->label('نامک')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('ترتیب')
                            ->numeric()
                            ->default(0),
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
                Tables\Columns\TextColumn::make('name')
                    ->label('نام')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('province.name')
                    ->label('استان')
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('نامک'),
                Tables\Columns\TextColumn::make('doctors_count')
                    ->label('تعداد پزشک')
                    ->counts('doctors'),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('province_id')
                    ->label('استان')
                    ->relationship('province', 'name'),
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
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}
