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
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    'scb' => [
        'deposit' => [
            'endpoint' => env('SCB_DEPOSIT_ENDPOINT'),
            'headers' => [
                'apikey' => env('SCB_DEPOSIT_HEADER_APIKEY'),
                'apisecret' => env('SCB_DEPOSIT_HEADER_APISECRET'),
                'requestUID' => env('SCB_DEPOSIT_HEADER_REQUEST_UID','UUID'),
                'resourceOwnerID' => env('SCB_DEPOSIT_HEADER_OWNER_ID'),

            ]
        ]
    ]

];
