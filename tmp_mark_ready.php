<?php
require __DIR__.'/vendor/autoload.php';
$kernel = require __DIR__.'/bootstrap/app.php';
$kernel->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$controller = new App\Http\Controllers\Admin\OrderController();
try {
    $request = \Illuminate\Http\Request::create('/admin/orders/1/ready-for-delivery', 'POST', ['auto_assign' => 0]);
    // Pass the Request instance then the order ID
    $controller->markReadyForDelivery($request, 1);
    echo "Marked ready\n";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
