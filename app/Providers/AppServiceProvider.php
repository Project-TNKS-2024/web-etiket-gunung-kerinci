<?php

namespace App\Providers;

use App\Models\destinasi as ModelDestinasi;
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
        view()->composer('etiket.admin.template.sidebar', function ($view) {
            $destinasi = ModelDestinasi::all();
            $view->with('DataDestinasi', $destinasi);
        });
    }
}
