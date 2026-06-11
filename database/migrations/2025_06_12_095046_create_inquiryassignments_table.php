<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inquiryassignments', function (Blueprint $table) {
            $table->string('AssignmentID', 8)->primary();
            $table->string('AgencyID', 8);
            $table->string('mcmcID', 8);
            $table->string('InquiryID', 8);
            $table->date('AssignDate')->nullable();
            $table->boolean('JurisdictionStatus')->default(0);
            $table->string('InquiryComment', 100)->nullable();
            $table->string('JurisdictionComment', 100)->nullable();

            // Foreign keys (assuming FK exists in database)
            $table->foreign('AgencyID')->references('AgencyID')->on('agency');
            $table->foreign('mcmcID')->references('mcmcID')->on('mcmc');
            $table->foreign('InquiryID')->references('InquiryID')->on('inquiry');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiryassignments');
    }
};
