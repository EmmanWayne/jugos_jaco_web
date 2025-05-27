<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('reference_number', 64)->unique();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
