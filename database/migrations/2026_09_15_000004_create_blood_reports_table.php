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
        Schema::create('blood_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_code')->unique();
            $table->string('scope'); // Inside Caraga, Outside Caraga
            $table->string('facility_name'); // e.g. Surigao City Health Office, Southern Philippines Medical Center
            $table->string('province_region'); // e.g. Surigao del Norte, Region X - Northern Mindanao
            $table->string('report_type'); // Collection Turnout, Inter-Regional Transfer, Consumption Audit, Safety Screening
            $table->string('reporting_period'); // e.g. September 2026, Q3 2026
            $table->unsignedInteger('blood_units_count')->default(0);
            $table->text('blood_breakdown')->nullable(); // JSON or text breakdown: O+, A+, B+, etc.
            $table->string('submitting_officer');
            $table->string('status')->default('Received'); // Verified, Received, Pending Review, Archived
            $table->text('summary_notes')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_reports');
    }
};
