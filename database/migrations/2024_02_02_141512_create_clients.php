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
            $table->string('name')->nullable(false);
            $table->string('phone')->nullable(false);
            $table->integer('discount')->nullable(false)->default(0);
            $table->string('email')->nullable(false);
            $table->string('iin')->nullable(false);
            $table->string('instagram')->nullable(false);
            $table->string('confirmation_code')->nullable(false);
            $table->boolean('email_confirmed')->nullable(false)->default(false);
            $table->boolean('blocked')->nullable(false)->default(false);
            $table->string('password');

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
