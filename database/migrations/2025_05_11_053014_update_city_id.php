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
        \Illuminate\Support\Facades\DB::update('UPDATE items SET city_id = 1');
        \Illuminate\Support\Facades\DB::update('UPDATE orders SET city_id = 1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
