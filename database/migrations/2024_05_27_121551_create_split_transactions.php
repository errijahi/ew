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
        Schema::create('split_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('amount');
            $table->string('payee')->nullable();
            $table->string('notes')->nullable();
            $table->foreignId('team_id');
            $table->foreignId('category_id')->nullable();
            $table->foreignId('tag_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('split_transaction');
    }
};
