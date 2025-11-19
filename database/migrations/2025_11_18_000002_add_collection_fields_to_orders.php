<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('collected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('collected_at')->nullable();
            $table->decimal('collected_amount', 10, 2)->nullable();
            $table->string('collection_method')->nullable();
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('collected_by');
            $table->dropColumn(['collected_at', 'collected_amount', 'collection_method']);
        });
    }
};
