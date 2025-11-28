<?php
require __DIR__.'/vendor/autoload.php';
$kernel = require __DIR__.'/bootstrap/app.php';
$kernel->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\User;
use App\Http\Controllers\Courier\OrderController as CourierOrderController;

$courier = User::where('role','courier')->first();
if(!$courier) { echo "No courier user\n"; exit; }
Auth::loginUsingId($courier->id);

$order = Order::where('courier_id', $courier->id)->whereIn('status', ['dijemput','diantar'])->first();
if(!$order) {
    // create a temporary state: pick order assigned to courier and set to dijemput
    $order = Order::where('courier_id', $courier->id)->first();
    if(!$order) { echo "No order assigned to courier to deliver\n"; exit; }
    $order->status = 'dijemput'; $order->save();
}

echo "Attempting to deliver order {$order->id} with status={$order->status} by courier {$courier->id}\n";
$controller = new CourierOrderController();
$controller->delivered($order);
$order->refresh();
echo "Result: order id={$order->id} status={$order->status}\n";
 
