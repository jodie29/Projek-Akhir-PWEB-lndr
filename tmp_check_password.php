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
$email = 'admin@powerwash.test';
$stmt = $pdo->prepare('SELECT password FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$row){ echo "Admin user not found: $email" . PHP_EOL; exit(0); }
$hash = $row['password'];
$tests = ['password','123'];
foreach($tests as $t){
    $ok = password_verify($t, $hash) ? 'MATCH' : 'NOPE';
    echo "Checking '$t' => $ok\n";
}
