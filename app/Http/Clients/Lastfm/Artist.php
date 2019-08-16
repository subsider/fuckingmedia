<?php

namespace App\Http\Clients\Lastfm;

class Artist
{
    /**
     * @var LastfmClient
     */
    private $client;

    /**
     * Artist constructor.
     * @param LastfmClient $client
     */
    public function __construct(LastfmClient $client)
    {
        $this->client = $client;
    }

    public function search(string $artistName)
    {
        $this->client->query = array_merge($this->client->query, [
            'method' => 'artist.search',
            'artist' => $artistName,
        ]);

        return $this->client;
    }

    public function info(string $artistName)
    {
        $this->client->query = array_merge($this->client->query, [
            'method' => 'artist.getinfo',
            'artist' => $artistName,
        ]);

        return $this->client;
    }
}
