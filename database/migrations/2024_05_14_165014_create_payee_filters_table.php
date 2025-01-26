<?php

declare(strict_types=1);

use App\Enums\TextMatchType;
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
        Schema::create('payee_filters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payee_id');
            $table->enum('filter', TextMatchType::values());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payee_filters');
    }
};
