<?php
require __DIR__.'/vendor/autoload.php';
$kernel = require __DIR__.'/bootstrap/app.php';
$kernel->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;
use App\Models\User;

$customer = User::where('role','customer')->first();
if(!$customer) { echo "No customer found\n"; exit; }
echo "Customer: {$customer->id} - {$customer->email}\n";
$recent_orders = $customer->orders()->with('service')->latest()->limit(5)->get();
echo "Latest 5 customer orders (including awaiting_confirmation):\n";
foreach($recent_orders as $o){ echo "- {$o->id} | {$o->order_number} | status: {$o->status} | token: " . ($o->confirmation_token ? $o->confirmation_token : '-') . "\n"; }
