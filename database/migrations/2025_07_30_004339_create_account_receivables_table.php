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
        Schema::create('account_receivables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->string('name',  64)->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->decimal('remaining_balance', 15, 2);
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->date('due_date')->nullable();
            $table->string('notes', 120)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_receivables');
    }
};
