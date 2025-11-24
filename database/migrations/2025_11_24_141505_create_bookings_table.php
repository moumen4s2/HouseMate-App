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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->constrained('apartments');
            $table->foreignId('tenant_id')->constrained('users');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['pending','accepted','rejected','cancelled'])->default('pending');
            $table->decimal('total_price', 10,2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
