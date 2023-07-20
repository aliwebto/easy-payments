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
            $table->id()->startingValue("1000000");

            $table->uuid("transaction_uuid")->unique("transaction_uuid"); // transaction id comes from gateway

            $table->morphs("model"); // relation to payable model

            $table->text('description')->nullable(); // description of payment
            $table->bigInteger('amount')->default(0); // payment amount
            $table->bigInteger('paidAmount')->default(0); // payment amount

            $table->string("specificCard")->nullable(); // pay with specific card
            $table->dateTime('paid_at')->nullable(); // payment datetime
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
