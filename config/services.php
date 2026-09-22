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
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    // Token, którym HRM (crm_mkl) uwierzytelnia się do API klientów.
    'hrm' => [
        'token' => env('HRM_API_TOKEN'),
    ],

    // Handoff HRM <-> CRM (SSO-lite). Sekret ten sam w obu aplikacjach.
    'sso' => [
        'secret' => env('SSO_SECRET', ''),
        'hrm_url' => env('SSO_HRM_URL'),
        'crm_url' => env('SSO_CRM_URL'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

];
