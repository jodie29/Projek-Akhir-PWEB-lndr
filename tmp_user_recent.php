<?php
require __DIR__.'/vendor/autoload.php';
$env=file_exists('.env')?file_get_contents('.env'):''; $cfg=[]; foreach(explode("\n",$env) as $l){ if(trim($l)===''||substr(trim($l),0,1)=='#') continue; $p=explode('=',$l,2); if(count($p)==2) $cfg[$p[0]]=$p[1]; }
$pdo=new PDO('mysql:host='.$cfg['DB_HOST'].';port='.$cfg['DB_PORT'].';dbname='.$cfg['DB_DATABASE'].';charset=utf8mb4',$cfg['DB_USERNAME'],$cfg['DB_PASSWORD']);
$userid=1; $stmt=$pdo->prepare('SELECT id,order_number,status,created_at FROM orders WHERE customer_id=? AND status NOT IN ("awaiting_confirmation") ORDER BY created_at DESC LIMIT 5'); $stmt->execute([$userid]); foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) echo $r['id'].' | '.$r['order_number'].' | status:'.$r['status'].' | '.$r['created_at']."\n";
