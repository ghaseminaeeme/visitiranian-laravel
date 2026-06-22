<?php

namespace App\Filament\Admin\Resources\DoctorResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ContactPhonesRelationManager extends RelationManager
{
    protected static string $relationship = 'contactPhones';

    protected static ?string $title = 'شماره‌های تماس';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('phone')
                    ->label('شماره')
                    ->tel()
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('label')
                    ->label('برچسب')
                    ->maxLength(50),
                Forms\Components\TextInput::make('sort_order')
                    ->label('ترتیب')
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_visible')
                    ->label('نمایش عمومی')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('phone')
                    ->label('شماره'),
                Tables\Columns\TextColumn::make('label')
                    ->label('برچسب'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('ترتیب'),
                Tables\Columns\IconColumn::make('is_visible')
                    ->label('نمایش')
                    ->boolean(),
            ])
            ->reorderable('sort_order')
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
