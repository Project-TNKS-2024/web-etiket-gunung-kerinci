<?php

namespace Tests\Feature;

use App\Models\destinasi;
use App\Models\gambar_destinasi;
use App\Models\gk_booking;
use App\Models\gk_gates;
use App\Models\gk_paket_tiket;
use App\Models\gk_tiket_pendaki;
use App\Models\statusPendaki;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\BookingSeeder;
use Database\Seeders\destinasi_gate;
use Database\Seeders\paket_tiket;
use Database\Seeders\UserSampelSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoSeedRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeding_uses_actual_ids_and_repairs_dangling_references_without_duplicates(): void
    {
        $destination = destinasi::forceCreate(['id' => 41, 'nama' => 'Gunung Kerinci', 'kategori' => 'gunung', 'lokasi' => 'Jambi']);
        $gate = gk_gates::forceCreate(['id' => 52, 'nama' => 'Desa Kersik Tuo', 'id_destinasi' => 999]);
        $image = gambar_destinasi::create(['src' => 'componen/gambar_destinasi/kerinci.png', 'nama' => 'Kerinci', 'detail' => 'Gambar lama', 'id_destinasi' => 999]);
        gk_paket_tiket::forceCreate(['id' => 73, 'id_destinasi' => $destination->id, 'nama' => 'Paket lain']);

        $seeders = [destinasi_gate::class, paket_tiket::class, UserSampelSeeder::class, BookingSeeder::class];
        $this->seed($seeders);
        $this->assertSame($destination->id, (int) $gate->fresh()->id_destinasi);
        $this->assertSame($destination->id, (int) $image->fresh()->id_destinasi);
        $package = gk_paket_tiket::where('id_destinasi', $destination->id)->where('nama', 'Umum')->firstOrFail();
        $this->assertGreaterThan(73, $package->id);
        $this->assertSame(2, gk_tiket_pendaki::where('id_paket_tiket', $package->id)->count());
        $booking = gk_booking::where('unique_code', 'DEMOCEKIN1')->firstOrFail();
        $this->assertSame($gate->id, (int) $booking->gate_masuk);
        $this->assertSame($package->id, (int) $booking->id_tiket);
        $this->assertSame(3, $booking->pendakis()->count());
        $this->assertSame(3, (int) $booking->total_hari);
        $this->assertSame(2, (int) $booking->total_pendaki_wni);
        $this->assertSame(1, (int) $booking->total_pendaki_wna);
        $this->assertSame(User::where('email', 'satria@gmail.com')->firstOrFail()->id, (int) $booking->id_user);
        foreach (['satria@gmail.com', 'mukhtada@gmail.com', 'opi@gmail.com'] as $email) {
            $user = User::where('email', $email)->firstOrFail();
            $this->assertTrue($booking->pendakis()->where('id_bio', $user->id_bio)->exists());
            $this->actingAs($user, 'sanctum')->getJson('/api/booking/'.$booking->id.'/tiket')->assertOk();
        }
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9]{10}$/', $booking->unique_code ?? '');
        $this->actingAs(User::where('email', 'satria@gmail.com')->firstOrFail(), 'sanctum')
            ->getJson('/api/booking/'.$booking->id.'/tiket')
            ->assertOk()
            ->assertJsonPath('data.unique_code', $booking->unique_code);

        $snapshot = [destinasi::count(), gk_gates::count(), gambar_destinasi::count(), gk_paket_tiket::count(), gk_tiket_pendaki::count(), gk_booking::count()];
        $this->seed($seeders);
        $this->assertSame($snapshot, [destinasi::count(), gk_gates::count(), gambar_destinasi::count(), gk_paket_tiket::count(), gk_tiket_pendaki::count(), gk_booking::count()]);
        $this->assertSame($booking->id, gk_booking::where('unique_code', 'DEMOCEKIN1')->firstOrFail()->id);
        $this->assertSame($booking->unique_code, $booking->fresh()->unique_code);
    }

    public function test_reseeding_repairs_missing_demo_ticket_code_without_recreating_booking(): void
    {
        $this->seed([destinasi_gate::class, paket_tiket::class, UserSampelSeeder::class, BookingSeeder::class]);
        $booking = gk_booking::where('unique_code', 'DEMOCEKIN1')->firstOrFail();
        $booking->update(['unique_code' => null]);
        $snapshot = $booking->fresh()->getAttributes();
        $hikerIds = $booking->pendakis()->pluck('id')->all();

        $this->seed(BookingSeeder::class);

        $booking->refresh();
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9]{10}$/', $booking->unique_code ?? '');
        $this->assertSame(7, gk_booking::count());
        $this->assertSame($hikerIds, $booking->pendakis()->pluck('id')->all());
        unset($snapshot['unique_code'], $snapshot['updated_at']);
        $this->assertSame($snapshot, array_diff_key($booking->getAttributes(), array_flip(['unique_code', 'updated_at'])));
    }

    public function test_reseeding_does_not_change_non_demo_active_booking(): void
    {
        $this->seed([destinasi_gate::class, paket_tiket::class, UserSampelSeeder::class, BookingSeeder::class]);
        $booking = gk_booking::where('unique_code', 'DEMOCEKIN1')->firstOrFail();
        $booking->update(['unique_code' => null, 'keterangan' => 'Booking pengguna']);
        $snapshot = $booking->fresh()->getAttributes();

        $this->seed(BookingSeeder::class);

        $this->assertSame($snapshot, $booking->fresh()->getAttributes());
        $this->assertSame(7, gk_booking::count());
    }

    public function test_reseeding_preserves_processed_demo_ticket_without_restoring_removed_member(): void
    {
        $this->seed([destinasi_gate::class, paket_tiket::class, UserSampelSeeder::class, BookingSeeder::class]);
        $booking = gk_booking::where('unique_code', 'DEMOCEKIN1')->firstOrFail();
        $opi = $booking->pendakis()->where('id_bio', User::where('email', 'opi@gmail.com')->firstOrFail()->id_bio)->firstOrFail();
        $opi->getStatus()->delete();
        $opi->delete();
        $booking->update([
            'tanggal_keluar' => Carbon::parse($booking->tanggal_masuk)->addDay()->toDateString(),
            'total_hari' => 2,
            'total_pendaki_wni' => 2,
            'total_pendaki_wna' => 0,
            'total_pembayaran' => 105000,
        ]);
        $existingIds = $booking->pendakis()->pluck('id')->all();
        $statusCount = statusPendaki::count();

        $this->seed(BookingSeeder::class);

        $fresh = $booking->fresh();
        $this->assertSame(7, gk_booking::count());
        $this->assertSame($booking->unique_code, $fresh->unique_code);
        $this->assertSame($booking->tanggal_masuk, $fresh->tanggal_masuk);
        $this->assertSame(2, (int) $fresh->total_hari);
        $this->assertSame(Carbon::parse($fresh->tanggal_masuk)->addDay()->toDateString(), $fresh->tanggal_keluar);
        $this->assertSame(2, $fresh->pendakis()->count());
        $this->assertSame($existingIds, $fresh->pendakis()->pluck('id')->all());
        $this->assertSame($statusCount, statusPendaki::count());
        $this->assertSame(105000, (int) $fresh->total_pembayaran);
    }
}
