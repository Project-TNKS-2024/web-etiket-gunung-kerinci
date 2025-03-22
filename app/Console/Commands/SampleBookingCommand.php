<?php

namespace App\Console\Commands;

use App\Models\gk_booking;
use Illuminate\Console\Command;

class SampleBookingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sampel:booking {action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kelola sampel booking';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'deleteAll':
                # code...
                break;

            default:
                $this->error("Perintah tidak valid. Gunakan.");
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
