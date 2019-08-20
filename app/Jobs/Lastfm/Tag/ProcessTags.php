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

class ProcessTags implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param LastfmClient $client
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(LastfmClient $client)
    {
        $results = $client
            ->tag()
            ->top()
            ->get();

        collect($results['toptags']['tag'])
            ->each(function($result) {
                $tag = Tag::firstOrNew([
                    'type' => 'tag',
                    'slug' => Str::slug($result['name']),
                ], [
                    'name' => $result['name'],
                ]);

                if (isset($result['count'])) {
                    $tag->playcount = ['Lastfm' => $result['count']];
                }

                if (isset($result['reach'])) {
                    $tag->listeners = ['Lastfm' => $result['reach']];
                }

                $tag->save();
            });
    }
}
