<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
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
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->decimal('actual_weight', 8, 2);
            $table->decimal('total_price', 10, 2);
            $table->string('payment_method');
            $table->string('status')->default('paid');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('services');
    }
};
