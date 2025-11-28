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

$order = Order::whereNull('courier_id')->where('status','ready_for_delivery')->first();
if(!$order) { echo "No unassigned ready_for_delivery order found\n"; exit; }

echo "Attempting to claim order {$order->id} by courier {$courier->id}\n";
$controller = new CourierOrderController();
$controller->claim($order);

$order->refresh();
echo "Result: order courier_id={$order->courier_id} status={$order->status}\n";
