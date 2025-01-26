<?php

declare(strict_types=1);

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
        Schema::create('split_transactions', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('tag_id')->nullable()->constrained();
            $table->foreignId('category_id')->nullable()->constrained();
            $table->foreignId('then_action_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('amount');
            $table->string('payee_id')->nullable();
            $table->string('notes')->nullable();
            $table->boolean('reviewed')->nullable();
            $table->boolean('run_through_rules')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('split_transactions');
    }
};
