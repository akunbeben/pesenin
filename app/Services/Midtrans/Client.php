<?php

namespace App\Services\Midtrans;

use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Snap;

class Client
{
    public function __construct()
    {
        Config::$clientKey = config('services.midtrans.client_key');
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = app()->isProduction();
    }

    public function snap(array $payload)
    {
        return Snap::getSnapToken($payload);
    }

    public function verify(Request $request): bool
    {
        return $request->signature_key === openssl_digest(
            $request->order_id .
                $request->status_code .
                $request->gross_amount .
                config('services.midtrans.server_key'),
            'sha512'
        );
    }

    public function status(string $orderId)
    {
        // return CoreApi::
    }
}
