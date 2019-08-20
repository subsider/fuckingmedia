<?php

namespace App\Http\Clients\Lastfm;

class Album
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

    public function search(string $albumName)
    {
        $this->client->query = array_merge($this->client->query, [
            'method' => 'album.search',
            'album' => $albumName,
        ]);

        return $this->client;
    }

    public function info(string $albumName, string $artistName)
    {
        $this->client->query = array_merge($this->client->query, [
            'method' => 'album.getinfo',
            'album' => $albumName,
            'artist' => $artistName,
        ]);

        return $this->client;
    }

    public function tags(string $albumName, string $artistName)
    {
        $this->client->query = array_merge($this->client->query, [
            'method' => 'album.gettoptags',
            'album' => $albumName,
            'artist' => $artistName,
        ]);

        return $this->client;
    }
}
