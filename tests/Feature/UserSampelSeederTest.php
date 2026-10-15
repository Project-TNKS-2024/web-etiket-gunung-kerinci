<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\bio_pendaki;
use Database\Seeders\UserSampelSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSampelSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeds_only_three_named_sample_users_and_can_be_run_again(): void
    {
        $this->seed(UserSampelSeeder::class);
        $this->seed(UserSampelSeeder::class);

        $this->assertSame(3, User::where('role', 'user')->count());
        $this->assertSame(3, bio_pendaki::count());
        $satria = User::where('email', 'satria@gmail.com')->firstOrFail()->biodata;
        $this->assertDatabaseHas('users', ['email' => 'mukhtada@gmail.com']);
        $this->assertDatabaseHas('users', ['email' => 'opi@gmail.com']);
        $this->assertSame('Satria Bahari', trim($satria->first_name . ' ' . $satria->last_name));
        $this->assertDatabaseHas('biodatas', ['first_name' => 'Mukhtada', 'last_name' => 'Billah Nasution']);
        $this->assertDatabaseHas('biodatas', ['first_name' => 'Opinur', 'last_name' => '']);
        $this->assertDatabaseMissing('users', ['email' => 'user4@example.com']);
        $this->assertDatabaseMissing('users', ['email' => 'user10@example.com']);
    }

    public function test_reseeding_updates_old_names_without_changing_password_biodata_id_or_contact(): void
    {
        $this->seed(UserSampelSeeder::class);
        $user = User::where('email', 'satria@gmail.com')->firstOrFail();
        $id = $user->id_bio;
        $password = $user->password;
        $user->update(['email' => 'user1@example.com']);
        $user->biodata->update(['first_name' => 'User', 'last_name' => 'One', 'no_hp' => '080000000001']);

        $this->seed(UserSampelSeeder::class);
        $user->refresh();
        $this->assertSame('satria@gmail.com', $user->email);
        $this->assertDatabaseMissing('users', ['email' => 'user1@example.com']);
        $this->assertSame($id, $user->id_bio);
        $this->assertSame($password, $user->password);
        $this->assertSame('Satria', $user->biodata->first_name);
        $this->assertSame('Bahari', $user->biodata->last_name);
        $this->assertSame('080000000001', $user->biodata->no_hp);
    }
}
