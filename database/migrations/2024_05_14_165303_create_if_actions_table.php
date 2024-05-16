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
        Schema::create('if_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id');
            $table->foreignId('matches_payee_name')->nullable();
            $table->foreignId('matches_category')->nullable();
            $table->foreignId('matches_notes')->nullable();
            $table->foreignId('matches_amount')->nullable();
            $table->foreignId('matches_day')->nullable();
            $table->foreignId('in_account')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('if');
    }
};
