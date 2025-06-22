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
        Schema::create('inquiryprogresses', function (Blueprint $table) {
            $table->string('StatusID', 8)->primary();
            $table->string('InquiryID', 8);
            $table->string('AgencyID', 8);
            $table->string('AssignmentID', 8);
            $table->timestamp('InvestigationBeginDate')->nullable();
            $table->string('VerificationStatus', 10);
            $table->timestamp('VerificationDateTime')->nullable();
            $table->string('InvestigationDetails', 30);

            // ✅ New columns
            $table->binary('InvestigationDoc')->nullable();
            $table->enum('Notify', [
                'Further clarification needed',
                'Inquiry is completed',
                'Reassignment requested',
                ''
            ])->nullable();

            $table->foreign('InquiryID')->references('InquiryID')->on('inquiries');
            $table->foreign('AgencyID')->references('AgencyID')->on('agencies');
            $table->foreign('AssignmentID')->references('AssignmentID')->on('inquiryassignments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiryprogresses');
    }
};
