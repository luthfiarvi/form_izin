<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use App\Models\FormIzin;
use App\Observers\FormIzinObserver;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

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
        RateLimiter::for('login', function (Request $request) {
            $key = (string) $request->input('email').'|'.$request->ip();
            return [Limit::perMinute(5)->by($key)];
        });

        // Register model observers
        FormIzin::observe(FormIzinObserver::class);

        // Gates for admin & user management
        Gate::define('admin-only', function (User $user) {
            return ($user->role ?? null) === 'admin' || (bool) $user->is_kepala_kepegawaian === true;
        });

        Gate::define('manage-users', function (User $user) {
            return Gate::forUser($user)->allows('admin-only');
        });

        Gate::define('delete-user', function (User $user, User $target) {
            return $user->id !== $target->id;
        });
    }
}
