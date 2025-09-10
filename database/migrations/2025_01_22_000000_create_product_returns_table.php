<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('reconciliation_id')
                  ->nullable()
                  ->constrained('daily_sales_reconciliations')
                  ->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->enum('type', ['damaged', 'returned']);
            $table->string('reason');
            $table->text('description')->nullable();
            $table->boolean('affects_inventory')->default(true)->comment('Indica si la devolución debe generar movimiento de inventario');
            $table->timestamps();

            $table->index(['reconciliation_id', 'type']);
            $table->index(['product_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_returns');
    }
};