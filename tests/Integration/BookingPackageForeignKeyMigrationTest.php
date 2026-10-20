<?php

namespace Tests\Integration;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BookingPackageForeignKeyMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (!app()->environment('testing') || DB::getDriverName() !== 'mysql'
            || !str_starts_with(DB::connection()->getDatabaseName(), 'tnks_migration80_test_')) {
            $this->markTestSkipped('Requires a dedicated MySQL database named tnks_migration80_test_*.');
        }
        foreach (['gk_bookings', 'gk_tiket_pendakis', 'gk_paket_tikets', 'users', 'migrations'] as $table) {
            Schema::dropIfExists($table);
        }
        foreach (['users', 'gk_paket_tikets', 'gk_tiket_pendakis'] as $table) {
            Schema::create($table, fn (Blueprint $blueprint) => $blueprint->id());
            DB::table($table)->insert(['id' => 1]);
        }
        Schema::create('gk_bookings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('id_tiket');
            $table->foreignId('id_user')->constrained('users');
        });
        DB::table('gk_bookings')->insert(['id' => 1, 'id_tiket' => 1, 'id_user' => 1]);
    }

    private function migration(): object
    {
        return require database_path('migrations/80_fix_booking_package_foreign_key.php');
    }

    private function addConstraint(string $name, string $target): void
    {
        Schema::table('gk_bookings', function (Blueprint $table) use ($name, $target): void {
            $table->foreign('id_tiket', $name)->references('id')->on($target)->onDelete('restrict');
        });
    }

    private function assertTarget(string $target): void
    {
        $keys = collect(Schema::getForeignKeys('gk_bookings'));
        $ticketKeys = $keys->filter(fn ($key) => $key['columns'] === ['id_tiket']);
        $this->assertCount(1, $ticketKeys);
        $this->assertSame($target, $ticketKeys->first()['foreign_table']);
        $this->assertSame(['id'], $ticketKeys->first()['foreign_columns']);
        $this->assertSame('restrict', $ticketKeys->first()['on_delete']);
        $this->assertCount(1, $keys->filter(fn ($key) => $key['columns'] === ['id_user']));
        $this->assertSame(1, DB::table('gk_bookings')->count());
        $this->assertSame(1, (int) DB::table('gk_bookings')->value('id_tiket'));
    }

    public function test_missing_constraint_is_created_without_dropping_an_absent_name(): void
    {
        $this->artisan('migrate', [
            '--path' => 'database/migrations/80_fix_booking_package_foreign_key.php',
            '--force' => true,
        ])->assertExitCode(0);
        $this->assertDatabaseHas('migrations', ['migration' => '80_fix_booking_package_foreign_key']);
        $this->assertTarget('gk_paket_tikets');
    }

    public function test_custom_constraint_name_is_replaced_using_its_actual_name(): void
    {
        $this->addConstraint('hosting_custom_ticket_fk', 'gk_tiket_pendakis');
        $this->migration()->up();
        $this->assertTarget('gk_paket_tikets');
    }

    public function test_already_correct_custom_constraint_remains_unchanged(): void
    {
        $this->addConstraint('hosting_correct_ticket_fk', 'gk_paket_tikets');
        $before = Schema::getForeignKeys('gk_bookings');
        $this->migration()->up();
        $this->assertSame($before, Schema::getForeignKeys('gk_bookings'));
        $this->assertTarget('gk_paket_tikets');
    }

    public function test_orphaned_ids_abort_before_changing_constraints_or_data(): void
    {
        DB::table('gk_tiket_pendakis')->insert(['id' => 99]);
        DB::table('gk_bookings')->where('id', 1)->update(['id_tiket' => 99]);
        $this->addConstraint('hosting_legacy_ticket_fk', 'gk_tiket_pendakis');
        $before = Schema::getForeignKeys('gk_bookings');
        try {
            $this->migration()->up();
            $this->fail('Expected an orphan ID guard.');
        } catch (\RuntimeException $error) {
            $this->assertStringContainsString('Data booking tidak diubah', $error->getMessage());
        }
        $this->assertSame($before, Schema::getForeignKeys('gk_bookings'));
        $this->assertSame(99, (int) DB::table('gk_bookings')->value('id_tiket'));
    }

    public function test_retry_after_a_previous_drop_and_repeated_up_are_safe(): void
    {
        $this->addConstraint('previous_ticket_fk', 'gk_tiket_pendakis');
        Schema::table('gk_bookings', fn (Blueprint $table) => $table->dropForeign('previous_ticket_fk'));
        $this->migration()->up();
        $this->migration()->up();
        $this->assertTarget('gk_paket_tikets');
    }

    public function test_down_also_handles_a_custom_constraint_name(): void
    {
        $this->addConstraint('hosting_correct_ticket_fk', 'gk_paket_tikets');
        $this->migration()->down();
        $this->assertTarget('gk_tiket_pendakis');
    }
}
