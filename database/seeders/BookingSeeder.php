<?php

namespace Database\Seeders;

use App\Http\Controllers\helper\BookingHelperController;
use App\Models\bio_pendaki;
use App\Models\gk_booking;
use App\Models\destinasi;
use App\Models\gk_gates;
use App\Models\gk_paket_tiket;
use App\Models\gk_pendaki;
use App\Models\statusPendaki;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    private const DEMO_DESCRIPTION = 'Data demo seeding: pendakian aktif untuk uji fitur pelacakan jejak.';

    /**
     * Booking demo siap sidang: Satria + Mukhtada dalam keadaan sedang mendaki
     * (status_booking = 6). Tanpa ini semua endpoint tracking/checkpoint
     * membalas 403 "Tidak ada pendakian aktif".
     *
     * Idempotent: kalau sudah ada booking aktif, tidak dibuat ulang.
     */
    public function run(): void
    {
        // Lengkapi booking demo lama tanpa mengulang check-in atau mengganti kode yang ada.
        $demoOwner = User::where('email', 'satria@gmail.com')->first();
        if ($demoOwner) {
            gk_booking::where('id_user', $demoOwner->id)
                ->where('status_booking', 6)
                ->where('keterangan', self::DEMO_DESCRIPTION)
                ->get()
                ->each(function (gk_booking $booking): void {
                    if (blank($booking->unique_code)) {
                        $booking->update(['unique_code' => $this->generateTicketCode()]);
                    }
                });
        }

        if (gk_booking::where('status_booking', 6)->exists()) {
            $this->command?->info('BookingSeeder: booking aktif sudah ada, dilewati.');
            return;
        }

        $user1 = User::where('email', 'satria@gmail.com')->first();
        $user2 = User::where('email', 'mukhtada@gmail.com')->first();

        if (! $user1) {
            $this->command?->warn('BookingSeeder: satria@gmail.com tidak ditemukan, dilewati.');
            return;
        }

        // Pendaki diambil dari biodata user sampel (user2 opsional).
        $pendakiUsers = collect([$user1, $user2])
            ->filter(fn ($u) => $u && $u->id_bio)
            ->values();

        if ($pendakiUsers->isEmpty()) {
            $this->command?->warn('BookingSeeder: biodata pendaki tidak ditemukan, dilewati.');
            return;
        }

        // Harga per pendaki (WNI, 2 hari): masuk wk 10.000 + masuk wd 15.000
        // + kemah 5.000 + tracking 20.000 + asuransi 2.500 = 52.500.
        $tagihanPerPendaki = 52500;

        $masuk = Carbon::today();
        $keluar = Carbon::today()->addDay();

        // gk_bookings.id_tiket -> relasi gktiket() = gk_paket_tiket ("Umum").
        $destinationId = destinasi::where('nama', 'Gunung Kerinci')->firstOrFail()->id;
        $paketId = gk_paket_tiket::where('id_destinasi', $destinationId)->where('nama', 'Umum')->firstOrFail()->id;
        $gateId = gk_gates::where('id_destinasi', $destinationId)->where('nama', 'Desa Kersik Tuo')->firstOrFail()->id;

        $booking = gk_booking::create([
            'id_user' => $user1->id,
            'id_tiket' => $paketId,
            'tanggal_masuk' => $masuk->toDateString(),
            'tanggal_keluar' => $keluar->toDateString(),
            'total_hari' => (int) $masuk->diffInDays($keluar) + 1,
            'total_pendaki_wni' => $pendakiUsers->count(),
            'total_pendaki_wna' => 0,
            'gate_masuk' => $gateId,
            'gate_keluar' => $gateId,
            'status_booking' => 6, // Check-in / sedang mendaki
            'total_pembayaran' => $tagihanPerPendaki * $pendakiUsers->count(),
            'status_pembayaran' => 1,
            'unique_code' => $this->generateTicketCode(),
            'keterangan' => self::DEMO_DESCRIPTION,
        ]);

        foreach ($pendakiUsers as $user) {
            $bio = bio_pendaki::find($user->id_bio);

            $pendaki = gk_pendaki::create([
                'booking_id' => $booking->id,
                'tagihan' => $tagihanPerPendaki,
                'id_bio' => $user->id_bio,
                'usia' => $bio?->tanggal_lahir
                    ? Carbon::parse($bio->tanggal_lahir)->age
                    : 25,
            ]);

            // Riwayat status pendaki (2 = "Cek In"), mengikuti alur admin saat check-in.
            statusPendaki::create([
                'id_pendaki' => $pendaki->id,
                'status' => 2,
                'detail' => 'Check-in seeding ' . $masuk->format('d-m-Y'),
            ]);
        }

        $this->command?->info(sprintf(
            'BookingSeeder: 1 booking aktif (status_booking=6) + %d pendaki dibuat (gate Desa Kersik Tuo).',
            $pendakiUsers->count()
        ));
    }

    private function generateTicketCode(): string
    {
        $helper = app(BookingHelperController::class);
        do {
            $code = $helper->generateCode(10);
        } while (gk_booking::where('unique_code', $code)->exists());

        return $code;
    }
}
