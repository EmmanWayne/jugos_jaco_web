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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->string('address', 120);
            $table->string('phone_number', 15);
            $table->string('department', 50);
            $table->string('township', 50);
            $table->foreignId('type_price_id')->nullable()->constrained('types_prices')->restrictOnDelete();
            $table->string('business_name', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};