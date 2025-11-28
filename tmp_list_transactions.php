<?php
require __DIR__.'/vendor/autoload.php';
$kernel = require __DIR__.'/bootstrap/app.php';
$kernel->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\Transaction; use Illuminate\Support\Facades\Schema;
if (!Schema::hasTable('transactions')) {
    echo "Transactions table not found. Please run migrations (php artisan migrate) to create it.\n";
    exit(1);
}
foreach(Transaction::with('order')->get() as $t) {
    echo sprintf("%d order=%s amount=%s method=%s created_by=%s created_at=%s\n", $t->id, $t->order_id ?? '-', $t->amount, $t->method ?? '-', $t->created_by ?? '-', $t->created_at->format('Y-m-d H:i:s'));
}
