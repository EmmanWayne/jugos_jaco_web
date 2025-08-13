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
            Schema::create('product_units', function (Blueprint $table) {
                  $table->id();

                  $table->foreignId('product_id')->constrained('products')->onDeleteRestrict();

                  $table->foreignId('unit_id')
                        ->constrained('units')
                        ->restrictOnDelete();

                  $table->decimal('conversion_factor', 10, 2)
                        ->comment('Factor de conversión a la unidad base (ej: 1 caja = 24 unidades)');

                  $table->boolean('is_base_unit')->default(false)
                        ->comment('Si es la unidad base para control de inventario');
                  $table->boolean('is_sellable')->default(true)
                        ->comment('Si se puede vender en esta unidad');
                  $table->boolean('is_purchasable')->default(false)
                        ->comment('Si se puede comprar en esta unidad');
                  $table->boolean('is_active')->default(true)
                        ->comment('Si la unidad está activa');

                  $table->timestamps();

                  $table->unique(['product_id', 'unit_id'], 'unique_product_unit');
                  $table->unique(['product_id', 'unit_id', 'is_base_unit'], 'unique_product_base_unit');
                  $table->index(['product_id', 'is_sellable'], 'product_sellable_units');
                  $table->index(['product_id', 'is_base_unit'], 'product_base_unit');
            });
      }

      /**
       * Reverse the migrations.
       */
      public function down(): void
      {
            Schema::dropIfExists('product_units');
      }
};
