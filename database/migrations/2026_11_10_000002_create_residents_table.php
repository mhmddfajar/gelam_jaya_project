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
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->char('nik', 16)->unique();
            $table->char('family_card_no', 16)->nullable()->index();
            $table->string('name', 150);
            $table->string('birth_place', 100);
            $table->date('birth_date');
            $table->enum('gender', ['L', 'P']);
            $table->string('religion', 30);
            $table->string('marital_status', 30);
            $table->string('occupation', 100)->nullable();
            $table->string('education', 50)->nullable();
            $table->string('citizenship', 10)->default('WNI');
            $table->string('address', 255);
            $table->foreignId('hamlet_id')->nullable()->constrained('hamlets')->nullOnDelete();
            $table->string('rt', 3);
            $table->string('rw', 3);
            $table->string('family_relation', 50)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};

