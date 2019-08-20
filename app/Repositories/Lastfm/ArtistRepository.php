<?php

namespace App\Repositories\Lastfm;

use App\Models\Artist;
use App\Models\Bio;
use App\Models\Provider;
use App\Models\Related;
use App\Models\Service;
use App\Models\Tag;
use App\Models\Taggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ArtistRepository
{
    /**
     * @var array
     */
    public $sizes = [
        'small' => 'icon',
        'medium' => 'avatar',
        'large' => 'thumb',
        'extralarge' => 'cover',
    ];

    /**
     * @var Provider
     */
    private $provider;

    /**
     * ArtistRepository constructor.
     * @param Provider $provider
     */
    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    public function create(array $attributes, array $overrides = []): Artist
    {
        $artist = Artist::firstOrNew(['name' => $attributes['name']]);

        if (Arr::has($attributes, 'mbid') && $attributes['mbid'] != '') {
            $artist->mbid = $attributes['mbid'];
        }

        if (Arr::has($attributes, 'ontour') && $attributes['ontour'] != '') {
            $artist->on_tour = $attributes['ontour'];
        }

        collect($overrides)->each(function ($override, $key) use ($artist) {
            $artist->$key = [$this->provider->name => (int)$override];
        });

        $artist->save();

        return $artist;
    }

    public function addService(Artist $artist, array $attributes): self
    {
        /** @var Service $service */
        $service = $artist->services()->firstOrNew([
            'provider_id' => $this->provider->id,
            'name' => $artist->name,
        ]);

        if (Arr::has($attributes, 'mbid') && $attributes['mbid'] != '') {
            $service->internal_id = $attributes['mbid'];
        }

        if (Arr::has($attributes, 'url') && $attributes['url'] != '') {
            $service->web_url = $attributes['url'];
        }

        $service->save();

        return $this;
    }

    public function addImages(Artist $artist, array $images)
    {
        collect($images)
            ->reject(function ($result) {
                return $result['#text'] == '' ||
                    $result['size'] == 'mega' ||
                    $result['size'] == '';
            })
            ->each(function ($result) use ($artist) {
                $image = $this->provider->images()->updateOrCreate([
                    'type' => $this->sizes[$result['size']],
                    'path' => $result['#text'],
                ]);

                $artist->images()->syncWithoutDetaching($image);
            });

        return $this;
    }

    public function addTrack(Artist $artist, array $attributes, array $overrides = [], int $rank = null)
    {
        $track = $artist->tracks()->firstOrNew([
            'name' => $attributes['name'],
            'artist_name' => $artist->name,
        ]);

        if (Arr::has($attributes, 'mbid') && $attributes['mbid'] != '') {
            $track->mbid = $attributes['mbid'];
        }

        collect($overrides)->each(function ($override, $key) use ($track) {
            $track->$key = [$this->provider->name => (int)$override];
        });

        $track->save();

        if (! $rank) {
            $artist->tracks()->syncWithoutDetaching($track);
        } else {
            try {
                $artist->tracks()->save($track, [
                    'rank' => [$this->provider->name => $rank],
                ]);
            } catch (\Exception $e) {
                $artist->tracks()->updateExistingPivot($track, [
                    'rank' => [$this->provider->name => $rank],
                ]);
            }
        }

        return $track;
    }

    public function addAlbum(Artist $artist, array $attributes)
    {
        $album = $artist->albums()->firstOrNew([
            'name' => $attributes['name'],
            'artist_name' => $artist->name,
        ]);

        if (Arr::has($attributes, 'mbid') && $attributes['mbid'] != '') {
            $album->mbid = $attributes['mbid'];
        }

        if (Arr::has($attributes, 'playcount') && $attributes['playcount'] != '') {
            $album->playcount = [$this->provider->name => (int)$attributes['playcount']];
        }

        if (Arr::has($attributes, 'streamable') && $attributes['streamable'] != '') {
            $album->streamable = [$this->provider->name => (int)$attributes['streamable']];
        }

        $album->save();
        $artist->albums()->syncWithoutDetaching($album);

        return $album;
    }

    public function addRelatedCollection(Artist $artist, array $results)
    {
        collect($results)->each(function ($result) use ($artist) {
            $this->addRelatedItem($artist, $result);
        });

        return $this;
    }

    public function addRelatedItem(Artist $artist, $result)
    {
        $relatedItem = $this->create($result);
        $this->addService($relatedItem, $result)
            ->addImages($relatedItem, $result['image']);

        $this->relate($artist, $relatedItem);
        $this->relate($relatedItem, $artist);

        return $relatedItem;
    }

    public function relate(Model $source, Model $related, float $match = null)
    {
        $related = Related::firstOrNew([
            'source_id' => $source->id,
            'source_type' => get_class($source),
            'related_id' => $related->id,
            'related_type' => get_class($related),
        ]);

        if ($match) {
            $related->match = [$this->provider->name => $match];
        }

        $related->save();

        return $this;
    }

    public function addTags(Artist $artist, array $results)
    {
        collect($results)->each(function ($result) use ($artist) {
            $this->addTag($artist, $result);
        });

        return $this;
    }

    public function addTag(Artist $artist, $result, int $match = null)
    {
        $tag = Tag::firstOrNew([
            'type' => 'tag',
            'slug' => Str::slug($result['name']),
        ], [
            'name' => $result['name'],
        ]);

        $tag->save();

        $taggable = Taggable::firstOrNew([
            'tag_id' => $tag->id,
            'taggable_id' => $artist->id,
            'taggable_type' => get_class($artist),
        ]);

        if ($match) {
            $taggable->match = [$this->provider->name => (int) $result['count']];
        }

        $taggable->url = [$this->provider->name => $result['url']];

        $taggable->save();

        return $this;
    }

    public function addBio(Artist $artist, array $attributes, string $lang = 'en', array $overrides = [])
    {
        if ($attributes) {
            $bio = Bio::firstOrNew([
                'provider_id' => $this->provider->id,
                'model_id' => $artist->id,
                'model_type' => get_class($artist),
            ], $overrides);

            $bio->setTranslation('summary', $lang, $attributes['summary']);
            $bio->setTranslation('content', $lang, $attributes['content']);
            $bio->setTranslation('published_at', $lang, $attributes['published']);

            $bio->save();
        }

        return $this;
    }
}
