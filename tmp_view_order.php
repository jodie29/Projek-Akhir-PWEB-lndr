<?php
require __DIR__.'/vendor/autoload.php';
$kernel = require __DIR__.'/bootstrap/app.php';
$kernel->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\Order;
$order = Order::with('service')->find(3);
echo "OrderID: {$order->id} status: {$order->status} confirmed_at: ";
if ($order->customer_confirmed_at) { echo $order->customer_confirmed_at->format('Y-m-d H:i:s'); } else { echo '-'; }
echo " payment_method: {$order->payment_method} total_price: {$order->total_price}\n";