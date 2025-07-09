<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_tax_totals', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('tax_category_id')->constrained('tax_categories')->restrictOnDelete();
            
            $table->string('tax_category_name', 64);
            
            $table->decimal('total_amount', 15, 4);
            
            $table->timestamps();
            
            $table->unique(['sale_id', 'tax_category_id'], 'unique_sale_tax_category');
                        
            $table->index('sale_id');
            $table->index('tax_category_id');
            $table->index(['sale_id', 'tax_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_tax_totals');
    }
};
