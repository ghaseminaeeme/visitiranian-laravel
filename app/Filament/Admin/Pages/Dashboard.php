<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\ExpiringDoctorsWidget;
use App\Filament\Admin\Widgets\StatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'داشبورد';

    protected static ?string $title = 'داشبورد';

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            ExpiringDoctorsWidget::class,
        ];
    }
}
