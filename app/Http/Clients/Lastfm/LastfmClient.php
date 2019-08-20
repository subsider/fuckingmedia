<?php

namespace App\Http\Clients\Lastfm;

use GuzzleHttp\Client;

class LastfmClient
{
    const MAX_LIMIT = 1000;

    /**
     * @var Client
     */
    private $http;

    /**
     * @var array
     */
    public $query = [
        'format' => 'json',
    ];

    /**
     * @var string
     */
    private $apiKey;

    /**
     * LastfmClient constructor.
     * @param Client $http
     * @param string $apiKey
     */
    public function __construct(Client $http, string $apiKey)
    {
        $this->http = $http;
        $this->apiKey = $apiKey;
    }

    public function artist()
    {
        return new Artist($this);
    }

    public function album()
    {
        return new Album($this);
    }

    public function track()
    {
        return new Track($this);
    }

    public function tag()
    {
        return new Tag($this);
    }

    public function limit(int $limit)
    {
        if ($limit > self::MAX_LIMIT) {
            $limit = self::MAX_LIMIT;
        }

        $this->query = array_merge($this->query, [
            'limit' => $limit,
        ]);

        return $this;
    }

    public function page(int $page)
    {
        $this->query = array_merge($this->query, [
            'page' => $page,
        ]);

        return $this;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get()
    {
        $this->query = array_merge($this->query, [
            'api_key' => $this->apiKey,
        ]);

        $response = $this->http->request('GET', '', [
            'query' => $this->query,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
