<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Añadir payment_term como nuevo campo
            $table->enum('payment_term', ['cash', 'credit'])->default('cash')->after('status');
            
            // Renombrar el campo payment_type a payment_method para mayor claridad
            $table->renameColumn('payment_type', 'payment_method');
            
            // Añadir índice para facilitar búsquedas por término de pago
            $table->index('payment_term');
        });

        // Migración de datos: Aseguramos consistencia entre payment_term y payment_method
        DB::statement('UPDATE sales SET payment_term = "credit" WHERE payment_method = "credit"');
        DB::statement('UPDATE sales SET payment_term = "cash" WHERE payment_method IN ("cash", "deposit", "card")');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Eliminar el nuevo campo
            $table->dropColumn('payment_term');
            
            // Restaurar el nombre original del campo
            $table->renameColumn('payment_method', 'payment_type');
        });
    }
};
