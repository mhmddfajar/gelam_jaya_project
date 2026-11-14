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
        Schema::table('village_profiles', function (Blueprint $table) {
            $table->unsignedInteger('total_population')->nullable()->after('area_size_ha');
            $table->unsignedInteger('total_households')->nullable()->after('total_population');
            $table->unsignedInteger('male_population')->nullable()->after('total_households');
            $table->unsignedInteger('female_population')->nullable()->after('male_population');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('village_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'total_population',
                'total_households',
                'male_population',
                'female_population',
            ]);
        });
    }
};

