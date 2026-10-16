<?php

namespace Database\Seeders;

use App\Http\Controllers\helper\BookingHelperController;
use App\Models\bio_pendaki;
use App\Models\destinasi;
use App\Models\gk_booking;
use App\Models\gk_gates;
use App\Models\gk_paket_tiket;
use App\Models\gk_pendaki;
use App\Models\statusPendaki;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingSeeder extends Seeder
{
    private const DEMO_DESCRIPTION = 'Data demo seeding: pendakian aktif untuk uji fitur pelacakan jejak.';

    /**
     * Booking demo tiga hari: Satria (ketua), Mukhtada, dan Opi sedang mendaki
     * (status_booking = 6). Tanpa ini semua endpoint tracking/checkpoint
     * membalas 403 "Tidak ada pendakian aktif".
     *
     * Booking demo lama dilengkapi tanpa mengulang check-in anggota yang sudah ada.
     */
    public function run(): void
    {
        $user1 = User::where('email', 'satria@gmail.com')->first();
        $user2 = User::where('email', 'mukhtada@gmail.com')->first();
        $user3 = User::where('email', 'opi@gmail.com')->first();

        $pendakiUsers = collect([$user1, $user2, $user3]);
        if ($pendakiUsers->contains(fn ($user) => ! $user || ! $user->id_bio || ! $user->biodata)) {
            $this->command?->warn('BookingSeeder: ketiga akun beserta biodatanya harus tersedia, jalankan UserSampelSeeder dahulu.');

            return;
        }

        $booking = gk_booking::where('id_user', $user1->id)
            ->where('status_booking', 6)
            ->where('keterangan', self::DEMO_DESCRIPTION)
            ->first();
        if (! $booking && gk_booking::where('status_booking', 6)->exists()) {
            $this->command?->info('BookingSeeder: booking aktif lain sudah ada, dilewati.');

            return;
        }

        // Pertahankan tanggal mulai booking lama agar riwayat pendakian tetap sesuai.
        $masuk = $booking ? Carbon::parse($booking->tanggal_masuk) : Carbon::today();
        $keluar = $masuk->copy()->addDays(2);

        // gk_bookings.id_tiket -> relasi gktiket() = gk_paket_tiket ("Umum").
        $destinationId = destinasi::where('nama', 'Gunung Kerinci')->firstOrFail()->id;
        $paketId = gk_paket_tiket::where('id_destinasi', $destinationId)->where('nama', 'Umum')->firstOrFail()->id;
        $gateId = gk_gates::where('id_destinasi', $destinationId)->where('nama', 'Desa Kersik Tuo')->firstOrFail()->id;

        DB::transaction(function () use ($booking, $user1, $pendakiUsers, $masuk, $keluar, $paketId, $gateId): void {
            $booking ??= new gk_booking;
            if (! $booking->exists) {
                $booking->fill(['total_pendaki_wni' => 0, 'total_pendaki_wna' => 0, 'total_pembayaran' => 0]);
            }
            $booking->fill([
                'id_user' => $user1->id,
                'id_tiket' => $paketId,
                'tanggal_masuk' => $masuk->toDateString(),
                'tanggal_keluar' => $keluar->toDateString(),
                'total_hari' => (int) $masuk->diffInDays($keluar) + 1,
                'gate_masuk' => $gateId,
                'gate_keluar' => $gateId,
                'status_booking' => 6, // Check-in / sedang mendaki
                'status_pembayaran' => 1,
                'unique_code' => filled($booking->unique_code) ? $booking->unique_code : $this->generateTicketCode(),
                'keterangan' => self::DEMO_DESCRIPTION,
            ]);
            $booking->save();

            foreach ($pendakiUsers as $user) {
                $bio = bio_pendaki::find($user->id_bio);

                $pendaki = gk_pendaki::firstOrCreate([
                    'booking_id' => $booking->id,
                    'id_bio' => $user->id_bio,
                ], [
                    'tagihan' => 0,
                    'usia' => $bio?->tanggal_lahir
                        ? Carbon::parse($bio->tanggal_lahir)->age
                        : 25,
                ]);

                // Riwayat status pendaki (2 = "Cek In"), mengikuti alur admin saat check-in.
                if ($pendaki->wasRecentlyCreated) {
                    statusPendaki::create([
                        'id_pendaki' => $pendaki->id,
                        'status' => 2,
                        'detail' => 'Check-in seeding '.$masuk->format('d-m-Y'),
                    ]);
                }
            }

            // Gunakan perhitungan aplikasi agar tarif tiga hari mengikuti tanggal dan kewarganegaraan.
            $helper = app(BookingHelperController::class);
            $members = $booking->pendakis()->with('biodata')->get();
            foreach ($members as $member) {
                $member->update(['tagihan' => $helper->getTagihanPendaki($member)]);
            }
            $wni = $members->filter(fn ($member) => $member->biodata->kenegaraan === 'ID')->count();
            $booking->update([
                'total_pendaki_wni' => $wni,
                'total_pendaki_wna' => $members->count() - $wni,
                'total_pembayaran' => $members->sum('tagihan'),
                'dataStruk' => null,
            ]);
        });

        $this->command?->info('BookingSeeder: booking demo 3 hari diperbarui; Satria ketua, Mukhtada dan Opi anggota.');
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
