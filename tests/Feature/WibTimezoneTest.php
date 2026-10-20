<?php

namespace Tests\Feature;

use App\Events\EmergencyBroadcast;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class WibTimezoneTest extends TestCase
{
    use RefreshDatabase;

    public function test_application_and_persisted_model_dates_use_wib(): void
    {
        $this->assertSame('Asia/Jakarta', config('app.timezone'));
        $this->assertSame('Asia/Jakarta', date_default_timezone_get());
        $user = new User(['email' => 'clock@example.test', 'password' => 'password']);
        $user->created_at = Carbon::parse('2026-10-20T13:20:00Z');
        $user->save();

        $this->assertSame('2026-10-20 20:20:00', DB::table('users')->where('id', $user->id)->value('created_at'));
        $this->assertSame('2026-10-20T20:20:00+07:00', $user->fresh()->toArray()['created_at']);
        $this->assertSame(Carbon::parse('2026-10-20T13:20:00Z')->timestamp, $user->fresh()->created_at->timestamp);
    }

    public function test_explicit_wib_input_is_not_shifted_again(): void
    {
        $user = new User(['email' => 'wib@example.test', 'password' => 'password']);
        $user->created_at = Carbon::parse('2026-10-20T20:20:00+07:00');
        $user->save();
        $this->assertSame('2026-10-20T20:20:00+07:00', $user->fresh()->toArray()['created_at']);
    }

    public function test_database_wall_time_stays_wib_when_clock_has_an_explicit_utc_offset(): void
    {
        $this->travelTo(Carbon::parse('2026-10-20T13:20:00Z'));
        try {
            $user = new User(['email' => 'utc-clock@example.test', 'password' => 'password']);
            $user->created_at = now('Asia/Jakarta');
            $user->save();
            $this->assertSame('2026-10-20 20:20:00', DB::table('users')->where('id', $user->id)->value('created_at'));
            $this->assertSame('2026-10-20T20:20:00+07:00', $user->fresh()->toArray()['created_at']);
        } finally {
            $this->travelBack();
        }
    }

    public function test_historical_migration_never_shifts_existing_dates_again(): void
    {
        DB::table('biodatas')->insert([
            'id' => 'legacy-bio', 'nik' => 'test', 'kenegaraan' => 'ID', 'first_name' => 'Clock',
            'lampiran_identitas' => 'test.jpg', 'no_hp' => '0', 'jenis_kelamin' => 'l',
            'tanggal_lahir' => '2000-01-01', 'created_at' => '2026-10-20 13:20:00',
            'updated_at' => null,
        ]);
        $migration = require database_path('migrations/88_convert_legacy_dates_to_wib.php');
        $migration->up();
        $row = DB::table('biodatas')->where('id', 'legacy-bio')->first();
        $this->assertSame('2026-10-20 13:20:00', $row->created_at);
        $this->assertSame('2000-01-01', $row->tanggal_lahir);
        $this->assertNull($row->updated_at);
        $migration->down();
        $this->assertSame('2026-10-20 13:20:00', DB::table('biodatas')->where('id', 'legacy-bio')->value('created_at'));
    }

    public function test_database_offset_is_derived_from_application_timezone_only(): void
    {
        $database = require config_path('database.php');
        $expected = now(config('app.timezone'))->format('P');
        $this->assertSame('+07:00', $expected);
        $this->assertSame($expected, $database['connections']['mysql']['timezone']);
        $this->assertSame($expected, $database['connections']['mariadb']['timezone']);
    }

    public function test_broadcast_timestamp_uses_wib_offset(): void
    {
        $this->travelTo(Carbon::parse('2026-10-20T13:20:00Z'));
        try {
            $event = new EmergencyBroadcast(1, 2, 'Warning', 'Detail', 'high', 'Admin');
            $this->assertSame('2026-10-20T20:20:00+07:00', $event->broadcastWith()['timestamp']);
        } finally {
            $this->travelBack();
        }
    }
}
