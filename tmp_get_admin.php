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
$stmt = $pdo->prepare('SELECT id,email,password,role,created_at,updated_at FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$row){ echo "Admin user not found: $email" . PHP_EOL; exit(0); }
// Print masked password (show only prefix)
$pw = $row['password'];
$masked = substr($pw,0,10) . (strlen($pw) > 10 ? '...('.strlen($pw).')' : '');
echo "id: {$row['id']}\nemail: {$row['email']}\npassword prefix: $masked\nrole: {$row['role']}\ncreated_at: {$row['created_at']}\nupdated_at: {$row['updated_at']}\n";
