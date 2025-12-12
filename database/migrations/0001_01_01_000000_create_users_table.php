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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->enum('role', ['tenant', 'owner', 'admin'])->default('tenant');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('mode', ['light', 'dark'])->default('light');
            $table->enum('dir', ['ltr', 'rtl'])->default('ltr');
            $table->string('avatar_url')->default('profiles/default-profile.jpg');
            $table->string('id_document_url');
            $table->timestamp('date_of_birth');
            $table->boolean('is_approved')->default(false);
            $table->string('otp')->nullable();
            $table->timestamp('expire_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
