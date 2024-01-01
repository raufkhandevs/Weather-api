### Weather API

#### Overview

 Laravel PHP 8.1 and Laravel 10.x based internal API for weather and air quality. The API provides current weather and air quality data for a user-specified city and state. The data is sourced from Airvisual and Weatherstack APIs.

#### API Endpoints

1. **GET /weather**
   - Accepts query parameters: `city` and `state` (US only).
   - Integrates with Airvisual and Weatherstack for air quality and weather data.
   - Returns JSON response with:
     - `query`: User-specified city and state.
     - `temperature`: Temperature from Weatherstack.
     - `temperature_unit`: Always `°F` (Fahrenheit).
     - `weather_description`: Weather description from Weatherstack.
     - `humidity_percent`: Humidity percentage from Weatherstack.
     - `air_quality_rating`: Air quality rating interpreted from Airvisual based on US EPA standards.

#### Testing

- Includes automated unit tests using PHPUnit.

#### Optional Enhancement

- Utilizes Laravel Octane for concurrent querying of both APIs to optimize response time.

#### Example Response

```json
{
    "query": {
        "city": "San Francisco",
        "state": "CA"
    },
    "temperature": "68",
    "temperature_unit": "°F",
    "weather_description": "Partly Cloudy",
    "humidity_percent": "72",
    "air_quality_description": "Good"
}
```

#### Used Links

- [Airvisual API Documentation](https://api-docs.iqair.com/#5bc93d6b-d563-43dc-adb9-c266b2e96d4a)
- [Weatherstack API Documentation](https://weatherstack.com/documentation#current_weather)
- [Laravel Documentation](https://laravel.com/docs/10.x/installation)
