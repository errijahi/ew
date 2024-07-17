<?php

use App\Enums\AccountType;
use App\Enums\Status;
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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('balance');
            $table->enum('status', Status::values());
            $table->string('institution_name')->nullable();
            $table->foreignId('category_id')->nullable()->constrained();
            $table->foreignId('team_id')->constrained();
            $table->enum('type', AccountType::values())->nullable();
            $table->boolean('set_as_a_default_account')->default(false);
            $table->boolean('do_not_track_transactions')->default(false);
            $table->boolean('mark_as_closed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
