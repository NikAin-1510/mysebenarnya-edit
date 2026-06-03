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
        Schema::create('publicusers', function (Blueprint $table) {
            $table->string('PublicID', 8)->primary();
            $table->string('UserID', 8);
            $table->enum('Gender', ['male', 'female'])->nullable();

            $table->foreign('UserID')->references('UserID')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publicusers');
    }
};
