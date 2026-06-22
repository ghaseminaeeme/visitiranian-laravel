<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('address')->nullable();
            $table->foreignId('city_id')->constrained()->restrictOnDelete();
            $table->string('phone', 20)->nullable();
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('city_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinics');
    }
};
