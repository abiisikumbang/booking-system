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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('unit_id')->constrained('units');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->integer('quantity')->default(1);
            $table->enum('status', ['PENDING','CONFIRMED','CANCELLED','EXPIRED','COMPLETED']);
            $table->decimal('total_amount', 12, 2);
            $table->enum('payment_status', ['UNPAID','PAID','REFUNDED'])->default('UNPAID');
            $table->text('notes')->nullable();
            $table->dateTime('checkin_at')->nullable();
            $table->dateTime('checkout_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
