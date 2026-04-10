<?php

return [
    // Base URL for payment gateway Snap redirect (e.g., Midtrans Snap redirect_url base)
    // Leave null to fallback to internal demo page route('donation.payment').
    'snap_redirect_base' => env('PAYMENT_SNAP_REDIRECT_BASE', null),
    'midtrans' => [
        'merchant_id' => env('MIDTRANS_MERCHANT_ID', ''),
        'server_key' => env('MIDTRANS_SERVER_KEY', ''),
        'client_key' => env('MIDTRANS_CLIENT_KEY', ''),
        'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
        'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
        'is_3ds' => env('MIDTRANS_IS_3DS', true),
    ],
];

