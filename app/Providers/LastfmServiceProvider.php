<?php

namespace App\Providers;

use App\Http\Clients\Lastfm\LastfmClient;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class LastfmServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(LastfmClient::class, function () {
            return new LastfmClient(
                new Client(['base_uri' => config('services.lastfm.base_uri')]),
                config('services.lastfm.api_key')
            );
        });
    }
}
