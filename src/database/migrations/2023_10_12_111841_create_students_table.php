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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->nullable();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('contact_number')->unique();
            $table->string('dob')->nullable();
            $table->string('password');
            $table->string('bank_ref')->nullable();
            $table->string('transaction_id')->nullable();
            $table->dateTime('transaction_date')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('payment_to')->nullable();
            $table->string('payment_type')->nullable();
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
        Schema::dropIfExists('students');
    }
};
