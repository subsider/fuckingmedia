<?php

namespace App\Http\Clients\Discogs;

class Album
{
    /**
     * @var DiscogsClient
     */
    private $client;

    /**
     * Album constructor.
     * @param DiscogsClient $client
     */
    public function __construct(DiscogsClient $client)
    {
        $this->client = $client;
    }

    public function search(string $albumName)
    {
        $this->client->url = 'database/search';

        $this->client->query = array_merge($this->client->query, [
            'q' => $albumName,
            'type' => 'release',
        ]);

        return $this->client;
    }

    public function info(string $id)
    {
        $this->client->url = "releases/{$id}";

        return $this->client;
    }
}
