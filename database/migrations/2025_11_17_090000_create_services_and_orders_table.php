<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price_per_kg', 10, 2);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            
            // ************ KOREKSI KRITIS ************
            // Tambahkan foreign key customer_id ke tabel users (untuk Customer)
            $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null');
            // ****************************************
            
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            
            // Sesuaikan fields: Awalnya mungkin null, diisi Kurir/Admin
            $table->decimal('actual_weight', 8, 2)->nullable(); 
            $table->decimal('total_price', 10, 2)->default(0);

            $table->string('payment_method');
            // Ubah default status ke awal proses (Menunggu Kurir Jemput)
            $table->string('status')->default('menunggu_jemput'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('services');
    }
};