<?php

if (!function_exists('mobile')) {
   /**
    * Generate deep link URL ke aplikasi mobile dari .env
    *
    * @param string $path Contoh: 'verify-email?id=1&hash=abcd'
    * @return string
    */
   function mobile(string $path): string
   {
      $scheme = env('MOBILE_SCHEME', 'app://');

      // Pastikan tidak ada duplikasi slash
      $path = ltrim($path, '/');

      return "{$scheme}/{$path}";
   }
}
