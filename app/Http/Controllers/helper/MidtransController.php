<?php

namespace App\Http\Controllers\helper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Midtrans\Transaction;

class MidtransController extends HelperController
{
    public function __construct()
    {
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = 'SB-Mid-server-dvS9mWzaI5eiN_nH4sbYe18W';
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;
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
