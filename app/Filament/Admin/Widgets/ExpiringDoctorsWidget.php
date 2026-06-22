<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Support\JalaliFormatter;
use App\Models\Doctor;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ExpiringDoctorsWidget extends BaseWidget
{
    protected static ?string $heading = 'پزشکان در حال انقضا';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $expiringDays = config('visitiranian.expiring_doctors_days', 30);

        return $table
            ->query(
                Doctor::query()
                    ->with(['city', 'primarySpecialty'])
                    ->where('is_active', true)
                    ->whereNotNull('expires_at')
                    ->where('expires_at', '<=', now()->addDays($expiringDays))
                    ->orderBy('expires_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('نام')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('شهر'),
                Tables\Columns\TextColumn::make('primarySpecialty.name')
                    ->label('تخصص'),
                Tables\Columns\IconColumn::make('is_vip')
                    ->label('VIP')
                    ->boolean(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('تاریخ انقضا')
                    ->formatStateUsing(fn ($state) => JalaliFormatter::dateTime($state)),
                Tables\Columns\TextColumn::make('sms_mobile')
                    ->label('موبایل پیامک'),
            ])
            ->paginated([5, 10])
            ->defaultPaginationPageOption(5);
    }
}
