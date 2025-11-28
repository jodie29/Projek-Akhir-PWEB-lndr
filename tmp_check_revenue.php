<?php
require __DIR__.'/vendor/autoload.php';
$kernel = require __DIR__.'/bootstrap/app.php';
$kernel->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\Order; use Carbon\Carbon; use Illuminate\Support\Facades\DB;

$income_today = Order::where(function ($q) { $q->where('status', 'paid') ->whereDate('approved_at', Carbon::today()); })
    ->orWhere(function ($q) { $q->whereNotNull('collected_at')->whereDate('collected_at', Carbon::today()); })
    ->sum(DB::raw('COALESCE(collected_amount, total_price)'));

$total_revenue_month = Order::where(function ($q) { $q->where('status', 'paid') ->whereBetween('approved_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]); })
    ->orWhere(function ($q) { $q->whereNotNull('collected_at')->whereBetween('collected_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]); })
    ->sum(DB::raw('COALESCE(collected_amount, total_price)'));

echo "Income Today: Rp " . number_format($income_today, 0, ',', '.') . "\n";
echo "Revenue This Month: Rp " . number_format($total_revenue_month, 0, ',', '.') . "\n";
?>