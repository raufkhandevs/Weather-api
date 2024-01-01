<?php

namespace App\Http\Services\API;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class WeatherStackService
{
    /**
     * @var Client
     */
    protected Client $client;

    const UNITS = [
        'm' => 'Metric',
        's' => 'Scientific',
        'f' => 'Fahrenheit',
    ];

    /**
     * WeatherStackService constructor.
     *
     * setup \GuzzleHttp\Client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * request weather details from external api
     *
     * @param string $state
     * @param string $city
     * @return array
     * @throws Exception|GuzzleException
     */
    public function currentWeatherDetails(string $state, string $city): array
    {
        $endpointWithQuery = $this->getBaseUri() . '/current?query=' . urlencode("$state $city") . '&units=f&access_key=' . $this->getKey();
        $response = $this->makeRequest('GET', $endpointWithQuery);

        $data = json_decode($response->getBody()->getContents(), true);

        if (isset($data['success']) && !$data['success']) {
            throw new Exception($data['error']['info'], $data['error']['code']);
        }

        return $this->parseData($data);
    }

    /**
     * parse array from api response
     *
     * @param array $data
     * @return array
     */
    private function parseData(array $data): array
    {
        return [
            'temperature'         => $data['current']['temperature'],
            'temperature_unit'    => self::UNITS[$data['request']['unit']],
            'weather_description' => array_pop($data['current']['weather_descriptions']),
            'humidity_percent'    => $data['current']['humidity'],
        ];
    }

    /**
     * make client request
     *
     * @param string $uri
     * @param string $method
     * @param array $options
     * @return ResponseInterface
     * @throws GuzzleException
     */
    private function makeRequest(string $method, string $uri, array $options = []): ResponseInterface
    {
        return $this->client->request($method, $uri, $options);
    }

    /**
     * @return string
     */
    private function getBaseUri(): string
    {
        return config('weather_stack.base_url');
    }

    /**
     * @return string
     */
    private function getKey(): string
    {
        return config('weather_stack.api_key');
    }
}
