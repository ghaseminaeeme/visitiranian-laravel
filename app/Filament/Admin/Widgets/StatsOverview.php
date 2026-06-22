<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorPhoto;
use App\Models\SupportTicket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $expiringDays = config('visitiranian.expiring_doctors_days', 30);

        return [
            Stat::make('پزشکان فعال', Doctor::query()->where('is_active', true)->count())
                ->description('کل پزشکان فعال')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            Stat::make('پزشکان منتشرشده', Doctor::query()->published()->count())
                ->description('قابل مشاهده در سایت')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('primary'),

            Stat::make('نوبت‌های امروز', Appointment::query()
                ->whereDate('starts_at', today())
                ->where('status', 'confirmed')
                ->count())
                ->description('نوبت‌های تأییدشده')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('عکس‌های در انتظار', DoctorPhoto::query()->pending()->count())
                ->description('نیاز به بررسی')
                ->descriptionIcon('heroicon-m-photo')
                ->color('warning'),

            Stat::make('در حال انقضا', Doctor::query()
                ->where('is_active', true)
                ->whereNotNull('expires_at')
                ->where('expires_at', '<=', now()->addDays($expiringDays))
                ->count())
                ->description("تا {$expiringDays} روز آینده")
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),

            Stat::make('تیکت‌های باز', SupportTicket::query()
                ->whereIn('status', ['sent', 'in_progress'])
                ->count())
                ->description('در انتظار پاسخ')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('gray'),
        ];
    }
}
