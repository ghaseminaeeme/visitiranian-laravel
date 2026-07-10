<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SliderResource\Pages;
use App\Filament\Support\ImageUpload;
use App\Filament\Support\JalaliFormatter;
use App\Models\Slider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;

    protected static ?string $navigationIcon = 'heroicon-o-film';

    protected static ?string $navigationLabel = 'اسلایدرها';

    protected static ?string $modelLabel = 'اسلایدر';

    protected static ?string $pluralModelLabel = 'اسلایدرها';

    protected static ?string $navigationGroup = 'محتوا و تبلیغات';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Section::make('محتوا')
                            ->schema([
                                Forms\Components\Select::make('template_id')
                                    ->label('قالب نمایش')
                                    ->relationship('template', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live(),
                                Forms\Components\TextInput::make('title')
                                    ->label('عنوان')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true),
                                Forms\Components\TextInput::make('subtitle')
                                    ->label('زیرعنوان')
                                    ->maxLength(255)
                                    ->live(onBlur: true),
                                Forms\Components\TextInput::make('cta_text')
                                    ->label('متن دکمه')
                                    ->maxLength(100)
                                    ->live(onBlur: true),
                                Forms\Components\TextInput::make('cta_url')
                                    ->label('لینک دکمه')
                                    ->url()
                                    ->maxLength(500),
                                ImageUpload::make('image_path', 'sliders')
                                    ->live(),
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('ترتیب')
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('فعال')
                                    ->default(true),
                                Forms\Components\DateTimePicker::make('starts_at')
                                    ->label('شروع نمایش')
                                    ->seconds(false),
                                Forms\Components\DateTimePicker::make('ends_at')
                                    ->label('پایان نمایش')
                                    ->seconds(false),
                            ]),
                        Forms\Components\Section::make('پیش‌نمایش زنده')
                            ->schema([
                                Forms\Components\ViewField::make('live_preview')
                                    ->view('filament.admin.components.visual-preview-field')
                                    ->viewData(fn (Get $get) => [
                                        'title' => $get('title'),
                                        'subtitle' => $get('subtitle'),
                                        'ctaText' => $get('cta_text'),
                                        'imageUrl' => $get('image_path')
                                            ? Storage::disk('public')->url($get('image_path'))
                                            : null,
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('تصویر')
                    ->disk('public'),
                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable(),
                Tables\Columns\TextColumn::make('template.name')
                    ->label('قالب'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('ترتیب')
                    ->sortable(),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('شروع')
                    ->formatStateUsing(fn ($state) => JalaliFormatter::dateTime($state))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
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
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
        ];
    }
}
