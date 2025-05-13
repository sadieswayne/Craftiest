<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Gemini API Key
    |--------------------------------------------------------------------------
    |
    | Here you may specify your Gemini API Key and organization. This will be
    | used to authenticate with the Gemini API - you can find your API key
    | on Google AI Studio, at https://makersuite.google.com.
    */

    'api_key' => env('GEMINI_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout may be used to specify the maximum number of seconds to wait
    | for a response. By default, the client will time out after 30 seconds.
    */

    'request_timeout' => env('GEMINI_REQUEST_TIMEOUT', 30),
];
