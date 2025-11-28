<?php
require __DIR__.'/vendor/autoload.php';
use Illuminate\Support\Facades\Log;
$env = file_exists(__DIR__.'/.env') ? file_get_contents(__DIR__.'/.env') : '';
$lines = explode("\n", $env);
$cfg = [];
foreach($lines as $l){ if(trim($l)===''||substr(trim($l),0,1)==='#') continue; $p=explode('=', $l, 2); if(count($p)==2) $cfg[$p[0]]=$p[1]; }
$host = $cfg['DB_HOST'] ?? '127.0.0.1';
$port = $cfg['DB_PORT'] ?? '3306';
$db   = $cfg['DB_DATABASE'] ?? 'powerwash_db';
$user = $cfg['DB_USERNAME'] ?? 'root';
$pass = $cfg['DB_PASSWORD'] ?? '';
try { $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]); }
catch(Exception $e){ echo "MySQL connect failed: " . $e->getMessage() . PHP_EOL; exit(1); }
$token = $argv[1] ?? null;
if (!$token) { echo "Usage: php tmp_do_confirm.php <token>\n"; exit(1); }
$stmt = $pdo->prepare('SELECT id, status FROM orders WHERE confirmation_token = ? LIMIT 1');
$stmt->execute([$token]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) { echo "Token not found!\n"; exit(1); }
$id = $row['id'];
$status = $row['status'];
if (in_array($status, ['pending', 'awaiting_collection', 'approved', 'awaiting_confirmation'])) {
    $newStatus = 'processing';
} else {
    $newStatus = $status; // keep whatever current
}
$stmt = $pdo->prepare('UPDATE orders SET customer_confirmed = 1, customer_confirmed_at = NOW(), confirmation_token = NULL, status = ? WHERE id = ?');
$stmt->execute([$newStatus, $id]);
if ($stmt->rowCount()) {
    echo "Order $id confirmed by customer; status set to $newStatus\n";
} else {
    echo "No update done for Order $id\n";
}
