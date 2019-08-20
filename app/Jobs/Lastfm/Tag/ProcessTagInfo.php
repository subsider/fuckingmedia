<?php

namespace App\Jobs\Lastfm\Tag;

use App\Http\Clients\Lastfm\LastfmClient;
use App\Models\Tag;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Str;

class ProcessTagInfo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $tagName;

    /**
     * Create a new job instance.
     *
     * @param string $tagName
     */
    public function __construct(string $tagName)
    {
        $this->tagName = $tagName;
    }

    /**
     * @param LastfmClient $client
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(LastfmClient $client)
    {
        $result = $client
            ->tag()
            ->info($this->tagName)
            ->get()['tag'];

        $tag = Tag::firstOrNew([
            'type' => 'tag',
            'slug' => Str::slug($result['name']),
        ], [
            'name' => $result['name'],
        ]);

        if (isset($result['total'])) {
            $tag->playcount = ['Lastfm' => $result['total']];
        }

        if (isset($result['reach'])) {
            $tag->listeners = ['Lastfm' => $result['reach']];
        }

        $tag->save();

        $tag->bios()->updateOrCreate([
            'provider_id' => 1,
        ], [
            'summary' => $result['wiki']['summary'],
            'content' => $result['wiki']['content'],
        ]);
    }
}
