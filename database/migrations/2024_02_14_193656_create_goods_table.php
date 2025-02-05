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
        Schema::create('goods', function (Blueprint $table) {
            $table->id();
            $table->string('name_ru');
            $table->string('name_en');
            $table->text('description_ru');
            $table->text('description_en');
            $table->integer('cost');
            $table->integer('additional_cost')->nullable(true)->default(0);
            $table->integer('discount_cost')->nullable();
            $table->integer('damage_cost');
            $table->json('related_goods')->nullable();
            $table->json('additionals')->nullable();

            $table->unsignedBigInteger('good_type_id')->nullable(false);
            $table->foreign('good_type_id')->references('id')->on('good_types')->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods');
    }
};
