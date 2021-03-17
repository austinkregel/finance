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

    'plaid' => [
        'env' => env('PLAID_ENV', 'sandbox'),
        'secret_key' => env('PLAID_SECRET', ''),
        'client_id' => env('PLAID_CLIENT_ID', ''),
        'client_name' => env('APP_NAME'),
        'language' => env('PLAID_LANGUAGE', 'en'),
        'country_codes' => explode(',', env('PLAID_COUNTRY_CODES', 'US')),
        'products' => ['transactions'],
    ],

    'slack_webhook_url' => env('SLACK_WEBHOOK_URL'),
    'discord' => [
        'token' => env('DISCORD_TOKEN'),
    ],
];
