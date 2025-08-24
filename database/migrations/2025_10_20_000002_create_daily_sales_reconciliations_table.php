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
        Schema::create('daily_sales_reconciliations', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->foreignId('cashier_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            
            // Reconciliation date
            $table->date('reconciliation_date');
            
            // Sales amounts
            $table->decimal('total_cash_sales', 15, 2)->default(0);
            $table->decimal('total_credit_sales', 15, 2)->default(0);
            $table->decimal('total_sales', 15, 2)->default(0)->comment('Sum of cash and credit sales');
            
            // Cash and deposits amounts
            $table->decimal('total_cash_received', 15, 2)->default(0)->comment('Total cash received');
            $table->decimal('total_deposits', 15, 2)->default(0)->comment('Total deposits made');
            
            // Collections
            $table->decimal('total_collections', 15, 2)->default(0)->comment('Total collections from accounts receivable');
            
            // Additional useful fields
            $table->decimal('total_cash_expected', 15, 2)->default(0)->comment('Cash that the employee should have');
            $table->decimal('cash_difference', 15, 2)->default(0)->comment('Difference between expected and received cash');

            // Deposits
            $table->decimal('deposit_sales', 10, 2)->default(0);
            $table->decimal('total_deposit_expected', 10, 2)->default(0);
            $table->decimal('deposit_difference', 10, 2)->default(0);
            
            // Notes and status
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'completed', 'with_differences'])->default('pending');
            
            // Timestamps
            $table->timestamps();
            
            // Indices
            $table->index(['employee_id', 'reconciliation_date'], 'dsr_emp_date_idx');
            $table->index('reconciliation_date', 'dsr_date_idx');
            $table->index('status', 'dsr_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_sales_reconciliations');
    }
};