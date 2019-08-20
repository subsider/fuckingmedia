<?php

namespace App\Http\Clients\Lastfm;

class Track
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

    public function search(string $trackName, string $artistName = '')
    {
        $this->client->query = array_merge($this->client->query, [
            'method' => 'track.search',
            'track' => $trackName,
            'artist' => $artistName,
        ]);

        return $this->client;
    }

    public function info(string $trackName, string $artistName, string $lang)
    {
        $this->client->query = array_merge($this->client->query, [
            'method' => 'track.getinfo',
            'track' => $trackName,
            'artist' => $artistName,
            'lang' => $lang,
        ]);

        return $this->client;
    }

    public function related(string $trackName, string $artistName)
    {
        $this->client->query = array_merge($this->client->query, [
            'method' => 'track.getsimilar',
            'track' => $trackName,
            'artist' => $artistName,
        ]);

        return $this->client;
    }

    public function tags(string $trackName, string $artistName)
    {
        $this->client->query = array_merge($this->client->query, [
            'method' => 'track.gettoptags',
            'track' => $trackName,
            'artist' => $artistName,
        ]);

        return $this->client;
    }
}
