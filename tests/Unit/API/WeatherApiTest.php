<?php

namespace Tests\Unit\API;

use App\Http\Services\API\IqAirService;
use App\Http\Services\API\WeatherStackService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class WeatherApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Seed Data
     *
     * @var boolean
     */
    protected bool $seed = true;

    /**
     * test validate get request pass
     */
    public function test_validate_get_request_pass(): void
    {
        $response = $this->makeRequest('California', 'Los Angeles');
        $response->assertStatus(ResponseAlias::HTTP_OK);
    }

    /**
     * test validate get request fail
     */
    public function test_validate_get_request_fail(): void
    {
        $response = $this->makeRequest();
        $response->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * test weather api service pass
     * @throws GuzzleException
     */
    public function test_weather_api_service_pass()
    {
        $client = $this->mockClient($this->getMockJson('weather'));

        // Create an instance of WeatherService with the mocked client
        $weatherService = new WeatherStackService($client);

        $data = $weatherService->currentWeatherDetails('California', 'Los Angeles');

        $this->assertArrayHasKey('temperature', $data);
        $this->assertArrayHasKey('temperature_unit', $data);
        $this->assertArrayHasKey('weather_description', $data);
        $this->assertArrayHasKey('humidity_percent', $data);

        $this->assertEquals(66, $data['temperature']);
        $this->assertEquals('Fahrenheit', $data['temperature_unit']);
        $this->assertEquals('Overcast', $data['weather_description']);
        $this->assertEquals(73, $data['humidity_percent']);
    }

    /**
     * test iq air api service pass
     * @throws GuzzleException
     */
    public function test_iq_air_api_service_pass()
    {
        $client = $this->mockClient($this->getMockJson('iq-air'));

        // Create an instance of IqAirService with the mocked client
        $weatherService = new IqAirService($client);

        $data = $weatherService->currentAirDetails('California', 'Los Angeles');

        $this->assertArrayHasKey('air_quality_rating', $data);
        $this->assertArrayHasKey('air_quality_description', $data);

        $this->assertEquals(22, $data['air_quality_rating']);
        $this->assertEquals('Good', $data['air_quality_description']);
    }

    /**
     * make request to endpoint
     *
     * @param string $state
     * @param string $city
     * @return TestResponse
     */
    private function makeRequest(string $state = '', string $city = ''): TestResponse
    {
        return $this->json('GET', "/api/weather?city=$city&state=$state");
    }

    /**
     * Client with the handler stack
     *
     * @param [type] $responseJson
     * @return Client
     */
    private function mockClient($responseJson): Client
    {
        // Create a mock handler
        $mock = new MockHandler([
            new Response(200, [], $responseJson),
        ]);

        // Create a handler stack with the mock handler
        $handlerStack = HandlerStack::create($mock);

        // Create a client with the handler stack
        return new Client(['handler' => $handlerStack]);
    }

    /**
     * read json file
     *
     * @param string $file
     * @return mixed
     */
    private function getMockJson(string $file): mixed
    {
        return file_get_contents(base_path("tests/Unit/API/Mocks/$file.json"));
    }
}
