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
        Schema::table('detail_assigned_products', function (Blueprint $table) {
            $table->decimal('sale_quantity', 12, 2)->default(0)->after('quantity')->comment('Cantidad vendida del producto asignado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_assigned_products', function (Blueprint $table) {
            $table->dropColumn('sale_qty');
        });
    }
};
