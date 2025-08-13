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
        Schema::table('products_prices', function (Blueprint $table) {
            $table->foreignId('tax_category_id')
                ->nullable()
                ->after('price')
                ->constrained('tax_categories')->restrictOnDelete();
            $table->boolean('price_include_tax')
                ->default(false)
                ->after('tax_category_id')
                ->comment('Indica si el precio incluye el impuesto o no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_prices', function (Blueprint $table) {
            $table->dropForeign(['tax_category_id']);
            $table->dropColumn(['tax_category_id', 'price_include_tax']);
        });
    }
};
