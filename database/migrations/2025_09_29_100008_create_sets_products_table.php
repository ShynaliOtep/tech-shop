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
        Schema::create('sets_goods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('set_id')->constrained('sets')->cascadeOnDelete();
            $table->foreignId('good_id')->constrained('goods')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sets_goods');
    }
};
