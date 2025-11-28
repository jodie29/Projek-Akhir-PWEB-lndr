<?php
require __DIR__.'/vendor/autoload.php';
$kernel = require __DIR__.'/bootstrap/app.php';
$kernel->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

$courier = User::where('role', 'courier')->first();
if(!$courier) {
    echo "No courier user found\n"; exit(0);
}
echo "Courier: {$courier->id} - {$courier->email}\n";

$pendingStatuses = ['menunggu_jemput', 'pending', 'ready_for_delivery'];
$unassignedStatuses = array_merge($pendingStatuses, ['ready_for_delivery']);
$inProcessStatuses = ['dijemput', 'diantar', 'di_laundry', 'in_laundry', 'processing'];
$completedStatuses = ['selesai','delivered','paid'];

$pendingPickups = Order::where('courier_id', $courier->id)
                ->whereIn('status', $pendingStatuses)
                ->get();

$inProcessOrders = Order::where('courier_id', $courier->id)
                ->whereIn('status', $inProcessStatuses)
                ->get();

$completed = Order::where('courier_id', $courier->id)
                ->whereIn('status', $completedStatuses)
                ->get();

$unassigned = Order::whereNull('courier_id')
                ->whereIn('status', $unassignedStatuses)
                ->get();

echo "Assigned pending: " . $pendingPickups->count() . "\n";
foreach($pendingPickups as $o) echo "  - {$o->id} {$o->order_number} status={$o->status} courier={$o->courier_id}\n";
echo "In process: " . $inProcessOrders->count() . "\n";
foreach($inProcessOrders as $o) echo "  - {$o->id} {$o->order_number} status={$o->status} courier={$o->courier_id}\n";
echo "Completed: " . $completed->count() . "\n";
foreach($completed as $o) echo "  - {$o->id} {$o->order_number} status={$o->status} courier={$o->courier_id}\n";
echo "Unassigned (couriers can claim): " . $unassigned->count() . "\n";
foreach($unassigned as $o) echo "  - {$o->id} {$o->order_number} status={$o->status} courier=" . ($o->courier_id?:'NULL') . "\n";

// As a sanity check, ensure there are orders in DB where status=ready_for_delivery and courier_id NULL
$check = Order::where('status','ready_for_delivery')->whereNull('courier_id')->count();
echo "Orders ready_for_delivery with no courier: {$check}\n";

// Transaction history for this courier
$transactionHistory = Order::where('collected_by', $courier->id)
                ->whereNotNull('collected_at')
                ->with('customer', 'service')
                ->latest('collected_at')
                ->limit(20)
                ->get();
echo "Transaction history (collected_by={$courier->id}): " . $transactionHistory->count() . "\n";
foreach($transactionHistory as $t) echo "  - {$t->id} {$t->order_number} method={$t->collection_method} amount={$t->collected_amount} at=" . ($t->collected_at? $t->collected_at->format('Y-m-d H:i'): '-') . "\n";

// Recent assigned orders
$recentOrders = Order::where('courier_id', $courier->id)->latest()->limit(5)->get();
echo "Recent orders assigned to courier: " . $recentOrders->count() . "\n";
foreach($recentOrders as $o) echo "  - {$o->id} {$o->order_number} status={$o->status} created_at=" . ($o->created_at ? $o->created_at->format('Y-m-d H:i') : '-') . "\n";
