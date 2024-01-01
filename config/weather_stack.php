<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Weather Stack api base URL
    |--------------------------------------------------------------------------
    |
    | This URL is going to be used to make api request from Weather Stack
    | third party platform
    |
    */
    'base_url' => env('WEATHER_STACK_BASE_URL', ''),


     /*
    |--------------------------------------------------------------------------
    | API key for access
    |--------------------------------------------------------------------------
    |
    | Your API Access Key
    |
    */
    'api_key' => env('WEATHER_STACK_API_KEY', ''),
];
