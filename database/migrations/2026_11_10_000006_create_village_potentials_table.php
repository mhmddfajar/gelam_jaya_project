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
        Schema::create('village_potentials', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->enum('category', [
                'pertanian',
                'peternakan',
                'perikanan',
                'umkm',
                'wisata',
                'lainnya',
            ]);
            $table->text('description');
            $table->string('location', 150)->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('village_potentials');
    }
};

