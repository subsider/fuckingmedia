<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    'lastfm' => [
        'base_uri' => env('LASTFM_BASE_URI', 'http://ws.audioscrobbler.com/2.0/'),
        'api_key' => env('LASTFM_API_KEY'),
    ],

    'discogs' => [
        'base_uri' => env('DISCOGS_BASE_URI', 'https://api.discogs.com/'),
        'web_uri' => 'https://discogs.com',
        'user_agent' => 'fucking-media/1.0.0 +https://github.com/subsider/fuckingmedia',
        'api_key' => env('DISCOGS_API_KEY'),
        'api_secret' => env('DISCOGS_API_SECRET'),
    ],
];
