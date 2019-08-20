<?php

namespace App\Concerns;

use App\Models\Artist;

trait GuardAgainstEmptyAlbum
{
    /**
     * @param Artist $artist
     * @param string $albumName
     * @return Artist|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    private function checkAlbum(Artist $artist, string $albumName)
    {
        $album = $artist->albums()->where('name', $albumName)->first();

        if (! $album) {
            dump("No results for album {$albumName}. Please execute php artisan lastfm:album:info {$albumName}");
            exit;
        }

        return $album;
    }
}
