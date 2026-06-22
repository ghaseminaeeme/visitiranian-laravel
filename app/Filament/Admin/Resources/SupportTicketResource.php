<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SupportTicketResource\Pages;
use App\Filament\Support\JalaliFormatter;
use App\Models\SupportTicket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'تیکت‌های پشتیبانی';

    protected static ?string $modelLabel = 'تیکت';

    protected static ?string $pluralModelLabel = 'تیکت‌های پشتیبانی';

    protected static ?string $navigationGroup = 'پشتیبانی';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ticket_number')
                    ->label('شماره تیکت')
                    ->disabled(),
                Forms\Components\Select::make('user_id')
                    ->label('کاربر')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('subject')
                    ->label('موضوع')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('category')
                    ->label('دسته‌بندی')
                    ->options([
                        'question' => 'سؤال',
                        'bug' => 'گزارش خطا',
                        'feature' => 'درخواست قابلیت',
                        'other' => 'سایر',
                    ])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'sent' => 'ارسال شده',
                        'in_progress' => 'در حال بررسی',
                        'resolved' => 'حل شده',
                        'closed' => 'بسته شده',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('body')
                    ->label('متن')
                    ->required()
                    ->rows(6)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('page_url')
                    ->label('آدرس صفحه')
                    ->url()
                    ->maxLength(500),
                Forms\Components\Select::make('error_log_id')
                    ->label('لاگ خطای مرتبط')
                    ->relationship('errorLog', 'message')
                    ->searchable()
                    ->preload(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label('شماره')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('کاربر')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('موضوع')
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('دسته')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'question' => 'سؤال',
                        'bug' => 'گزارش خطا',
                        'feature' => 'درخواست قابلیت',
                        default => 'سایر',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'sent' => 'ارسال شده',
                        'in_progress' => 'در حال بررسی',
                        'resolved' => 'حل شده',
                        'closed' => 'بسته شده',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ')
                    ->formatStateUsing(fn ($state) => JalaliFormatter::dateTime($state))
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'sent' => 'ارسال شده',
                        'in_progress' => 'در حال بررسی',
                        'resolved' => 'حل شده',
                        'closed' => 'بسته شده',
                    ]),
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
            'index' => Pages\ListSupportTickets::route('/'),
            'create' => Pages\CreateSupportTicket::route('/create'),
            'edit' => Pages\EditSupportTicket::route('/{record}/edit'),
        ];
    }
}
