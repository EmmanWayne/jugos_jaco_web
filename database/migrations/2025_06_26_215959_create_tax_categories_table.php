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
        Schema::create('tax_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64);
            $table->decimal('rate', 5, 2)->default(0.00);
            $table->integer('sequence_invoice')->default(1);
            $table->enum('type_tax_use', ['sale', 'purchase', 'all'])->default('sale');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->enum('display_type', ['tax_source', 'base_display', 'tax_display'])
                ->default('tax_source');
            $table->foreignId('base_tax_id')
                ->nullable()
                ->constrained('tax_categories');
            $table->boolean('is_for_products')
                ->default(true);
            $table->enum('calculation_type', ['base', 'tax', 'exempt'])
                ->default('tax');

            $table->timestamps();

            $table->index(['is_active', 'type_tax_use']);
            $table->index(['display_type', 'is_for_products']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_categories');
    }
};
