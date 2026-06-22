<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Support\JalaliFormatter;
use App\Models\Doctor;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ExpiringDoctorsReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'گزارش انقضای پزشکان';

    protected static ?string $title = 'گزارش پزشکان در حال انقضا';

    protected static ?string $navigationGroup = 'گزارش‌ها';

    protected static ?int $navigationSort = 70;

    protected static string $view = 'filament.admin.pages.expiring-doctors-report';

    public function table(Table $table): Table
    {
        $expiringDays = config('visitiranian.expiring_doctors_days', 30);

        return $table
            ->query(
                Doctor::query()
                    ->with(['city', 'primarySpecialty', 'user'])
                    ->where('is_active', true)
                    ->whereNotNull('expires_at')
                    ->where('expires_at', '<=', now()->addDays($expiringDays))
                    ->orderBy('expires_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('نام')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('شهر')
                    ->sortable(),
                Tables\Columns\TextColumn::make('primarySpecialty.name')
                    ->label('تخصص'),
                Tables\Columns\IconColumn::make('is_vip')
                    ->label('VIP')
                    ->boolean(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('تاریخ انقضا')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => JalaliFormatter::dateTime($state)),
                Tables\Columns\TextColumn::make('sms_mobile')
                    ->label('موبایل پیامک')
                    ->copyable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('ایمیل کاربر')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_vip')
                    ->label('VIP'),
            ])
            ->defaultSort('expires_at');
    }
}
