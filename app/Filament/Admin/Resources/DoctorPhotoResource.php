<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DoctorPhotoResource\Pages;
use App\Filament\Support\JalaliFormatter;
use App\Models\DoctorPhoto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class DoctorPhotoResource extends Resource
{
    protected static ?string $model = DoctorPhoto::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'عکس‌های پزشکان';

    protected static ?string $modelLabel = 'عکس';

    protected static ?string $pluralModelLabel = 'عکس‌های پزشکان';

    protected static ?string $navigationGroup = 'مدیریت پزشکان';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('doctor_id')
                    ->label('پزشک')
                    ->relationship('doctor', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled(),
                Forms\Components\ViewField::make('preview')
                    ->label('پیش‌نمایش')
                    ->view('filament.admin.components.photo-preview'),
                Forms\Components\Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'pending' => 'در انتظار',
                        'approved' => 'تأیید شده',
                        'rejected' => 'رد شده',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('rejection_reason')
                    ->label('دلیل رد')
                    ->rows(3)
                    ->visible(fn (Forms\Get $get) => $get('status') === 'rejected'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumb_path')
                    ->label('بندانگشتی')
                    ->disk('public')
                    ->defaultImageUrl(fn (DoctorPhoto $record) => Storage::disk('public')->url($record->file_path)),
                Tables\Columns\TextColumn::make('doctor.name')
                    ->label('پزشک')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'در انتظار',
                        'approved' => 'تأیید شده',
                        'rejected' => 'رد شده',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('ارسال')
                    ->formatStateUsing(fn ($state) => JalaliFormatter::dateTime($state))
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'pending' => 'در انتظار',
                        'approved' => 'تأیید شده',
                        'rejected' => 'رد شده',
                    ])
                    ->default('pending'),
            ])
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->label('پیش‌نمایش')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('پیش‌نمایش عکس')
                    ->modalContent(fn (DoctorPhoto $record) => view('filament.admin.components.photo-preview-modal', [
                        'record' => $record,
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('بستن'),
                Tables\Actions\Action::make('approve')
                    ->label('تأیید')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (DoctorPhoto $record) => $record->status === 'pending')
                    ->action(function (DoctorPhoto $record): void {
                        $record->update([
                            'status' => 'approved',
                            'approved_at' => now(),
                            'approved_by' => auth()->id(),
                            'rejection_reason' => null,
                        ]);

                        Notification::make()
                            ->title('عکس تأیید شد')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('رد')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (DoctorPhoto $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('دلیل رد')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (DoctorPhoto $record, array $data): void {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                            'approved_at' => null,
                            'approved_by' => null,
                        ]);

                        Notification::make()
                            ->title('عکس رد شد')
                            ->warning()
                            ->send();
                    }),
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
            'index' => Pages\ListDoctorPhotos::route('/'),
            'edit' => Pages\EditDoctorPhoto::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
