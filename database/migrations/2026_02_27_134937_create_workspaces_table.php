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
        Schema::create('workspaces', function (Blueprint $table) {
            $table->id();
            $table->string('name');                                                   // Index for search
            $table->string('slug')->unique();                                         // Unique and indexed for lookups
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade'); // Index for owner lookups
            $table->boolean('status')->default(true);                                 // Index for filtering
            $table->integer('rate_limit')->default(-1);
            $table->integer('agent_limit')->default(1);
            $table->integer('members_limit')->default(5);
            $table->integer('credit_limit')->default(100);
            $table->integer('free_limit')->default(-1);
            $table->timestamp('subscribe_at');
            $table->timestamp('next_bill_date');
            $table->timestamp('next_credit_reset');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workspaces');
    }
};
