<?php

namespace App\Http\Services\API;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class IqAirService
{
    /**
     * @var Client
     */
    protected Client $client;

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
     * request air quality details from external api
     *
     * @param string $state
     * @param string $city
     * @return array
     * @throws Exception
     * @throws GuzzleException
     */
    public function currentAirDetails(string $state, string $city): array
    {
        $endpointWithQuery = $this->getBaseUri() . "city?city=" . urlencode($city) . "&state=" . urlencode($state) . "&country=USA&key=" . $this->getKey();
        $response = $this->makeRequest('GET', $endpointWithQuery);

        if ($response->getStatusCode() != 200) {
            throw new Exception($response->getReasonPhrase(), $response->getStatusCode());
        }

        $data = json_decode($response->getBody()->getContents(), true);

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
        $rating = $data['data']['current']['pollution']['aqius'];
        return [
            'air_quality_rating'      => $rating,
            'air_quality_description' => $this->getLabel($rating),
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
    public function makeRequest(string $method, string $uri, array $options = []): ResponseInterface
    {
        return $this->client->request($method, $uri, $options);
    }

    /**
     * @return string
     */
    private function getBaseUri(): string
    {
        return config('iq_air.base_url');
    }

    /**
     * @return string
     */
    private function getKey(): string
    {
        return config('iq_air.api_key');
    }

    /**
     * label according to Air Quality Index (AQI) Basics
     *
     * @param [type] $level
     * @return string
     */
    public function getLabel($level): string
    {
        $status = 'N/A';
        if (0 <= $level && $level <= 50) {
            $status = 'Good';
        } elseif (51 <= $level && $level <= 100) {
            $status = 'Moderate';
        } elseif (101 <= $level && $level <= 150) {
            $status = 'Unhealthy for Sensitive Groups';
        } elseif (151 <= $level && $level <= 200) {
            $status = 'Unhealthy';
        } elseif (201 <= $level && $level <= 300) {
            $status = 'Very Unhealthy';
        } elseif (301 <= $level) {
            $status = 'Hazardous';
        }

        return $status;
    }
}
