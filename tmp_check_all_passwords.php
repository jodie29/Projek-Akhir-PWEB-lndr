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
$tests = ['12345678','password','123456789','123'];
$stmt = $pdo->prepare('SELECT id,name,email,role,password FROM users');
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if(!$rows){ echo "No users found\n"; exit(0); }
foreach($rows as $r){
    echo sprintf("%3s | %-30s | %-8s | ", $r['id'], $r['email'], $r['role']);
    $found = false;
    foreach($tests as $t){
        if(password_verify($t, $r['password'])){
            echo "PASS($t)"; $found = true; break;
        }
    }
    if(!$found) echo "NO MATCH";
    echo PHP_EOL;
}
