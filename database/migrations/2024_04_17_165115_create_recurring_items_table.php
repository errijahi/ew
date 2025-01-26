<?php

declare(strict_types=1);

use App\Enums\Cadence;
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
        Schema::create('recurring_items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('billing_date');
            $table->enum('repeating_cadence', Cadence::values());
            $table->string('description')->nullable();
            $table->string('start_date');
            $table->string('end_date');
            $table->foreignId('team_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_items');
    }
};
