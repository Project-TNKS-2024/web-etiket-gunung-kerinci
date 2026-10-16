<?php

namespace Tests\Feature;

use App\Models\destinasi;
use App\Models\gambar_destinasi;
use App\Models\gk_booking;
use App\Models\gk_gates;
use App\Models\gk_paket_tiket;
use App\Models\gk_tiket_pendaki;
use Database\Seeders\BookingSeeder;
use Database\Seeders\UserSampelSeeder;
use Database\Seeders\destinasi_gate;
use Database\Seeders\paket_tiket;
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
        $booking = gk_booking::firstOrFail();
        $this->assertSame($gate->id, (int) $booking->gate_masuk);
        $this->assertSame($package->id, (int) $booking->id_tiket);
        $this->assertSame(2, $booking->pendakis()->count());

        $snapshot = [destinasi::count(), gk_gates::count(), gambar_destinasi::count(), gk_paket_tiket::count(), gk_tiket_pendaki::count(), gk_booking::count()];
        $this->seed($seeders);
        $this->assertSame($snapshot, [destinasi::count(), gk_gates::count(), gambar_destinasi::count(), gk_paket_tiket::count(), gk_tiket_pendaki::count(), gk_booking::count()]);
        $this->assertSame($booking->id, gk_booking::firstOrFail()->id);
    }
}
