<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'employee_code', 'grade', 'ax_iag_code', 'employee_name', 'father_name',
        'designation', 'hq', 'hq_state', 'territory', 'team', 'date_of_joining',
        'date_of_leaving', 'last_working_date', 'category', 'date_of_birth', 
        'gender', 'marital_status', 'mobile_no', 'official_email_id', 'personal_email_id',
        'cc_email_id', 'aadhar_number', 'pf_flag', 'uan_no', 'pf_no', 'esic_status', 
        'esic_no', 'compliance_branch', 'department', 'pan'
    ];
}
