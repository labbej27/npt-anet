<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_session_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->enum('status', ['confirmed','cancelled'])->default('confirmed');
            $table->string('cancel_token')->nullable();
            $table->timestamps();
            $table->unique(['workshop_session_id','email']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('reservations');
    }
};
