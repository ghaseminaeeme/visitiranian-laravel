<?php

namespace App\Filament\Doctor\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'داشبورد';

    protected static ?string $title = 'داشبورد پزشک';
}
