<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('bio')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('city_id')->constrained()->restrictOnDelete();
            $table->foreignId('primary_specialty_id')->nullable()->constrained('specialties')->nullOnDelete();
            $table->string('sms_mobile', 20)->nullable();
            $table->string('name_normalized')->nullable();
            $table->text('search_text')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_vip')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['city_id', 'is_published', 'is_active', 'is_vip']);
            $table->index(['is_published', 'published_at']);
            $table->index('expires_at');
            $table->index('sms_mobile');
            $table->fullText(['name', 'search_text']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
