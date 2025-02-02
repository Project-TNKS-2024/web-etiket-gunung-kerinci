<?php

namespace App\Providers;

use App\Models\destinasi as ModelDestinasi;
use App\Models\setting;
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

        View()->composer('homepage.template.footer', function ($view) {
            $ffacabook = setting::where('id', '0000facebook')->first();
            $finstagram = setting::where('id', '0000instagram')->first();
            $fyoutube = setting::where('id', '0000youtube')->first();
            $fweb = setting::where('id', '0000website')->first();
            $view->with([
                'ffacebook' => $ffacabook,
                'finstagram' => $finstagram,
                'fyoutube' => $fyoutube,
                'fweb' => $fweb
            ]);
        });
    }
}
