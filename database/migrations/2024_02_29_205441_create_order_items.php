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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable(false);
            $table->unsignedBigInteger('item_id')->nullable(false);

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('item_id')
                ->references('id')
                ->on('items')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->json('additionals')->nullable();

            $table->boolean('is_additional')->nullable(false)->default(false);

            $table->enum('status', ['returned', 'in_rent', 'waiting', 'confirmed', 'cancelled']);

            $table->integer('amount_of_days')
                ->nullable(false);
            $table->integer('amount_paid')
                ->nullable(false);
            $table->date('rent_start_date')
                ->nullable(false);
            $table->time('rent_start_time')
                ->nullable(false);

            $table->date('rent_end_date')
                ->nullable(false);
            $table->time('rent_end_time')
                ->nullable(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
