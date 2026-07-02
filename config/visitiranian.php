<?php

return [

    'site_name' => env('VISITIRANIAN_SITE_NAME', 'ویزیت ایرانیان'),

    'og_image' => env('VISITIRANIAN_OG_IMAGE'),

    'doctor_placeholder' => 'images/doctor-placeholder.svg',

    'robots_disallow' => [
        '/admin',
        '/login',
        '/appointments',
    ],

    'developer_email' => env('VISITIRANIAN_DEVELOPER_EMAIL'),

    'admin_email' => env('ADMIN_EMAIL', 'admin@example.com'),
    'admin_password' => env('ADMIN_PASSWORD', 'password'),

    'kavenegar' => [
        'api_key' => env('KAVENEGAR_API_KEY'),
        'sender' => env('KAVENEGAR_SENDER', '10004346'),
    ],

    'support' => [
        'email' => env('VISITIRANIAN_SUPPORT_EMAIL'),
        'phone' => env('VISITIRANIAN_SUPPORT_PHONE'),
        'ticket_prefix' => env('VISITIRANIAN_SUPPORT_TICKET_PREFIX', 'VI'),
    ],

    'telegram' => [
        'bot_token' => env('SUPPORT_TELEGRAM_BOT_TOKEN'),
        'chat_id' => env('SUPPORT_TELEGRAM_CHAT_ID'),
    ],

    'bale' => [
        'bot_token' => env('SUPPORT_BALE_BOT_TOKEN'),
        'chat_id' => env('SUPPORT_BALE_CHAT_ID'),
    ],

];
