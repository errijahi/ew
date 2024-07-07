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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('amount');
            $table->foreignId('payee_id')->constrained();
            $table->string('notes');
            $table->date('date');
            $table->foreignId('team_id')->constrained();
            $table->string('transaction_source');
            $table->boolean('status'); // TODO: change boolean to enum.
            $table->foreignId('category_id')->nullable()->constrained();
            $table->foreignId('tag_id')->nullable()->constrained();
            $table->foreignId('account_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
