<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->string('level');
            $table->text('message');
            $table->string('exception_class')->nullable();
            $table->string('file')->nullable();
            $table->unsignedInteger('line')->nullable();
            $table->longText('stack_trace')->nullable();
            $table->string('url')->nullable();
            $table->string('http_method', 10)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('context')->nullable();
            $table->json('request_input')->nullable();
            $table->timestamp('occurred_at');
            $table->string('status')->default('new');
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolved_note')->nullable();
            $table->timestamps();

            $table->index('occurred_at');
            $table->index(['status', 'level']);
            $table->index('url');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('error_logs');
    }
};
