<?php

namespace App\Library;

use GuzzleHttp\Client;

class Youtube
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var string
     */
    private $key;

    public const API_URI = 'https://www.googleapis.com/youtube/v3/';
    /**
     * @var callable
     */
    private $failed;
    /**
     * @var callable
     */
    private $succeed;

    private $requestOptions = [];

    public function __construct(Client $client, $key = null)
    {
        $this->client = $client;
        $this->key = $key;
    }

    public function searchVideo($query, $limit = 10, $params = [])
    {
        return $this->request('search', array_merge([
            'part'       => 'snippet',
            'type'       => 'video',
            'maxResults' => $limit,
            'q'          => $query
        ], $params));
    }

    /**
     * @param $id
     * @return YoutubeDetail
     */
    public function getVideoDetail($id)
    {
        return new YoutubeDetail($this->request('videos', ['part' => 'contentDetails,snippet', 'id' => $id]));
    }

    public function request($method, $params = [], $options = [])
    {
        $options = array_merge($this->requestOptions, $options);
        $options['query']['key'] = $options['query']['key'] ?? $this->key;
        $options['query'] = array_merge($options['query'], $params);

        $response = $this->client->get(static::API_URI . ltrim($method, '/'), $options);

        if (is_callable($this->succeed) && $response->getStatusCode() === 200) {
            return call_user_func($this->succeed, $response, $options);
        }
        if (is_callable($this->failed) && $response->getStatusCode() !== 200) {
            return call_user_func($this->failed, $response, $options);
        }

        return json_decode($response->getBody()->getContents(), true) ?? [];
    }

    public function setRequestOptions(array $options)
    {
        $this->requestOptions = $options;
        return $this;
    }

    public function bindRequestIp($ip)
    {
        if (!empty($ip)) {
            $this->requestOptions['curl'] = array_merge($this->requestOptions['curl'] ?? [], [
                CURLOPT_INTERFACE => $ip
            ]);
        }
        return $this;
    }

    /**
     * $callback(ResponseInterface $response,array $options)
     */
    public function whenFailed($callback)
    {
        $this->failed = $callback;
        return $this;
    }

    /**
     * $callback(ResponseInterface $response,array $options)
     */
    public function whenSucceed($callback)
    {
        $this->succeed = $callback;
        return $this;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

}
