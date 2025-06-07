<?php

use App\Models\Client;
use App\Models\ClientVisitDay;
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
        Client::all()->each(function ($client) {
            ClientVisitDay::create([
                'client_id' => $client->id,
                'visit_day' => $client->visit_day,
                'position' => $client->position,
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
