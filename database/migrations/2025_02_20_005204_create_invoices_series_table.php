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
        Schema::create('invoices_series', function (Blueprint $table) {
            $table->id();
            $table->string('cai', 100)->unique();
            $table->integer('initial_range');
            $table->integer('end_range');
            $table->date('expiration_date');
            $table->enum('status', ['Activada', 'Expirada', 'Completada']);
            $table->string('mask_format', 20);
            $table->string('prefix', 20);
            $table->integer('current_number');
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices_series');
    }
};
