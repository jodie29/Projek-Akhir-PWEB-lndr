<?php
require __DIR__.'/vendor/autoload.php';
use App\Http\Controllers\Admin\OrderController;
use Illuminate\Support\Facades\App as AppFacade;

$kernel = require __DIR__.'/bootstrap/app.php';
$kernel->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$controller = new OrderController();
try {
    $controller->resendConfirmation(1);
    echo "Resend called\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
