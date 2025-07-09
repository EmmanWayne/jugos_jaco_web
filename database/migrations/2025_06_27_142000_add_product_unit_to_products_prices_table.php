<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agregar relación con product_units para precios por unidad de medida
     */
    public function up(): void
    {
        Schema::table('products_prices', function (Blueprint $table) {
            $table->foreignId('product_unit_id')
                ->after('product_id')
                ->nullable()
                ->constrained('product_units')
                ->restrictOnDelete();

            $table->unique([
                'type_price_id',
                'product_id',
                'product_unit_id',
                'tax_category_id',
            ], 'unique_price_per_unit_tax');

            $table->index(['product_id', 'product_unit_id'], 'product_unit_prices');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_prices', function (Blueprint $table) {
            $table->dropForeign(['product_unit_id']);
            $table->dropUnique('unique_price_per_unit_tax');
            $table->dropIndex('product_unit_prices');
            $table->dropColumn('product_unit_id');

            // Restaurar constraint único original (si existe)
            // $table->unique(['type_price_id', 'product_id']);
        });
    }
};
