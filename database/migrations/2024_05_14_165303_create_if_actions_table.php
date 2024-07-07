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
            $table->foreignId('rule_id')->constrained();
            $table->foreignId('payee_name_id')->nullable()->constrained();
            $table->foreignId('category_id')->nullable()->constrained();
            $table->foreignId('note_id')->nullable()->constrained();
            $table->foreignId('amount_id')->nullable()->constrained();
            $table->foreignId('day_id')->nullable()->constrained();
            $table->foreignId('account_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('if_actions');
    }
};
