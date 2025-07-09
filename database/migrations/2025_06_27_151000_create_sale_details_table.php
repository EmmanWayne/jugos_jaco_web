<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sale_id')->constrained('sales')->onDeleteRestrict();

            $table->foreignId('product_id')->constrained('products');
            $table->string('product_name');
            $table->string('product_code')->nullable();

            $table->foreignId('type_price_id')->constrained('types_prices')
                ->comment('Tipo de precio aplicado en esta venta específica');

            // Inmutabilidad en la inforamcion del tipo de unidad
            $table->string('unit_name', 50)
                ->comment('Nombre de la unidad al momento de la venta');
            $table->string('unit_abbreviation', 10)->nullable()
                ->comment('Abreviatura de la unidad al momento de la venta');

            $table->decimal('quantity', 12, 4);
            $table->decimal('base_quantity', 12, 4)
                ->comment('Cantidad equivalente en unidades base (para reportes unificados)');

            $table->decimal('unit_price_without_tax', 15, 4);
            $table->decimal('unit_tax_amount', 15, 4)->default(0);

            $table->foreignId('tax_category_id')->nullable()->constrained('tax_categories');

            // Inmutabilidad en el nombre de la categoría fiscal
            $table->string('tax_category_name')->nullable();

            $table->decimal('line_subtotal', 15, 4)
                ->comment('quantity * unit_price_without_tax');
            $table->decimal('line_tax_amount', 15, 4)->default(0)
                ->comment('quantity * unit_tax_amount');
            $table->decimal('line_total', 15, 4)
                ->comment('(line_subtotal + line_tax_amount) - discount_amount');

            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->decimal('discount_amount', 15, 4)->default(0.0000);

            $table->timestamps();

            $table->index('sale_id');
            $table->index('product_id');
            $table->index('tax_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_details');
    }
};
