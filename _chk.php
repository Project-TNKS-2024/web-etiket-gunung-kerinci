<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "== gk_posts columns ==\n";
foreach (DB::select("SHOW COLUMNS FROM gk_posts") as $c) echo "  ".$c->Field." | ".$c->Type."\n";

echo "\n== gk_posts rows ==\n";
foreach (DB::table('gk_posts')->orderBy('id')->get() as $p) {
    echo "  ".json_encode($p)."\n";
}

echo "\n== booking demo ==\n";
$b = DB::table('gk_bookings')->orderByDesc('created_at')->first();
echo "  ".json_encode($b)."\n";

echo "\n== pendakis of that booking ==\n";
foreach (DB::table('gk_pendakis')->where('booking_id', $b->id)->get() as $p) {
    echo "  ".json_encode(['id'=>$p->id, 'id_bio'=>$p->id_bio ?? null, 'name'=>$p->name ?? null, 'status'=>$p->status ?? null])."\n";
}

echo "\n== user1 ==\n";
$u = DB::table('users')->where('email','user1@example.com')->first();
echo "  email_verified_at=".var_export($u->email_verified_at ?? null, true)." gauth=".var_export($u->gauth_type ?? null, true)."\n";

echo "\n== counts ==\n";
foreach (['gk_sos','gk_sos_chats','gk_emergency_alerts','gk_checkpoint_logs','gk_trackings','gk_bookings','gk_pembayarans','pembayarans'] as $t) {
    try { echo "  $t=".DB::table($t)->count()."\n"; } catch (\Throwable $e) { echo "  $t=ERR\n"; }
}

echo "\n== gates ==\n";
foreach (DB::table('gk_gates')->get() as $g) echo "  ".json_encode($g)."\n";
