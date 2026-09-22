<?php

namespace App\Providers;

use App\Models\Zapytania;
use App\Models\Zgloszenie;
use App\Policies\ZapytaniaPolicy;
use App\Policies\ZgloszeniePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Zapytania::class => ZapytaniaPolicy::class,
        Zgloszenie::class => ZgloszeniePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Implicitly grant "super-admin" role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }
}
