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
            
            // Rename kolom agar konsisten
            $table->renameColumn('customer_name', 'recipient_name');
            $table->renameColumn('address', 'full_address');
            // Province
            $table->string('province_id')->nullable();
            $table->string('province_name')->nullable();

            // City
            $table->string('city_id')->nullable();
            $table->string('city_name')->nullable();

            // District
            $table->string('district_id')->nullable();
            $table->string('district_name')->nullable();

            // Subdistrict
            $table->string('subdistrict_id')->nullable();
            $table->string('subdistrict_name')->nullable();

            // Postal code
            $table->string('postal_code', 10)->nullable();

            // Pin point
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // Rename columns back
            $table->renameColumn('recipient_name', 'customer_name');
            $table->renameColumn('full_address', 'address');

            $table->dropColumn([
                'province_id',
                'province_name',
                'city_id',
                'city_name',
                'district_id',
                'district_name',
                'subdistrict_id',
                'subdistrict_name',
                'postal_code',
                'latitude',
                'longitude',
            ]);
        });
    }
};
