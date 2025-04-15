<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_materials_inventory', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit')->default('unidad'); // unidad, kg, litro, etc.
            $table->decimal('quantity', 10, 2)->default(0);
            $table->decimal('min_stock', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_materials_inventory');
    }
};
