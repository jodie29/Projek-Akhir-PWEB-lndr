<?php
require __DIR__.'/vendor/autoload.php';
$kernel = require __DIR__.'/bootstrap/app.php';
$kernel->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Http\Controllers\Admin\OrderApprovalController;

$admin = User::where('role','admin')->first();
if(!$admin) { echo "No admin found\n"; exit; }
Auth::loginUsingId($admin->id);

$order = Order::where('payment_method','Bayar Nanti')->whereNotIn('status',['processing','awaiting_confirmation','paid'])->first();
if(!$order) { echo "No suitable Bayar Nanti order found to test skip\n"; exit; }

echo "Approving order {$order->id} with skip_confirmation as admin {$admin->id}\n";
$controller = new OrderApprovalController();
$request = Request::create('/admin/orders/'.$order->id.'/approve', 'POST', ['actual_weight' => 2, 'skip_confirmation' => 1]);
$controller->approve($request, $order->id);
$order->refresh();
echo "Result: status={$order->status} confirmed={$order->customer_confirmed} token={$order->confirmation_token}\n";
