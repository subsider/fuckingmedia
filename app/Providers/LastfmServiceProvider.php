<?php

namespace App\Providers;

use App\Http\Clients\Lastfm\LastfmClient;
use App\Models\Provider;
use App\Repositories\Lastfm\AlbumRepository;
use App\Repositories\Lastfm\ArtistRepository;
use App\Repositories\Lastfm\TrackRepository;
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

        $this->app->bind(ArtistRepository::class, function () {
            return new ArtistRepository(
                cache()->rememberForever('providers.lastfm', function () {
                    return Provider::where('name', 'Lastfm')->first();
                })
            );
        });

        $this->app->bind(AlbumRepository::class, function () {
            return new AlbumRepository(
                cache()->rememberForever('providers.lastfm', function () {
                    return Provider::where('name', 'Lastfm')->first();
                })
            );
        });

        $this->app->bind(TrackRepository::class, function () {
            return new TrackRepository(
                cache()->rememberForever('providers.lastfm', function () {
                    return Provider::where('name', 'Lastfm')->first();
                })
            );
        });
    }
}
