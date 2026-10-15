<?php

namespace App\Http\Controllers\helper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Midtrans\Transaction;

class MidtransController extends HelperController
{
    public function __construct()
    {
        // Kredensial diambil dari .env (lihat config/midtrans.php)
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        // Set true untuk Production Environment (transaksi nyata)
        \Midtrans\Config::$isProduction = (bool) config('midtrans.is_production');
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = (bool) config('midtrans.is_sanitized');
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = (bool) config('midtrans.is_3ds');
    }

    public function checkPaymentStatus($orderId)
    {
        try {
            $status = Transaction::status($orderId);
            return [
                'success' => true,
                'message' => 'Payment status retrieved successfully',
                'data' => json_decode(json_encode($status), true),
            ];
        } catch (\Exception $e) {
            $errorData = json_decode(substr($e->getMessage(), strpos($e->getMessage(), '{')), true);
            return [
                'success' => false,
                'message' => 'Failed to retrieve payment status: ' . $errorData['status_message'],
                'data' => [
                    'status_code' => $errorData['status_code'] ?? 500,
                    'id' => $errorData['id'] ?? null,
                ],
            ];
        }
    }
    // function generate snap token
    public function generateSnapToken($params)
    {
        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            return [
                'success' => true,
                'message' => 'Snap token generated successfully',
                'data' => $snapToken,
            ];
        } catch (\Exception $e) {
            // throw new \Exception('Failed to generate Snap token: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to generate Snap token: ' . $e->getMessage(),
                'data' => null,
            ];
        }
    }
}
