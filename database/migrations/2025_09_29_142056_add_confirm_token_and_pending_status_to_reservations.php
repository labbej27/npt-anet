<?php
// database/migrations/XXXX_XX_XX_XXXXXX_add_confirm_token_and_pending_status_to_reservations.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'confirm_token')) {
                $table->string('confirm_token')->nullable()->after('cancel_token');
            }
        });

        // MySQL/MariaDB : élargir l'ENUM + défaut "pending"
        DB::statement("ALTER TABLE reservations MODIFY status ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void {
        // ⚠️ seulement si vous supprimez toutes les lignes 'pending' avant
        DB::statement("ALTER TABLE reservations MODIFY status ENUM('confirmed','cancelled') NOT NULL DEFAULT 'confirmed'");
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('confirm_token');
        });
    }
};
