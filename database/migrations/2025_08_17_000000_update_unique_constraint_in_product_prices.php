<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Modificar la restricción única en la tabla products_prices para garantizar
     * que solo exista un precio por cada combinación de producto, tipo de precio y unidad,
     * independientemente de la categoría de impuesto.
     */
    public function up(): void
    {
        Schema::table('products_prices', function (Blueprint $table) {            
            $table->unique([
                'type_price_id',
                'product_id',
                'product_unit_id',
            ], 'unique_price_per_unit');
        });
    }

    /**
     * Revierte los cambios, restaurando la restricción original.
     */
    public function down(): void
    {
        Schema::table('products_prices', function (Blueprint $table) {
            $table->dropUnique('unique_price_per_unit');
        });
    }
};
