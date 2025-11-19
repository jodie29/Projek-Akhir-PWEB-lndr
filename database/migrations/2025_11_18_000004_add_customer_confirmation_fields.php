<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('customer_confirmed')->default(false)->after('confirmation_token');
            $table->timestamp('customer_confirmed_at')->nullable()->after('customer_confirmed');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['customer_confirmed', 'customer_confirmed_at']);
        });
    }
};
