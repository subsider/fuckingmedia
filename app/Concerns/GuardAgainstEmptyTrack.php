<?php

namespace App\Concerns;

use App\Models\Artist;

trait GuardAgainstEmptyTrack
{
    /**
     * @param Artist $artist
     * @param string $trackName
     * @return Artist|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    private function checkTrack(Artist $artist, string $trackName)
    {
        $track = $artist->tracks()->where('name', $trackName)->first();

        if (! $track) {
            dump("No results for album {$trackName}. Please execute php artisan lastfm:track:info {$trackName} {$artist->name}");
            exit;
        }

        return $track;
    }
}
