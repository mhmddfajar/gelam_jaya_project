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
        Schema::create('letter_requests', function (Blueprint $table) {
            $table->id();
            $table->string('registration_no', 30)->unique();
            $table->foreignId('letter_type_id')->constrained('letter_types');
            $table->foreignId('resident_id')->constrained('residents');
            $table->foreignId('submitted_by')->constrained('users');
            $table->string('channel')->index(); // 'daring' | 'luring'
            $table->string('status')->index();  // 'diajukan' | 'perlu_perbaikan' | 'diverifikasi' | 'diproses' | 'siap_diambil' | 'selesai' | 'ditolak'
            $table->string('purpose', 255);
            $table->json('form_data')->nullable();
            $table->text('status_note')->nullable();
            $table->string('letter_number', 50)->nullable()->unique();
            $table->date('letter_date')->nullable();
            $table->foreignId('signer_id')->nullable()->constrained('officials');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });

        Schema::create('request_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_request_id')->constrained('letter_requests')->cascadeOnDelete();
            $table->foreignId('requirement_id')->nullable()->constrained('letter_type_requirements')->nullOnDelete();
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type', 100);
            $table->unsignedInteger('size_kb');
            $table->timestamps();
        });

        Schema::create('request_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_request_id')->constrained('letter_requests')->cascadeOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('note')->nullable();
            $table->foreignId('changed_by')->constrained('users');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_status_logs');
        Schema::dropIfExists('request_attachments');
        Schema::dropIfExists('letter_requests');
    }
};
