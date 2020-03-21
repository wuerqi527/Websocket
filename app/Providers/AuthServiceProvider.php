<?php

namespace App\Providers;

use Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Extensions\Auth\AdvTokenGuard;
use App\Extensions\Auth\HttpBasicGuard;
use Laravel\Horizon\Horizon;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // BigApiToken
        Auth::extend('adv_token', function ($app, $name, array $config) {
            return new AdvTokenGuard(
                Auth::createUserProvider($config['provider']),
                $app['request']
            );
        });

        // 简单版 Basic Auth
        Auth::extend('http_basic', function ($app, $name, array $config) {
            return new HttpBasicGuard($app['request']);
        });

        // Horizon 权限交由 HttpBasicGuard 来守护
        if (! \App::environment('local')) {
            Horizon::routeMailNotificationsTo(config('logging.alarm_email'));
            Horizon::auth(function ($request) {
                return true;
            });
        }
    }
}
