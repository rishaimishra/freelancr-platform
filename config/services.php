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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'ceca' => [
        'key_encryption' => env('CECA_KEY_ENCRYPTION'),
        'merchant_id' => env('CECA_MERCHANT_ID'),
        'acquirer_bin' => env('CECA_ACQUIRER_BIN'),
        'terminal_id' => env('CECA_TERMINAL_ID'),
        'test_mode' => env('CECA_TEST_MODE', true),
        'test_url' => 'https://tpv.ceca.es/tpvweb/tpv/compra.action',
        'live_url' => 'https://pgw.ceca.es/tpvweb/tpv/compra.action',
    ],

];
