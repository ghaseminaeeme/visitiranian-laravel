<?php

namespace App\Filament\Doctor\Pages;

use Filament\Pages\Page;

class DoctorGuidePage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'راهنمای پزشک';

    protected static ?string $title = 'راهنمای استفاده از پنل';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.doctor.pages.doctor-guide';
}
