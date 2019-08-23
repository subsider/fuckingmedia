<?php

namespace App\Repositories\Discogs;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Collaboration;
use App\Models\Companiable;
use App\Models\Company;
use App\Models\Image;
use App\Models\Provider;
use App\Models\Tag;
use App\Models\Taggable;
use Illuminate\Support\Str;

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

    public function addImage(Album $album, $result, $type, $alias = null)
    {
        if ($alias == null) {
            $alias = $type;
        }

        if ($result[$type] != '') {
            $image = Image::updateOrCreate([
                'type' => $alias,
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

            $artist->tracks()->syncWithoutDetaching($track);
        }

        return $this;
    }

    public function addFormats(Album $album, $result)
    {
        if (isset($result['format'])) {
            $formats = is_array($result['format'])
                ? $result['format']
                : explode(', ', $result['format']);

            collect($formats)->each(function ($format) use ($album) {
                $album->formats()->updateOrCreate([
                    'name' => $format,
                ]);
            });
        }

        return $this;
    }

    public function addLabels(Album $album, $result, $overrides = [])
    {
        if (isset($result['label'])) {
            $this->collectAndSyncLabels($album, $result['label'], $overrides);
        }

        return $this;
    }

    public function addLabel(Album $album, $result, array $overrides = [])
    {
        if (isset($result['label'])) {
            $labels = explode(', ', $result['label']);

            $this->collectAndSyncLabels($album, $labels, $overrides);
        }

        return $this;
    }

    public function collectAndSyncLabels(Album $album, $labels, $overrides)
    {
        collect($labels)->each(function ($labelName) use ($album, $overrides) {
            $label = Company::firstOrCreate([
                'name' => $labelName,
            ]);

            $this->syncLabel($album, $label);
        });

        return $this;
    }

    public function syncLabel($album, $label)
    {
        $companiable = Companiable::firstOrNew([
            'company_id' => $label->id,
            'companiable_id' => $album->id,
            'companiable_type' => get_class($album),
            'role' => 'label',
        ]);

        $companiable->save();

        return $this;
    }

    public function addStyles(Album $album, array $results)
    {
        return $this->addTags($album, $results, 'style');
    }

    public function addGenres(Album $album, array $results)
    {
        return $this->addTags($album, $results, 'genre');
    }

    public function addTags(Album $album, array $results, $type = 'tag')
    {
        if (isset($results[$type])) {
            collect($results[$type])->each(function ($result) use ($album, $type) {
                $this->addTag($album, $result, $type);
            });

            return $this;
        }
    }

    public function addTag(Album $album, $result, $type = 'tag')
    {
        $tag = Tag::firstOrNew([
            'type' => $type,
            'slug' => Str::slug($result),
        ], [
            'name' => $result,
        ]);

        $tag->save();

        $taggable = Taggable::firstOrNew([
            'tag_id' => $tag->id,
            'taggable_id' => $album->id,
            'taggable_type' => get_class($album),
        ]);

        $taggable->save();

        return $this;
    }

    public function addBarcodes(Album $album, $result)
    {
        if ($result['barcode']) {
            collect($result['barcode'])->each(function ($code) use ($album) {
                $album->barcodes()->updateOrCreate(['code' => $code]);
            });
        }

        return $this;
    }
}
