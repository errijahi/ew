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
        Schema::create('then_actions', function (Blueprint $table) {
            $table->id();
            $table->string('set_payee')->nullable();
            $table->string('set_notes')->nullable();
            $table->foreignId('category_id')->nullable()->constrained();
            $table->boolean('set_uncategorized')->nullable();
            $table->foreignId('tag_id')->nullable()->constrained();
            $table->boolean('delete_transaction')->nullable();
            $table->foreignId('recurring_item_id')->nullable()->constrained();
            $table->boolean('do_not_link_to_recurring_item')->nullable();
            $table->boolean('do_not_create_rule')->nullable();
            $table->boolean('mark_as_reviewed')->nullable();
            $table->boolean('mark_as_unreviewed')->nullable();
            $table->boolean('send_me_email')->nullable();
            $table->foreignId('rule_split_transaction_id')->constrained();
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
