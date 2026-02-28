<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('paddle_id')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price');
            $table->string("paddle_monthly_price_id")->nullable();
            $table->string("paddle_yearly_price_id")->nullable();
            $table->integer('yearly_offer_percentage')->default(0);
            $table->integer('rate_limit')->default(-1);    // Index for filtering
            $table->integer('agent_limit')->default(1);    // Index for filtering
            $table->integer('members_limit')->default(5);  // Index for filtering
            $table->integer('credit_limit')->default(100); // Index for filtering
            $table->integer('free_limit')->default(-1);
            $table->integer('extra_credit')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('plans');
    }
};
