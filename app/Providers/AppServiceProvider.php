<?php

namespace App\Providers;

use App\Models\Division;
use App\Models\Question;
use App\Models\Registration;
use App\Models\ScreeningQuestion;
use App\Models\User;
use App\Observers\AdminActivityObserver;
use App\Observers\DivisionObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;

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
        if ($this->app->environment('production') || isset($_SERVER['VERCEL']) || isset($_ENV['VERCEL'])) {
            URL::forceScheme('https');
        }

        Division::observe(AdminActivityObserver::class);
        Division::observe(DivisionObserver::class);
        Question::observe(AdminActivityObserver::class);
        Registration::observe(AdminActivityObserver::class);
        ScreeningQuestion::observe(AdminActivityObserver::class);
        User::observe(AdminActivityObserver::class);

        RateLimiter::for('registration', function (Request $request): Limit {
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}
