<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Si la table n'existe pas, on la crée complète
        if (! Schema::hasTable('pages')) {
            Schema::create('pages', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->longText('content')->nullable();
                $table->boolean('is_published')->default(true);
                $table->timestamps();
            });
            return;
        }

        // Sinon on ajoute juste les colonnes manquantes
        Schema::table('pages', function (Blueprint $table) {
            if (! Schema::hasColumn('pages', 'title')) {
                $table->string('title')->after('id');
            }
            if (! Schema::hasColumn('pages', 'slug')) {
                $table->string('slug')->unique()->after('title');
            }
            if (! Schema::hasColumn('pages', 'content')) {
                $table->longText('content')->nullable()->after('slug');
            }
            if (! Schema::hasColumn('pages', 'is_published')) {
                $table->boolean('is_published')->default(true)->after('content');
            }
        });
    }

    public function down(): void
    {
        // On ne détruit rien (safe). Si besoin, commenter ce choix.
        // Schema::dropIfExists('pages');
    }
};
