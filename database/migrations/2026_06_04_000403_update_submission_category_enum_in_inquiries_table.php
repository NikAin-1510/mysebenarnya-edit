<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: expand ENUM to allow both old and new values
        DB::statement("ALTER TABLE `inquiry` MODIFY `SubmissionCategory` ENUM('Genuine', 'Serious', 'Non-Serious') NULL");

        // Step 2: rename existing 'Genuine' records to 'Serious'
        DB::table('inquiry')->where('SubmissionCategory', 'Genuine')->update(['SubmissionCategory' => 'Serious']);

        // Step 3: remove 'Genuine' now that no rows use it
        DB::statement("ALTER TABLE `inquiry` MODIFY `SubmissionCategory` ENUM('Serious', 'Non-Serious') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `inquiry` MODIFY `SubmissionCategory` ENUM('Genuine', 'Serious', 'Non-Serious') NULL");

        DB::table('inquiry')->where('SubmissionCategory', 'Serious')->update(['SubmissionCategory' => 'Genuine']);

        DB::statement("ALTER TABLE `inquiry` MODIFY `SubmissionCategory` ENUM('Genuine', 'Non-Serious') NULL");
    }
};
