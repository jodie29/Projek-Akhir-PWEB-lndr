<?php
require __DIR__.'/vendor/autoload.php';
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
$orderId = $argv[1] ?? 1;
$token = bin2hex(random_bytes(16));
$actual_weight = 2.5;
$service_price = 20000; // not reading service price; compute final
$total_price = round($actual_weight * $service_price + 6000, 2);
$stmt = $pdo->prepare('UPDATE orders SET actual_weight = ?, total_price = ?, payment_method = "Bayar Nanti", status = "awaiting_confirmation", customer_confirmed = 0, confirmation_token = ? WHERE id = ?');
$stmt->execute([$actual_weight, $total_price, $token, $orderId]);
if($stmt->rowCount()===0){ echo "Order not found or no changes made\n"; exit(0); }
// print token so we can test
echo "Order $orderId prepared for confirmation. Token: $token\n";
