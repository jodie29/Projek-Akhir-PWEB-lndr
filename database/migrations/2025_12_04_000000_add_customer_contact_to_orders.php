<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Store walk-in / guest customer contact details to help couriers contact customers
            $table->string('customer_name')->nullable()->after('customer_id');
            $table->string('customer_phone')->nullable()->after('customer_name');
            $table->text('pickup_address')->nullable()->after('customer_phone');
            // Keep a generic 'address' alias if used elsewhere
            if (! Schema::hasColumn('orders', 'address')) {
                $table->text('address')->nullable()->after('pickup_address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'address')) {
                $table->dropColumn('address');
            }
            $table->dropColumn(['pickup_address', 'customer_phone', 'customer_name']);
        });
    }
};
