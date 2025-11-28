<?php
require __DIR__.'/vendor/autoload.php';
$env=file_exists('.env')?file_get_contents('.env'):''; $lines=explode("\n",$env); $cfg=[]; foreach($lines as $l){ if(trim($l)===''||substr(trim($l),0,1)=='#') continue; $p=explode('=',$l,2); if(count($p)==2) $cfg[$p[0]]=$p[1]; }
$pdo=new PDO('mysql:host='.$cfg['DB_HOST'].';port='.$cfg['DB_PORT'].';dbname='.$cfg['DB_DATABASE'].';charset=utf8mb4',$cfg['DB_USERNAME'],$cfg['DB_PASSWORD']);
$stmt=$pdo->query('SELECT id,email,password FROM users'); foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $r){ $ok = preg_match('/^\$2[ayb]\$/' , $r['password']) ? 'bcrypt' : 'NOT'; echo $r['id'].' | '.$r['email'].' | '.$ok."\n"; }
