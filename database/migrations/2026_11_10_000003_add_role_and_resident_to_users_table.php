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
            $table->string('role', 20)->default('warga')->index()->after('password');
            $table->foreignId('resident_id')->nullable()->unique()->after('role')->constrained('residents')->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('resident_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['resident_id']);
            $table->dropColumn(['role', 'resident_id', 'is_active']);
        });
    }
};

