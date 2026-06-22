<?php

namespace App\Filament\Admin\Resources\ErrorLogResource\Pages;

use App\Filament\Admin\Resources\ErrorLogResource;
use App\Filament\Support\JalaliFormatter;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewErrorLog extends ViewRecord
{
    protected static string $resource = ErrorLogResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('خلاصه')
                    ->schema([
                        Infolists\Components\TextEntry::make('level')
                            ->label('سطح'),
                        Infolists\Components\TextEntry::make('status')
                            ->label('وضعیت'),
                        Infolists\Components\TextEntry::make('occurred_at')
                            ->label('زمان وقوع')
                            ->formatStateUsing(fn ($state) => JalaliFormatter::dateTime($state)),
                        Infolists\Components\TextEntry::make('url')
                            ->label('آدرس'),
                        Infolists\Components\TextEntry::make('message')
                            ->label('پیام')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make('جزئیات فنی')
                    ->schema([
                        Infolists\Components\TextEntry::make('exception_class')
                            ->label('کلاس استثنا'),
                        Infolists\Components\TextEntry::make('file')
                            ->label('فایل'),
                        Infolists\Components\TextEntry::make('line')
                            ->label('خط'),
                        Infolists\Components\TextEntry::make('stack_trace')
                            ->label('Stack Trace')
                            ->columnSpanFull()
                            ->prose(),
                    ])
                    ->collapsed(),
            ]);
    }
}
