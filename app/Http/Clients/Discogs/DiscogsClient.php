<?php

namespace App\Http\Clients\Discogs;

use GuzzleHttp\Client;

class DiscogsClient
{
    const MAX_PAGE = 4;

    /**
     * @var Client
     */
    private $http;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $apiSecret;

    /**
     * @var array
     */
    public $query = [];

    /**
     * @var string
     */
    public $url = '';

    /**
     * DiscogsClient constructor.
     * @param Client $http
     * @param string $apiKey
     * @param string $apiSecret
     */
    public function __construct(Client $http, string $apiKey, string $apiSecret)
    {
        $this->http = $http;
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    public function artist()
    {
        return new Artist($this);
    }

    public function limit(int $limit)
    {
        $this->query = array_merge($this->query, [
            'per_page' => $limit,
        ]);

        return $this;
    }

    public function page(int $page)
    {
        if ($page > self::MAX_PAGE) {
            $page = self::MAX_PAGE;
        }

        $this->query = array_merge($this->query, [
            'page' => $page,
        ]);

        return $this;
    }

    /**
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get()
    {
        $this->query = array_merge($this->query, [
            'key' => $this->apiKey,
            'secret' => $this->apiSecret,
        ]);

        $response = $this->http->request('GET', $this->url, [
            'query' => $this->query,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
