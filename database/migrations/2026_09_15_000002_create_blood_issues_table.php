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
        Schema::create('blood_issues', function (Blueprint $table) {
            $table->id();
            $table->string('requisition_no')->unique();
            $table->string('patient_name');
            $table->string('patient_hrn'); // Hospital Record Number
            $table->string('ward_room');
            $table->string('blood_type'); // A+, A-, B+, B-, AB+, AB-, O+, O-
            $table->string('blood_component'); // Packed RBC (PRBC), Whole Blood, Fresh Frozen Plasma (FFP), Platelet Concentrate, Cryoprecipitate
            $table->unsignedInteger('units_issued')->default(1);
            $table->string('attending_physician');
            $table->string('crossmatch_status')->default('Compatible'); // Compatible, Pending Crossmatch, Emergency Uncrossmatched, Incompatible
            $table->string('urgency_level')->default('Routine'); // Routine, Urgent, Stat/Emergency
            $table->string('status')->default('Issued'); // Issued, Transfusion Ongoing, Completed, Returned/Discarded
            $table->string('issued_by')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_issues');
    }
};
