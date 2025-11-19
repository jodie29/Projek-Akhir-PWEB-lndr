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
$stmt = $pdo->query("SELECT id, name, email, role, password FROM users WHERE email = 'admin@powerwash.test'");
foreach($stmt as $row){
    $pw = $row['password'];
    $masked = substr($pw,0,10) . (strlen($pw) > 10 ? '...('.strlen($pw).')' : '');
    echo "id: {$row['id']}\nname: {$row['name']}\nemail: {$row['email']}\nrole: {$row['role']}\npassword prefix: $masked\n";
}
