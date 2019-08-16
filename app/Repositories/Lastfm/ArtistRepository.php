<?php

namespace App\Repositories\Lastfm;

use App\Models\Artist;
use App\Models\Service;
use Illuminate\Support\Arr;

class ArtistRepository
{
    public function create(array $attributes, array $overrides = []): Artist
    {
        $artist = Artist::firstOrNew(['name' => $attributes['name']]);

        if (Arr::has($attributes, 'mbid') && $attributes['mbid'] != '') {
            $artist->mbid = $attributes['mbid'];
        }

        collect($overrides)->each(function ($override, $key) use ($artist) {
            $artist->$key = ['lastfm' => (int) $override];
        });

        $artist->save();

        return $artist;
    }

    public function addService(Artist $artist, array $attributes): self
    {
        /** @var Service $service */
        $service = $artist->services()->firstOrNew([
            'provider_id' => 1,
            'name' => $artist->name,
        ]);

        if (Arr::has($attributes, 'mbid') && $attributes['mbid'] != '') {
            $service->internal_id = $attributes['mbid'];
        }

        $service->web_url = $attributes['url'];
        $service->save();

        return $this;
    }
}
