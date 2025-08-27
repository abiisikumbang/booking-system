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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties');
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('capacity');
            $table->decimal('price_amount', 12, 2);
            $table->string('price_currency', 3)->default('IDR');
            $table->enum('price_unit', ['NIGHT','HOUR','MONTH']);
            $table->json('amenities')->nullable();
            $table->enum('status', ['ACTIVE','INACTIVE','MAINTENANCE']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
