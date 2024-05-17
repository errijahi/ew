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
        Schema::create('then', function (Blueprint $table) {
            $table->id();
            $table->string('set_payee');
            $table->string('set_notes');
            $table->foreignId('set_category')->nullable();
            $table->boolean('set_uncategorized');
            $table->foreignId('add_tag')->nullable();
            $table->boolean('delete_transaction');
            $table->foreignId('link_to_recurring_item')->nullable();
            $table->boolean('do_not_link_to_recurring_item');
            $table->boolean('do_not_create_rule');
            $table->foreignId('split_transaction')->nullable();
            $table->boolean('mark_as_reviewed');
            $table->boolean('mark_as_unreviewed');
            $table->boolean('send_me_email');
            $table->foreignId('rule_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('then');
    }
};
