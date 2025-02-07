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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->unique();
            $table->string('grade')->nullable();
            $table->string('ax_iag_code')->nullable();
            $table->string('employee_name');
            $table->string('father_name')->nullable();
            $table->string('designation')->nullable();
            $table->string('hq')->nullable();
            $table->string('hq_state')->nullable();
            $table->string('territory')->nullable();
            $table->string('team')->nullable();
            $table->date('date_of_joining')->nullable();
            $table->date('date_of_leaving')->nullable();
            $table->date('last_working_date')->nullable();
            $table->string('category')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->enum('marital_status', ['Single', 'Married', 'Divorced'])->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('official_email_id')->nullable();
            $table->string('personal_email_id')->nullable();
            $table->string('cc_email_id')->nullable();
            $table->string('aadhar_number')->nullable();
            $table->boolean('pf_flag')->default(false);
            $table->string('uan_no')->nullable();
            $table->string('pf_no')->nullable();
            $table->boolean('esic_status')->default(false);
            $table->string('esic_no')->nullable();
            $table->string('compliance_branch')->nullable();
            $table->string('department')->nullable();
            $table->string('pan')->nullable();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
