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
        Schema::create('village_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('village_code', 30)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('district', 100); // kecamatan
            $table->string('regency', 100);  // kabupaten
            $table->string('province', 100);
            $table->string('postal_code', 10)->nullable();
            $table->string('logo_path')->nullable();
            $table->decimal('area_size_ha', 10, 2)->nullable();
            $table->string('boundary_north', 150)->nullable();
            $table->string('boundary_south', 150)->nullable();
            $table->string('boundary_east', 150)->nullable();
            $table->string('boundary_west', 150)->nullable();
            $table->text('vision')->nullable();
            $table->longText('mission')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('village_profiles');
    }
};

