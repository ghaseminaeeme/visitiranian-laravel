<?php

namespace App\Providers;

use App\Models\SiteSocialLink;
use Filament\Forms\Components\Select;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Filters\SelectFilter;
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
        Select::configureUsing(function (Select $select): void {
            $select->native(false)->searchable();
        });

        SelectFilter::configureUsing(function (SelectFilter $filter): void {
            $filter->searchable();
        });

        FilamentView::registerRenderHook(
            'panels::head.end',
            fn (): \Illuminate\Contracts\View\View => view('filament.hooks.rtl'),
        );

        FilamentView::registerRenderHook(
            'panels::head.end',
            fn (): \Illuminate\Contracts\View\View => view('filament.hooks.auth-styles'),
        );

        FilamentView::registerRenderHook(
            'panels::auth.login.form.before',
            fn (): \Illuminate\Contracts\View\View => view('filament.hooks.auth-before'),
        );

        FilamentView::registerRenderHook(
            'panels::auth.login.form.after',
            fn (): \Illuminate\Contracts\View\View => view('filament.hooks.auth-after'),
        );

        View::composer('layouts.app', function ($view): void {
            $view->with([
                'siteName' => config('visitiranian.site_name'),
                'socialLinks' => SiteSocialLink::query()->active()->ordered()->get(),
            ]);
        });
    }
}
