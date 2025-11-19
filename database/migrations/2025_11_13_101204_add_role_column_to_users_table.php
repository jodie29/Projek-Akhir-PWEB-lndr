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
        Schema::table('users', function (Blueprint $table) {
            // Kita tambahkan kolom 'role' setelah kolom 'password'
            $table->enum('role', ['admin', 'cashier', 'courier', 'customer'])
                  ->default('customer')
                  ->after('password'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Untuk rollback/undo, hapus kolom 'role'
            $table->dropColumn('role');
        });
    }
};