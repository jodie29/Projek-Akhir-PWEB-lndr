<?php
require __DIR__.'/vendor/autoload.php';
$kernel = require __DIR__.'/bootstrap/app.php';
$kernel->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Courier\OrderController as CourierOrderController;

$courier = User::where('role','courier')->first();
if(!$courier) { echo "No courier user\n"; exit; }
Auth::loginUsingId($courier->id);

$order = Order::where('courier_id', $courier->id)->whereNotIn('status', ['paid','selesai','delivered'])->first();
if(!$order) { echo "No appropriate order to collect (courier has none in 'pending' states)\n"; exit; }

echo "Simulating collection for order {$order->id} current status={$order->status}\n";

$controller = new CourierOrderController();
$request = Request::create('/courier/orders/'.$order->id.'/collect', 'POST', [
    'collection_method' => 'Tunai',
    'collected_amount' => $order->total_price ?? 0,
]);
+$controller->collect($request, $order);
$order->refresh();
echo "Result: order id={$order->id} status={$order->status}, payment_method={$order->payment_method}\n";
