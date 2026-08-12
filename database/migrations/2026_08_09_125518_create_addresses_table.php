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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            // realsi ke user
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // informasi user
            $table->string('recipient_name');
            $table->string('phone', 20);

            // label alamat
            $table->string('label', 50);

            $table->text('full_address');


            // Pilihan Lokasi di indonesia

            // Provinsi
            $table->string('province_id');
            $table->string('province_name');

            // Kota / Kabupaten 
            $table->string('city_id');
            $table->string('city_name');

            // Kecamatan 
            $table->string('district_id');
            $table->string('district_name');

            // Kelurahan atau desa
            // Nullable karena tidak semua kebutuhan pengiriman membutuhkannya
            $table->string('subdistrict_id')->nullable();
            $table->string('subdistrict_name')->nullable();

            // Kode pos
            $table->string('postal_code', 10);
            

            // Pinpoint lokasi 
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();


            // apakah alamat default
            $table->boolean('is_default')->default(false);


            // Soft delete agar tidak langsung menghapus alamat
            $table->softDeletes();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
