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
        Schema::create('then_actions', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id');
            $table->foreignId('tag_id')->nullable()->constrained();
            $table->foreignId('payee_id')->nullable()->constrained();
            $table->foreignId('category_id')->nullable()->constrained();
            $table->foreignId('recurring_item_id')->nullable()->constrained();
            $table->string('set_notes')->nullable();
            $table->string('set_uncategorized')->nullable();
            $table->string('delete_transaction')->nullable();
            $table->string('do_not_link_to_recurring_item')->nullable();
            $table->string('do_not_create_rule')->nullable();
            $table->string('reviewed')->nullable();
            $table->string('send_me_email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('then_actions');
    }
};
