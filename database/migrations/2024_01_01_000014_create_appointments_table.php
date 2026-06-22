<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('patient_name');
            $table->string('patient_phone', 20);
            $table->string('patient_national_code', 10);
            $table->string('tracking_code', 8)->unique();
            $table->string('status')->default('confirmed');
            $table->timestamp('booked_at');
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('reminder_24h_sent_at')->nullable();
            $table->timestamp('reminder_2h_sent_at')->nullable();
            $table->timestamps();

            $table->index(['doctor_id', 'starts_at', 'status']);
            $table->index(['patient_phone', 'patient_national_code']);
            $table->index(['status', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
