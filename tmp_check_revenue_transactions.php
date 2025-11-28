<?php
require __DIR__.'/vendor/autoload.php';
$kernel = require __DIR__.'/bootstrap/app.php';
$kernel->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB; use Carbon\Carbon; use Illuminate\Support\Facades\Schema;

if (!Schema::hasTable('transactions')) { echo "No transactions table\n"; exit(1); }

$income_today = DB::table('transactions')->whereDate('created_at', Carbon::today())->sum('amount');
$total_revenue_month = DB::table('transactions')->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('amount');

echo "Income Today (transactions): Rp " . number_format($income_today, 0, ',', '.') . "\n";
echo "Revenue This Month (transactions): Rp " . number_format($total_revenue_month, 0, ',', '.') . "\n";