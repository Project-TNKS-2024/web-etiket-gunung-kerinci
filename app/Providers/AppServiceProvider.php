<?php

namespace App\Providers;

use App\Models\destinasi as ModelDestinasi;
use App\Models\setting;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Blade;
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

        //  mengerim email ke user ketika login manual
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verify Email Address')
                ->line('Click the button below to verify your email address.')
                ->action('Verify Email Address', $url);
        });


        // membuat pengecekan admin bertanggungawab atas destinasi
        Blade::directive('canDestinasi', function ($destinasiId) {
            return "<?php if(auth()->check() && auth()->user()->destinasis->contains('id', $destinasiId)): ?>";
        });

        Blade::directive('endCanDestinasi', function () {
            return "<?php endif; ?>";
        });
    }
}
