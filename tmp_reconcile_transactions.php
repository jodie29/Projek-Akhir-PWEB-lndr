<?php
require __DIR__.'/vendor/autoload.php';
$kernel = require __DIR__.'/bootstrap/app.php';
$kernel->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order; use App\Models\Transaction; use Illuminate\Support\Facades\Schema;

if (!Schema::hasTable('transactions')) {
    echo "Transactions table not found. Please run migrations (php artisan migrate) to create it.\n";
    exit(1);
}

$orders = Order::whereNotNull('collected_at')->get();
$created = 0;
foreach ($orders as $order) {
    $exists = Transaction::where('order_id', $order->id)->where('type','collection')->exists();
    if (!$exists) {
        Transaction::create([
            'order_id' => $order->id,
            'type' => 'collection',
            'amount' => $order->collected_amount ?: $order->total_price ?: 0,
            'method' => $order->collection_method ?: $order->payment_method ?: null,
            'created_by' => $order->collected_by ?: $order->courier_id ?: null,
            'collected_at' => $order->collected_at,
            'notes' => 'Reconciled from order collected_at'
        ]);
        $created++;
    }
}

// Also reconcile orders with 'paid' status but no 'collected_at' or transaction
$paidOrders = Order::where('status','paid')->get();
foreach ($paidOrders as $order) {
    $exists = Transaction::where('order_id', $order->id)->where('type','collection')->exists();
    if (!$exists) {
        Transaction::create([
            'order_id' => $order->id,
            'type' => 'collection',
            'amount' => $order->collected_amount ?: $order->total_price ?: 0,
            'method' => $order->collection_method ?: $order->payment_method ?: null,
            'created_by' => $order->collected_by ?: $order->approved_by ?: null,
            'collected_at' => $order->collected_at ?: $order->approved_at,
            'notes' => 'Reconciled from order status=paid'
        ]);
        $created++;
    }
}

echo "Created $created transaction(s) from existing orders.\n";
