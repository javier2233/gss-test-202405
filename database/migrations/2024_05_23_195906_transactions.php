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
            $table->foreignId('from')
                ->constrained(
                    table: 'accounts', indexName: 'from_account_id')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->integer('authorization')->value(0);
            $table->integer('status')->value(0);
            $table->integer('value')->value(0);
            $table->enum('type', ['recharge', 'transfer']);
            $table->foreignId('to')
                ->constrained(
                    table: 'accounts', indexName: 'to_account_id')
                ->onUpdate('cascade')
                ->onDelete('cascade');
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
