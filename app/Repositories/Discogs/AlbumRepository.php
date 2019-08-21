<?php

namespace App\Repositories\Discogs;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Collaboration;
use App\Models\Companiable;
use App\Models\Company;
use App\Models\Image;
use App\Models\Provider;

class AlbumRepository
{
    /**
     * @var Provider
     */
    private $provider;

    /**
     * AlbumRepository constructor.
     * @param Provider $provider
     */
    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    public function addService(Artist $artist, Album $album, $result)
    {
        $album->services()->updateOrCreate([
            'provider_id' => $this->provider->id,
            'name' => $artist->name . ' - ' . $album->name,
            'internal_id' => $result['id'],
            'api_url' => $result['resource_url'],
        ]);

        return $this;
    }

    public function addImage(Album $album, $result, $type)
    {
        if ($result[$type] != '') {
            $image = Image::updateOrCreate([
                'type' => $type,
                'provider_id' => $this->provider->id,
                'path' => $result[$type],
            ]);

            $album->images()->syncWithoutDetaching($image);
        }

        return $this;
    }

    public function addCollaboration(Artist $artist, Artist $albumArtist, Album $album, $result)
    {
        Collaboration::updateOrCreate([
            'artist_id' => $artist->id,
            'model_id' => $albumArtist->id,
            'model_type' => get_class($albumArtist),
            'context_id' => $album->id,
            'context_type' => get_class($album),
            'role' => $result['role'],
        ]);

        return $this;
    }

    public function addTrackInfo(Artist $artist, Album $album, $result)
    {
        if (isset($result['trackinfo'])) {
            $track = $artist->tracks()->firstOrCreate([
                'artist_name' => $artist->name,
                'name' => $result['trackinfo'],
            ]);

            try {
                $album->tracks()->save($track, ['track_artist_id' => $artist->id]);
            } catch (\Exception $e) {
                $album->tracks()->updateExistingPivot($track, ['track_artist_id' => $artist->id]);
            }
        }

        return $this;
    }

    public function addFormats(Album $album, $result)
    {
        if (isset($result['format'])) {
            $formats = explode(', ', $result['format']);

            collect($formats)->each(function ($format) use ($album) {
                $album->formats()->updateOrCreate([
                    'name' => $format,
                ]);
            });
        }

        return $this;
    }

    public function addLabel(Album $album, $result)
    {
        if (isset($result['label'])) {
            $labels = explode(', ', $result['label']);

            collect($labels)->each(function ($labelName) use ($album) {
                $label = Company::firstOrCreate([
                    'name' => $labelName,
                ]);

                Companiable::updateOrCreate([
                    'company_id' => $label->id,
                    'companiable_id' => $album->id,
                    'companiable_type' => get_class($album),
                    'role' => 'label',
                ]);
            });
        }

        return $this;
    }
}
