<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WeatherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'query'                   => [
                'city'  => $request->city,
                'state' => $request->state,
            ],
            'temperature'             => $this->temperature,
            'temperature_unit'        => $this->temperature_unit,
            'weather_description'     => $this->weather_description,
            'humidity_percent'        => $this->humidity_percent,
            'air_quality_rating'      => $this->air_quality_rating,
            'air_quality_description' => $this->air_quality_description,
        ];
    }
}
