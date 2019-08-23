<?php

namespace App\Http\Clients\Discogs;

class Label
{
    /**
     * @var DiscogsClient
     */
    private $client;

    /**
     * Label constructor.
     * @param DiscogsClient $client
     */
    public function __construct(DiscogsClient $client)
    {
        $this->client = $client;
    }

    public function search(string $labelName)
    {
        $this->client->url = 'database/search';

        $this->client->query = array_merge($this->client->query, [
            'q' => $labelName,
            'type' => 'label',
        ]);

        return $this->client;
    }

    public function info(string $id)
    {
        $this->client->url = "labels/{$id}";

        return $this->client;
    }

    public function albums(string $id)
    {
        $this->client->url = "labels/{$id}/releases";

        return $this->client;
    }
}
