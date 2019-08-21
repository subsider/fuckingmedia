<?php

namespace App\Providers;

use App\Http\Clients\Discogs\DiscogsClient;
use App\Models\Provider;
use App\Repositories\Discogs\AlbumRepository;
use App\Repositories\Discogs\ArtistRepository;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class DiscogsServiceProvider extends ServiceProvider
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
        $this->app->bind(DiscogsClient::class, function () {
            return new DiscogsClient(
                new Client([
                    'base_uri' => config('services.discogs.base_uri'),
                    'headers' => [
                        'User-Agent' => config('services.discogs.user_agent'),
                    ]
                ]),
                config('services.discogs.api_key'),
                config('services.discogs.api_secret')
            );
        });

        $this->app->bind(ArtistRepository::class, function () {
            return new ArtistRepository(
                cache()->rememberForever('providers.discogs', function () {
                    return Provider::where('name', 'Discogs')->first();
                })
            );
        });

        $this->app->bind(AlbumRepository::class, function () {
            return new AlbumRepository(
                cache()->rememberForever('providers.discogs', function () {
                    return Provider::where('name', 'Discogs')->first();
                })
            );
        });
    }
}
