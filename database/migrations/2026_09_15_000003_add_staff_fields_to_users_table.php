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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('Medical Technologist')->after('email');
            $table->string('employee_id')->nullable()->unique()->after('role');
            $table->string('department')->default('Blood Bank & Transfusion Medicine')->after('employee_id');
            $table->string('phone_number')->nullable()->after('department');
            $table->string('status')->default('Active')->after('phone_number'); // Active, Inactive, On Leave
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'employee_id', 'department', 'phone_number', 'status']);
        });
    }
};
