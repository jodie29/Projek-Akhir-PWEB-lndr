<?php
require __DIR__.'/vendor/autoload.php';
$kernel = require __DIR__.'/bootstrap/app.php';
$kernel->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Http\Controllers\Courier\OrderController as CourierOrderController;

$courier = User::where('role','courier')->first();
if(!$courier) { echo "No courier found\n"; exit; }
Auth::loginUsingId($courier->id);

$order = Order::where('courier_id', $courier->id)->whereIn('status', ['ready_for_delivery','dijemput'])->first();
if(!$order) { echo "No order in 'ready_for_delivery' or 'dijemput' assigned to courier\n"; exit; }

echo "Attempting collection for order {$order->id} (status={$order->status}) by courier {$courier->id}\n";

$controller = new CourierOrderController();
$request = Request::create('/courier/orders/'.$order->id.'/collect', 'POST', [
    'collection_method' => 'Tunai',
    'collected_amount' => $order->total_price ?? 0,
]);
$controller->collect($request, $order);
$order->refresh();
echo "Result: order id={$order->id} status={$order->status} collected_by={$order->collected_by} collected_amount={$order->collected_amount}\n";
