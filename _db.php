<?php
// _db.php create|drop <name> — kelola DB uji lewat PDO (tanpa cetak kredensial)
$mode = $argv[1] ?? '';
$db   = $argv[2] ?? '';
if (!in_array($mode, ['create', 'drop'], true) || $db === '') {
    fwrite(STDERR, "usage: php _db.php create|drop <dbname>\n");
    exit(2);
}
$env = [];
foreach (file(__DIR__ . '/.env') as $l) {
    if (preg_match('/^\s*([A-Z_][A-Z0-9_]*)\s*=\s*(.*)$/', trim($l), $m)) {
        $env[$m[1]] = trim($m[2], "\"'");
    }
}
$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? '3306';
$user = $env['DB_USERNAME'] ?? 'root';
$pass = $env['DB_PASSWORD'] ?? '';
try {
    $pdo = new PDO("mysql:host=$host;port=$port", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    if ($mode === 'create') {
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db`");
        echo "Created $db\n";
    } else {
        $pdo->exec("DROP DATABASE IF EXISTS `$db`");
        echo "Dropped $db\n";
    }
} catch (Throwable $e) {
    fwrite(STDERR, "ERR: " . $e->getMessage() . "\n");
    exit(1);
}
