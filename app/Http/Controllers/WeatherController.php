<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\WeatherRequest;
use App\Http\Resources\WeatherResource;
use App\Http\Services\API\IqAirService;
use App\Http\Services\API\WeatherStackService;
use Illuminate\Http\JsonResponse;
use Laravel\Octane\Facades\Octane;

class WeatherController extends Controller
{
    /** protected @vars */
    protected WeatherStackService $weatherStackService;
    protected IqAirService $iqAirService;

    /**
     * WeatherController constructor.
     *
     * @param  WeatherStackService  $weatherStackService
     * @param  IqAirService  $iqAirService
     */
    public function __construct(WeatherStackService $weatherStackService, IqAirService $iqAirService)
    {
        $this->weatherStackService = $weatherStackService;
        $this->iqAirService = $iqAirService;
    }

    /**
     * Shows an index page of weather api.
     *
     * @param  WeatherRequest  $request
     * @return response
     */
    public function index(WeatherRequest $request): WeatherResource | JsonResponse
    {
        $state = $request->get('state');
        $city = $request->get('city');

        try {
            // Closures
            [$weatherDetailsClosure, $airDetailsClosure] = [
                fn() => $this->weatherStackService->currentWeatherDetails($state, $city),
                fn() => $this->iqAirService->currentAirDetails($state, $city),
            ];

            if (config('octane.is_enabled')) { // Beta State
                // execute two api calls closures concurrently
                [$weatherDetails, $airDetails] = Octane::concurrently([$weatherDetailsClosure, $airDetailsClosure]);
            } else {
                // directly invoked
                $weatherDetails = $weatherDetailsClosure();
                $airDetails = $airDetailsClosure();
            }
        } catch (\Throwable $th) {
            return $this->generateErrorResponse($th);
        }

        $response = (object) array_merge($weatherDetails, $airDetails);

        return new WeatherResource($response);
    }

    /**
     * generate error response structure
     *
     * @param \Throwable $exception
     * @return JsonResponse
     */
    private function generateErrorResponse(\Throwable $exception): JsonResponse
    {
        return response()->json([
            'status'  => 'ERROR',
            'code'    => $exception->getCode(),
            'message' => $exception->getMessage(),
        ]);
    }
}
