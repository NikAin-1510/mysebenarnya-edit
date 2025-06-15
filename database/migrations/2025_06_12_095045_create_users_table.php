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
        Schema::create('user', function (Blueprint $table) {
            $table->string('UserID', 8)->primary();
            $table->string('Email', 30)->unique();
            $table->string('Password', 20);
            $table->string('Name', 30);
            $table->string('PhoneNum', 20)->nullable();
            $table->binary('ProfilePic')->nullable();
            $table->enum('Role', ['publicuser', 'mcmc', 'agency']);
            $table->timestamp('Created_At')->useCurrent();
            $table->timestamp('Login_At')->nullable();
            $table->timestamp('Updated_At')->nullable();
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
