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
        Schema::table('sale_details', function (Blueprint $table) {
            // Añadir product_price_id para trazabilidad completa
            $table->foreignId('product_price_id')
                ->nullable()
                ->after('product_code')
                ->constrained('products_prices')
                ->comment('ID del precio de producto utilizado en esta venta');
            
            // Añadir tax_rate para transparencia en el cálculo de impuestos
            $table->decimal('tax_rate', 5, 2)
                ->default(0.00)
                ->after('tax_category_name')
                ->comment('Tasa de impuesto aplicada (porcentaje)');
                
            // Añadir price_include_tax para claridad en el precio original
            $table->boolean('price_include_tax')
                ->default(false)
                ->after('tax_rate')
                ->comment('Indica si el precio original incluía impuesto');
                
            // Añadir product_unit_id para referencia a la unidad de producto
            $table->foreignId('product_unit_id')
                ->nullable()
                ->after('unit_abbreviation')
                ->constrained('product_units')
                ->comment('Referencia a la unidad de producto utilizada');
                
            // Añadir conversion_factor para cálculos de inventario
            $table->decimal('conversion_factor', 10, 2)
                ->default(1.0000)
                ->after('product_unit_id')
                ->comment('Factor de conversión de la unidad usada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_details', function (Blueprint $table) {
            $table->dropForeign(['product_price_id']);
            $table->dropForeign(['product_unit_id']);
            $table->dropColumn([
                'product_price_id',
                'tax_rate',
                'price_include_tax',
                'product_unit_id',
                'conversion_factor'
            ]);
        });
    }
};
