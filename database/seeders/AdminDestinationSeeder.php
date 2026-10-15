<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\destinasi;
use Illuminate\Database\Seeder;

class AdminDestinationSeeder extends Seeder
{
    public function run(): void
    {
        // Run after the demo account and destinations exist; retain other assignments.
        $admin = User::where('email', 'superadmin@tnks.com')->where('role', 'admin')->firstOrFail();
        $destination = destinasi::where('nama', 'Gunung Kerinci')->firstOrFail();
        $admin->destinasis()->syncWithoutDetaching([$destination->id]);
    }
}
