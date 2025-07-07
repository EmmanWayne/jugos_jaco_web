<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            
            $table->string('name', 50);
            $table->string('abbreviation', 10);
            
            $table->enum('category', ['count', 'weight', 'volume', 'length', 'area']);
            $table->text('description')->nullable();
            
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            $table->unique('name', 'unique_unit_name');
            $table->unique('abbreviation', 'unique_unit_abbreviation');
            
            $table->index(['category', 'is_active'], 'category_active_units');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
