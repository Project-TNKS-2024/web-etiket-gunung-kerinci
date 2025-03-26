<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class LoggerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next): Response
    // {
    //     return $next($request);
    // }

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $data = [
            'path'    => $request->getPathInfo(),
            'method'  => $request->getMethod(),
            'ip'      => $request->ip(),
            'timestamp' => now()->toDateTimeString(),
            'headers' => [
                'user-agent' => $request->header('user-agent'),
                'referer'    => $request->header('referer'),
            ],
        ];

        // Tambahkan user ID jika ada
        if ($request->user()) {
            $data['user_id'] = $request->user()->id;
        }

        // Simpan data request (kecuali password)
        if (count($request->all()) > 0) {
            $hiddenKeys = ['password'];
            $data['request'] = $request->except($hiddenKeys);
        }

        // Ambil flash session tanpa menghapusnya
        $successMessage = session()->get('success', null);
        $errorMessage = session()->get('error', null);

        // Simpan success/error dari session
        $logLevel = 'info'; // Default info
        if (!empty($successMessage)) {
            $data['response']['success'] = $successMessage;
        }
        if (!empty($errorMessage)) {
            $data['response']['errors'] = $errorMessage;
            $logLevel = 'error'; // Jika ada error, ubah jadi error log
        }

        // Format nama log berdasarkan path request
        $message = str_replace('/', '_', trim($request->getPathInfo(), '/'));

        // // Simpan log ke channel "pengguna"
        Log::channel('pengguna')->$logLevel($message, $data);

        return $response;
    }
}
