<?php

namespace App\Concerns;

use App\Models\Artist;

trait GuardAgainstEmptyArtist
{
    /**
     * @param string $artistName
     * @return bool|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    private function checkArtist(string $artistName)
    {
        $artist = Artist::where('name', $artistName)->first();

        if (! $artist) {
            dump("No results for artist {$artistName}. Please execute php artisan lastfm:artist:info {$artistName}");
            exit;
        }

        return $artist;
    }
}
