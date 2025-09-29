<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('workshop_sessions', function (Blueprint $table) {
            $table->id();
            $table->date('date'); // Date du mercredi
            $table->time('start_time'); // 14:00, 15:00, 16:00
            $table->time('end_time');   // 15:00, 16:00, 17:00
            $table->unsignedTinyInteger('capacity')->default(5);
            $table->string('location')->default("Mairie d'Anet");
            $table->string('topic')->default('Inclusion numérique – logiciels libres');
            $table->timestamps();
            $table->unique(['date','start_time']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('workshop_sessions');
    }
};
