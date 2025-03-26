<?php

namespace App\Console\Commands;

use App\Models\gk_booking;
use App\Models\gk_pendaki;
use App\Models\User;
use Illuminate\Console\Command;

class SampleUserCommand extends Command
{

    protected $signature = 'sampel:user {action}';


    protected $description = 'Membuat atau menghapus user sampel';


    public function handle()
    {
        $action = $this->argument('action');

        if ($action === 'create') {
            $this->createSampleUsers();
        } elseif ($action === 'delete') {
            $this->deleteSampleUsers();
        } else {
            $this->error("Perintah tidak valid. Gunakan 'create' atau 'delete'.");
        }
    }
    private function createSampleUsers()
    {
        $this->call('db:seed', [
            '--class' => 'UserSampelSeeder',
        ]);
        $this->info('User sampel berhasil dibuat.');
    }
    private function deleteSampleUsers()
    {
        $userSampel = User::where('email', 'LIKE', 'user%@example.com')->get();

        foreach ($userSampel as $user) {
            $pendakiSampel = gk_pendaki::where('id_bio', $user->id_bio)->get();
            
            foreach ($pendakiSampel as $pendaki) {
                gk_booking::where('id', $pendaki->booking_id)->delete();
            }
            $user->delete();
        }

        $this->info('User sampel berhasil dihapus.');
    }
}
