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
        Schema::create('inquiries', function (Blueprint $table) {
            $table->string('InquiryID', 8)->primary();
            $table->string('PublicID', 8);
            $table->string('InquiryTitle', 20);
            $table->string('InquiryDescription', 30);
            $table->timestamp('SubmissionDate')->useCurrent()->useCurrentOnUpdate();
            $table->string('SubmissionStatus', 10);
            $table->string('SubmissionLink', 20);
            $table->string('SubmissionEvidence', 20)->nullable();

            $table->foreign('PublicID')->references('PublicID')->on('publicusers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
