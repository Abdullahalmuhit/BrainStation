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
        Schema::create('student_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('bank_ref')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('payment_to')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('transaction_date')->nullable();
            $table->string('amount')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('payment_status_code')->nullable();
            $table->string('pay_mode')->nullable();
            $table->string('session_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_payments');
    }
};
