<?php
// database/migrations/2025_09_29_000200_create_pages_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique(); // ex: contact, mentions-legales
            $table->longText('content')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('pages'); }
};
