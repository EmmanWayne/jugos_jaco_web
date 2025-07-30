<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            $table->integer('invoice_number')->nullable();
            $table->foreignId('invoice_series_id')->nullable()->constrained('invoices_series');

            $table->foreignId('client_id')->nullable()->constrained('clients');
            $table->foreignId('employee_id')->constrained('employees');

            $table->date('sale_date');
            $table->date('due_date')->nullable();

            $table->enum('status', ['draft', 'confirmed', 'partially_paid', 'paid', 'cancelled'])
                ->default('draft');
            $table->enum('payment_type', ['cash', 'credit', 'deposit', 'card'])->default('cash');
            $table->decimal('cash_amount', 15, 4)->default(0);

            $table->decimal('subtotal', 15, 4)->default(0);

            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->decimal('discount_amount', 15, 4)->default(0.0000);
            $table->string('discount_reason', 100)->nullable();

            $table->decimal('total_amount', 15, 4)->default(0)
                ->comment('Subtotal + total_tax_amount - discount_amount');

            $table->text('notes')->nullable();

            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');

            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            $table->index(['sale_date', 'status']);
            $table->index(['client_id', 'sale_date']);
            $table->index(['employee_id', 'sale_date']);
            $table->index('status');
            $table->unique(['invoice_series_id', 'invoice_number'], 'unique_invoice_per_series');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
