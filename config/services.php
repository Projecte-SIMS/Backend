<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Integración con el Microservicio IoT (FastAPI)
    'iot' => [
        'url' => env('IOT_MICROSERVICE_URL', 'http://localhost:8001'),
        'api_key' => env('IOT_API_KEY', 'MACMECMIC'),
    ],

    // Integración con el Chatbot de IA (Open WebUI)
    'open_webui' => [
        'api_key' => env('OPEN_WEBUI_API_KEY'),
        'base_url' => env('OPEN_WEBUI_BASE_URL', 'https://api-ia.daw2.iesmontsia.org:3000/api'),
        'model' => env('OPEN_WEBUI_MODEL', 'llm-per-a-alumnat'),
    ],

    'stripe' => [
        'secret' => env('STRIPE_SECRET_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'default_price_id' => env('STRIPE_DEFAULT_PRICE_ID'),
        'base_url' => env('STRIPE_API_BASE_URL', 'https://api.stripe.com/v1'),
        'demo_mode' => env('STRIPE_DEMO_MODE', false),
    ],

];
