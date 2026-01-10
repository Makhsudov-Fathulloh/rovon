<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('coreAccess', function ($user) {
            return $user->role->title === 'Developer';
        });

        Gate::define('aodAccess', function ($user) {
            return in_array($user->role->title, ['Admin', 'Developer']);
        });

        Gate::define('hasAccess', function ($user) {
            return in_array($user->role->title, ['Admin', 'Manager', 'Developer']);
        });

        Gate::define('fullAccess', function ($user) {
            return in_array($user->role->title, ['Admin', 'Manager', 'Moderator', 'Developer']);
        });

        Gate::define('allAccess', function ($user) {
            return in_array($user->role->title, ['Admin', 'Manager', 'Moderator', 'Seller', 'User', 'Developer']);
        });

        Gate::define('moderatorAccess', function ($user) {
            return $user->role->title === 'Moderator';
        });
    }
}
