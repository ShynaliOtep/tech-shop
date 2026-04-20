<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Script for run
     *UPDATE goods
     * SET order_count = (
     * SELECT COUNT(order_items.id)
     * FROM items
     * LEFT JOIN order_items ON order_items.item_id = items.id
     * WHERE items.good_id = goods.id
     * );
     */
    public function up(): void
    {
        Schema::table('goods', function (Blueprint $table) {
            $table->integer('order_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods', function (Blueprint $table) {
            $table->dropColumn('order_count');
        });
    }
};
