<?php

namespace App\Http\Clients\Discogs;

class Artist
{
    /**
     * @var DiscogsClient
     */
    private $client;

    /**
     * Artist constructor.
     * @param DiscogsClient $client
     */
    public function __construct(DiscogsClient $client)
    {
        $this->client = $client;
    }

    public function search(string $artistName)
    {
        $this->client->url = 'database/search';

        $this->client->query = array_merge($this->client->query, [
            'q' => $artistName,
            'type' => 'artist',
        ]);

        return $this->client;
    }

    public function info(string $id)
    {
        $this->client->url = "artists/{$id}";

        return $this->client;
    }
}
