<?php
require __DIR__.'/vendor/autoload.php';
$kernel=require __DIR__.'/bootstrap/app.php';
$kernel->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
// Log in as courier id 3
Illuminate\Support\Facades\Auth::loginUsingId(3);
$controller = new App\Http\Controllers\Courier\OrderController();
try {
    $controller->pickedUp(App\Models\Order::find(1));
    echo "Picked up called\n";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
