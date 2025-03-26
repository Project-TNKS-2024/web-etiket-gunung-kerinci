<?php

namespace App\Console\Commands;

use App\Models\gk_booking;
use Illuminate\Console\Command;

class SampleBookingCommand extends Command
{

    protected $signature = 'sampel:booking {action}';

    protected $description = 'Kelola sampel booking';

    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'deleteAll':
                # code...
                break;

            default:
                $this->error("Perintah tidak valid. Gunakan. Gunakan 'deleteAll' untuk menghapus semua booking sampel.");
                break;
        }
    }
    private function deleteAll()
    {
        $BookingAll = gk_booking::all();
        foreach ($BookingAll as $booking) {
            $booking->delete();
        }

        $this->info('Booking sampel berhasil dihapus.');
    }
}
