<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('logo_path')->nullable();
            $table->string('theme_color')->default('#001C4D');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('system_settings');
    }
}; 