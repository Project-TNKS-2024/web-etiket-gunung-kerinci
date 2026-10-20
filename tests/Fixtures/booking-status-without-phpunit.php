<?php

// Simulate Composer --no-dev without changing vendor or the application database.
$root = dirname(__DIR__, 2);
foreach (require $root.'/vendor/composer/autoload_files.php' as $id => $file) {
    if (str_contains(strtolower(str_replace('\\', '/', $file)), '/phpunit/')) {
        $GLOBALS['__composer_autoload_files'][$id] = true;
    }
}
require $root.'/vendor/autoload.php';

if (function_exists('PHPUnit\\Framework\\isEmpty')) {
    throw new RuntimeException('The fixture must run without PHPUnit functions.');
}
$booking = new App\Models\gk_booking([
    'status_booking' => 6,
    'tanggal_masuk' => Carbon\Carbon::today()->toDateString(),
    'tanggal_keluar' => Carbon\Carbon::today()->addDays(2)->toDateString(),
]);
$hiker = new App\Models\statusPendaki(['status' => 2]);
$actual = [
    'default_booking' => $booking->getStatusBooking()->code,
    'zero_booking' => $booking->getStatusBooking(0)->code,
    'explicit_booking' => $booking->getStatusBooking(9)->code,
    'default_hiker' => $hiker->statusName(),
    'explicit_hiker' => $hiker->statusName(1),
    'zero_hiker' => $hiker->statusName(0),
];
$expected = [
    'default_booking' => 6, 'zero_booking' => 0, 'explicit_booking' => 9,
    'default_hiker' => 'Cek In', 'explicit_hiker' => 'Batal Mendaki', 'zero_hiker' => 'Tidak Diketahui',
];
if ($actual !== $expected) {
    throw new RuntimeException('Unexpected status result: '.json_encode($actual));
}
echo json_encode(['without_phpunit' => true, 'checks' => count($actual)]);
