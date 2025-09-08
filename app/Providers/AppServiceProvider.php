<?php

namespace App\Providers;

use Filament\Schemas\Components\Section;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
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
        Section::configureUsing(function (Section $section): void {
            $section
                ->columnSpanFull()
                ->columns(2);
        });

        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_BEFORE,
            fn (): string => ucfirst(auth()->user()->name),
        );
    }
}
