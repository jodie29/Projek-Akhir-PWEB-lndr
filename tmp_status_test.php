<?php
require __DIR__.'/vendor/autoload.php';
$kernel=require __DIR__.'/bootstrap/app.php';
$kernel->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\Order;
$order = Order::find(1);
if (!$order) { echo "Order not found\n"; exit(0); }
$order->status = 'processing';
$order->save();
$order->refresh();
echo "Order {$order->id} status after save: {$order->status}\n";
