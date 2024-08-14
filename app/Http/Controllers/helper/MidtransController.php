<?php

namespace App\Http\Controllers\helper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Midtrans\Transaction;

class MidtransController extends Controller
{
    public function __construct()
    {
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = 'SB-Mid-server-EOmb5LvKeLEnP_vWZPGKWFEt';
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
            return response()->json([
                'status' => 200,
                'message' => 'berhasil',
                'data' => $status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
    // function generate snap token
    public function generateSnapToken($params)
    {
        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            return response()->json([
                'status' => 200,
                'message' => 'berhasil',
                'data' => $snapToken
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
