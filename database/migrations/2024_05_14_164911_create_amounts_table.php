<?php

declare(strict_types=1);

use App\Enums\AccountType;
use App\Enums\NumberComparisonType;
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
        Schema::create('amounts', function (Blueprint $table) {
            $table->id();
            $table->float('amount');
            $table->enum('type', AccountType::values());
            $table->enum('filter', NumberComparisonType::values());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amounts');
    }
};
