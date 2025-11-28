<?php
require __DIR__.'/vendor/autoload.php';
$kernel = require __DIR__.'/bootstrap/app.php';
$kernel->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\User;
use App\Http\Controllers\Customer\CustomerController;

$customer = User::where('role','customer')->first();
if(!$customer) { echo "No customer found\n"; exit; }

Auth::loginUsingId($customer->id);

$order = Order::where('customer_id', $customer->id)->whereIn('status',['awaiting_confirmation','approved','pending'])->first();
if(!$order) { echo "No order found to confirm for customer {$customer->id}\n"; exit; }

echo "Confirming order {$order->id} as customer {$customer->id}\n";
$controller = new CustomerController();
$controller->confirmPrice(new Illuminate\Http\Request(), $order->id);
$order->refresh();
echo "Result: status={$order->status} confirmed={$order->customer_confirmed} token={$order->confirmation_token}\n";
