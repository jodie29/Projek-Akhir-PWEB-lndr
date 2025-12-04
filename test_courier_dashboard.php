<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== COURIER DASHBOARD TEST ===\n";
echo "Testing view rendering for courier dashboard\n\n";

// Get Courier User
$courier = \App\Models\User::find(3); // Kurir Cepat Asep
if (!$courier) {
    echo "ERROR: Courier not found!\n";
    exit;
}

echo "Logged in as: {$courier->name} (ID: {$courier->id})\n";
echo "Role: {$courier->role}\n\n";

// Simulate what controller does
$pendingStatuses = ['menunggu_jemput', 'pending', 'ready_for_delivery'];
$inProcessStatuses = ['dijemput', 'diantar', 'di_laundry', 'in_laundry', 'processing'];

// Get in-process orders
$inProcessOrders = \App\Models\Order::where('courier_id', $courier->id)
    ->whereIn('status', $inProcessStatuses)
    ->with('customer', 'service')
    ->latest()
    ->get();

echo "=== IN-PROCESS ORDERS (for 'Sedang Proses' section) ===\n";
echo "Count: " . $inProcessOrders->count() . "\n\n";

foreach ($inProcessOrders as $order) {
    echo "Order {$order->id}:\n";
    echo "  - Order Number: {$order->order_number}\n";
    echo "  - Status: {$order->status}\n";
    echo "  - Customer: " . ($order->customer->name ?? 'N/A') . "\n";
    echo "  - Phone: " . ($order->customer->phone ?? $order->customer_phone ?? 'N/A') . "\n";
    
    // Check button conditions
    $shouldShowTandaiDiantar = in_array($order->status, ['dijemput']);
    $shouldShowTandaiSelesai = in_array($order->status, ['diantar']);
    
    echo "  - Show 'Tandai Diantar' button? " . ($shouldShowTandaiDiantar ? "✓ YES" : "✗ NO") . "\n";
    echo "  - Show 'Tandai Selesai' button? " . ($shouldShowTandaiSelesai ? "✓ YES" : "✗ NO") . "\n";
    echo "\n";
}

if ($inProcessOrders->count() === 0) {
    echo "No in-process orders found for this courier.\n";
    echo "\nPending orders instead:\n";
    
    $pendingOrders = \App\Models\Order::where('courier_id', $courier->id)
        ->whereIn('status', $pendingStatuses)
        ->with('customer', 'service')
        ->latest()
        ->get();
    
    foreach ($pendingOrders as $order) {
        echo "  - Order {$order->id} ({$order->order_number}): Status = {$order->status}\n";
    }
}
