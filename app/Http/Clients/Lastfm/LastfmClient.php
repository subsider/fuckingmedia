<?php

namespace App\Http\Clients\Lastfm;

use GuzzleHttp\Client;

class LastfmClient
{
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

    public function searchArtist(string $artistName)
    {
        $this->query = array_merge($this->query, [
            'method' => 'artist.search',
            'artist' => $artistName,
            'limit' => 1000,
            'api_key' => $this->apiKey,
            'page' => 1,
        ]);

        $response = $this->http->request('GET', '', [
            'query' => $this->query,
        ]);

        $results = json_decode($response->getBody()->getContents(), true);

        return $results['results']['artistmatches']['artist'];
    }
}
