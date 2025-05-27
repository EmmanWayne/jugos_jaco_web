<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('management_inventory', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->decimal('quantity', 10, 2);
            $table->enum('type', ['entrada', 'salida', 'dañado', 'devolución']);
            $table->morphs('model');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('created_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('management_inventory');
    }
};
