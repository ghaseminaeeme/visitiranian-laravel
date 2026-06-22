<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_waitlist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->string('patient_name');
            $table->string('patient_phone', 20);
            $table->string('patient_national_code', 10);
            $table->date('preferred_date');
            $table->string('status')->default('pending');
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['doctor_id', 'preferred_date', 'status']);
            $table->index(['patient_phone', 'patient_national_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_waitlist');
    }
};
