<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\destinasi;
use Database\Seeders\AdminDestinationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDestinationSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_admin_can_select_kerinci_without_receiving_other_destinations(): void
    {
        $admin = User::create(['email' => 'superadmin@tnks.com', 'password' => 'password', 'role' => 'admin']);
        $kerinci = destinasi::create(['nama' => 'Gunung Kerinci', 'kategori' => 'gunung', 'lokasi' => 'Jambi']);
        destinasi::create(['nama' => 'Destinasi Lain', 'kategori' => 'gunung', 'lokasi' => 'Jambi']);
        $this->assertCount(0, $admin->destinasis()->get());

        $this->seed(AdminDestinationSeeder::class);
        $this->actingAs($admin)->get('/admin/emergency')->assertOk()
            ->assertViewHas('destinasis', fn ($destinations) => $destinations->pluck('id')->all() === [$kerinci->id])
            ->assertSee('<option value="'.$kerinci->id.'">Gunung Kerinci</option>', false);

        $this->post('/admin/emergency/broadcast', [
            'id_destinasi' => destinasi::where('nama', 'Destinasi Lain')->value('id'),
            'title' => 'Test', 'description' => 'Test', 'severity' => 'high',
        ])->assertRedirect()->assertSessionHas('error');
        $this->assertDatabaseCount('gk_emergency_messages', 0);
    }

    public function test_reseeding_preserves_existing_assignments_and_other_admins(): void
    {
        $admin = User::create(['email' => 'superadmin@tnks.com', 'password' => 'password', 'role' => 'admin']);
        $otherAdmin = User::create(['email' => 'other@example.test', 'password' => 'password', 'role' => 'admin']);
        $kerinci = destinasi::create(['nama' => 'Gunung Kerinci', 'kategori' => 'gunung', 'lokasi' => 'Jambi']);
        $other = destinasi::create(['nama' => 'Destinasi Lain', 'kategori' => 'gunung', 'lokasi' => 'Jambi']);
        $admin->destinasis()->attach($other->id, ['is_penanggungjawab' => true]);
        $this->seed(AdminDestinationSeeder::class);
        $this->seed(AdminDestinationSeeder::class);

        $this->assertEqualsCanonicalizing([$kerinci->id, $other->id], $admin->destinasis()->pluck('destinasis.id')->all());
        $this->assertTrue((bool) $admin->destinasis()->find($other->id)->pivot->is_penanggungjawab);
        $this->assertCount(0, $otherAdmin->destinasis()->get());
        $this->assertDatabaseCount('destinasi_user', 2);
    }
}
