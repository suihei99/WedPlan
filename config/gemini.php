<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Gemini API Key
    |--------------------------------------------------------------------------
    |
    | This is the API key used to authenticate requests to the Google Gemini API.
    | You should set this in your .env file as GEMINI_API_KEY
    |
    */
    'api_key' => env('GEMINI_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Default Model
    |--------------------------------------------------------------------------
    |
    | This is the default model to use for API requests.
    |
    */
    'model' => env('GEMINI_MODEL', 'gemini-2.0-flash'),
];
