<?php

namespace App\Providers;

use Bitbucket\Client;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app()->singleton(Client::class, function () {
            $bitbucket = new Client;

            $bitbucket->authenticate(
                Client::AUTH_HTTP_PASSWORD,
                config('services.bitbucket.username'),
                config('services.bitbucket.password')
            );

            return $bitbucket;
        });
    }
}
