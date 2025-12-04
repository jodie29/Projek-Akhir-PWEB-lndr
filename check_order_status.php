<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Step 1: Check all orders and statuses
echo "=== STEP 1: Current Orders Status ===\n";
$allOrders = \App\Models\Order::all();
foreach ($allOrders as $order) {
    echo "Order {$order->id} ({$order->order_number}): Status = '{$order->status}', Courier = {$order->courier_id}\n";
}

// Step 2: Simulate picking up Order 1 and 2
echo "\n=== STEP 2: Simulating Order State Transitions ===\n";

// Transition Order 1: ready_for_delivery -> menunggu_jemput -> dijemput
$order1 = \App\Models\Order::find(1);
if ($order1) {
    echo "\nOrder 1 Current Status: '{$order1->status}'\n";
    
    // Claim by courier 3
    if ($order1->status === 'ready_for_delivery') {
        $order1->courier_id = 3;
        $order1->status = 'menunggu_jemput';
        $order1->save();
        echo "  -> Claimed by Courier 3, Status changed to: '{$order1->status}'\n";
    }
    
    // Then pick up (Jemput)
    if (in_array($order1->status, ['menunggu_jemput', 'pending', 'ready_for_delivery'])) {
        $order1->status = 'dijemput';
        $order1->save();
        echo "  -> Picked up, Status changed to: '{$order1->status}'\n";
    }
    
    echo "  -> Should show 'Tandai Diantar' button? " . (in_array($order1->status, ['dijemput']) ? "YES ✓" : "NO ✗") . "\n";
}

// Step 3: Check which section each order should appear in
echo "\n=== STEP 3: Order Placement by Section ===\n";
foreach ($allOrders->fresh() as $order) {
    $order->refresh(); // Reload latest data
    
    // Check which list it belongs to
    $pendingStatuses = ['menunggu_jemput', 'pending', 'ready_for_delivery'];
    $inProcessStatuses = ['dijemput', 'diantar', 'di_laundry', 'in_laundry', 'processing'];
    $completedStatuses = ['selesai', 'delivered', 'paid'];
    
    $section = 'UNKNOWN';
    if (in_array($order->status, $pendingStatuses)) {
        $section = 'Daftar Penjemputan';
    } elseif (in_array($order->status, $inProcessStatuses)) {
        $section = 'Sedang Proses';
    } elseif (in_array($order->status, $completedStatuses)) {
        $section = 'Selesai';
    }
    
    echo "Order {$order->id} (Status: '{$order->status}'): Should appear in '{$section}' section\n";
}
