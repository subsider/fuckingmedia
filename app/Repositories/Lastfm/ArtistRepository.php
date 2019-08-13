<?php

namespace App\Repositories\Lastfm;

use App\Models\Artist;

class ArtistRepository
{
    /**
     * @var Artist
     */
    private $artist;

    /**
     * ArtistRepository constructor.
     * @param Artist $artist
     */
    public function __construct(Artist $artist)
    {
        $this->artist = $artist;
    }

    public function create(array $attributes, array $overrides = [])
    {
        $artist = Artist::firstOrNew([
            'name' => $attributes['name'],
        ]);

        if ($attributes['mbid'] && $attributes['mbid'] != '') {
            $artist->mbid = $attributes['mbid'];
        }

        collect($overrides)->each(function ($override, $key) use ($artist) {
            $artist->$key = ['lastfm' => (int) $override];
        });

        $artist->save();

        return $artist;
    }

    public function addService(Artist $artist, array $attributes)
    {
        $service = $artist->services()->firstOrNew([
            'provider_id' => 1,
        ]);

        $service->web_url = $attributes['url'];

        $service->save();

        return $this;
    }
}
