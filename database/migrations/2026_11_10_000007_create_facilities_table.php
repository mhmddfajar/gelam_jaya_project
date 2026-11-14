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
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->enum('category', [
                'pendidikan',
                'kesehatan',
                'ibadah',
                'olahraga',
                'jalan_transportasi',
                'fasilitas_umum',
            ]);
            $table->string('location', 150)->nullable();
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->enum('condition', [
                'baik',
                'rusak_ringan',
                'rusak_berat',
            ]);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};

