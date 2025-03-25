<?php

declare(strict_types=1);

namespace ZaimeaLabs\PDF;

use Illuminate\Support\ServiceProvider;

class PDFServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'pdf');

        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/pdf'),
            __DIR__.'/config/pdf.php'  => config_path('pdf.php'),
        ], 'pdf');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/pdf.php', 'pdf'
        );
    }
}
