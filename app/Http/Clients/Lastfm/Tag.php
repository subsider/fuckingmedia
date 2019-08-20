<?php

namespace App\Http\Clients\Lastfm;

class Tag
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

    public function top()
    {
        $this->client->query = array_merge($this->client->query, [
            'method' => 'tag.gettoptags',
        ]);

        return $this->client;
    }

    public function info(string $tagName)
    {
        $this->client->query = array_merge($this->client->query, [
            'method' => 'tag.getinfo',
            'tag' => $tagName,
        ]);

        return $this->client;
    }
}
