<?php
require __DIR__.'/vendor/autoload.php';
$kernel = require __DIR__.'/bootstrap/app.php';
$kernel->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\Order;
use Illuminate\Http\Request;

// Find a sample order to mark ready for delivery; prefer one without a courier assigned
$order = Order::whereNull('courier_id')
            ->whereNotIn('status', ['ready_for_delivery','diantar','selesai','delivered'])
            ->first();
if(!$order) {
    // If none, take latest and clear courier_id
    $order = Order::latest()->first();
    if(!$order) {
        echo "No orders found\n";
        exit(0);
    }
    $order->courier_id = null;
    $order->status = 'pending';
    $order->save();
}

echo "Marking order {$order->id} (courier: ".($order->courier_id?:'NULL').") ready without auto-assign\n";

$controller = new App\Http\Controllers\Admin\OrderController();
$request = Request::create('/admin/orders/'.$order->id.'/ready-for-delivery', 'POST', []);
$controller->markReadyForDelivery($request, $order->id);
echo "Done\n";
