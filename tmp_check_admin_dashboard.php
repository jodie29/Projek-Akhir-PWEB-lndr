<?php
require __DIR__.'/vendor/autoload.php';
$kernel = require __DIR__.'/bootstrap/app.php';
$kernel->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Models\Order;

$controller = new AdminDashboardController();
$reflection = new ReflectionClass($controller);
// call index method to build view data
ob_start();
try {
    $controller->index();
} catch (Throwable $e) {
    // controllers typically return views which print nothing in CLI; instead inspect data via model
}

// Instead, we'll just query the last 5 orders and print them
$orders = Order::with('service')->latest()->limit(5)->get();
echo "Admin Latest 5 Orders:\n";
foreach ($orders as $o) {
    echo "- {$o->id} | {$o->order_number} | status: {$o->status} | confirmation_token: " . ($o->confirmation_token ?: '-') . "\n";
}
