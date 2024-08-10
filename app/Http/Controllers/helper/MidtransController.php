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
            return response()->json($status);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    // function generate snap token
    public function generateSnapToken($params)
    {
        $snapToken = \Midtrans\Snap::getSnapToken($params);
        return $snapToken;
    }
}
