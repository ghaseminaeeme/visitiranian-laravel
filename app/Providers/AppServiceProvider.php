<?php

namespace App\Providers;

use App\Models\SiteSocialLink;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentView::registerRenderHook(
            'panels::head.end',
            fn (): \Illuminate\Contracts\View\View => view('filament.hooks.rtl'),
        );

        View::composer('layouts.app', function ($view): void {
            $view->with([
                'siteName' => config('visitiranian.site_name'),
                'socialLinks' => SiteSocialLink::query()->active()->ordered()->get(),
            ]);
        });
    }
}
