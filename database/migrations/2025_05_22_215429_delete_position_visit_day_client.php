<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('position');
            $table->dropColumn('visit_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->tinyInteger('position')->unsigned()->nullable();
            $table->enum('visit_day', ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'])->nullable();
        });
    }
};
