<?php

// SSLCommerz configuration

$apiDomain = env('SSLCOMMERZ_SANDBOX', true) ? "https://sandbox.sslcommerz.com" : "https://securepay.sslcommerz.com";
return [
    'apiCredentials' => [
        'store_id' => env('SSLCOMMERZ_STORE_ID'),
        'store_password' => env('SSLCOMMERZ_STORE_PASSWORD'),
    ],
    'apiUrl' => [
        'make_payment' => "/gwprocess/v4/api.php",
        'transaction_status' => "/validator/api/merchantTransIDvalidationAPI.php",
        'order_validate' => "/validator/api/validationserverAPI.php",
        'refund_payment' => "/validator/api/merchantTransIDvalidationAPI.php",
        'refund_status' => "/validator/api/merchantTransIDvalidationAPI.php",
    ],
    'apiDomain' => $apiDomain,
    'connect_from_localhost' => env('IS_LOCALHOST', true), // For Sandbox, use "true", For Live, use "false"
'success_url' => env('SSLCOMMERZ_SUCCESS_URL', '/payment/success'),
'failed_url' => env('SSLCOMMERZ_FAIL_URL', '/payment/fail'),
'cancel_url' => env('SSLCOMMERZ_CANCEL_URL', '/payment/cancel'),
'ipn_url' => env('SSLCOMMERZ_IPN_URL', '/payment/ipn'),
];