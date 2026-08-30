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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'finance' => [
        'url' => rtrim(env('FINANCE_API_URL', 'http://127.0.0.1:8000'), '/'),
        'client_id' => env('FINANCE_CLIENT_ID', ''),
        'key_id' => env('FINANCE_KEY_ID', ''),
        'secret' => env('FINANCE_CLIENT_SECRET', ''),
        'client_name' => env('FINANCE_CLIENT_NAME', 'Web CIO Keuangan'),
    ],

    'xendit' => [
        'secret_key'    => env('XENDIT_SECRET_KEY', ''),
        'webhook_token' => env('XENDIT_WEBHOOK_TOKEN', ''),
        'base_url'      => 'https://api.xendit.co',
        'forwarders'    => [
            [
                'name'   => 'Investor',
                'prefix' => env('XENDIT_FORWARD_INVESTOR_PREFIX', 'INV-'),
                'url'    => env('XENDIT_FORWARD_INVESTOR_URL', ''),
            ],
            [
                'name'   => 'Operasional',
                'prefix' => env('XENDIT_FORWARD_OPERASIONAL_PREFIX', 'OPS-'),
                'url'    => env('XENDIT_FORWARD_OPERASIONAL_URL', ''),
            ],
            [
                'name'   => 'Web Lainnya',
                'prefix' => env('XENDIT_FORWARD_LAINNYA_PREFIX', 'OTHER-'),
                'url'    => env('XENDIT_FORWARD_LAINNYA_URL', ''),
            ],
        ],
    ],

    'mekari_qontak' => [
        'token'                  => env('MEKARI_QONTAK_TOKEN', ''),
        'channel_id'             => env('MEKARI_QONTAK_CHANNEL_ID', ''),
        'template_id_pengajuan'  => env('MEKARI_QONTAK_TEMPLATE_ID_PENGAJUAN', ''),
        'template_id_approval'   => env('MEKARI_QONTAK_TEMPLATE_ID_APPROVAL', ''),
        'template_id_rejection'  => env('MEKARI_QONTAK_TEMPLATE_ID_REJECTION', ''),
        'template_id_gaji'       => env('MEKARI_QONTAK_TEMPLATE_ID_GAJI', ''),
        'template_id_otp'        => env('MEKARI_QONTAK_TEMPLATE_ID_OTP', ''),
        'admin_phone'            => env('MEKARI_QONTAK_ADMIN_PHONE', '6285324780031'),
        'base_url'               => env('MEKARI_QONTAK_BASE_URL', 'https://service-chat.qontak.com/api/open/v1'),
    ],

];
