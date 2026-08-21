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
        Schema::table('payments', function (Blueprint $table) {

            // Menyimpan nama gateway contoh: midtrans
            $table->string('gateway')
                ->nullable()
                ->after('order_id');

            // Menyimpan ID/reference transaksi yang kita kirim ke payment gateway. Contoh: ZIVO-ORDER-102 
            $table->string('gateway_order_id')
                ->nullable()
                ->unique()
                ->after('gateway');
            
            // Menyimpan ID transaksi yang diberikan oleh payment gateway
            $table->string('gateway_transaction_id')
                ->nullable()
                ->unique()
                ->after('gateway_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            $table->dropUnique([
                'payments_gateway_order_id_unique',
            ]);

            $table->dropUnique([
                'payments_gateway_transaction_id_unique',
            ]);

            // Hapus kolom gateway.
            $table->dropColumn([
                'gateway',
                'gateway_order_id',
                'gateway_transaction_id',
            ]);
        });
    }
};
