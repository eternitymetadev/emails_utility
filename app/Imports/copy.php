<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\BankDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use App\Services\EmailService; 
use \PhpOffice\PhpSpreadsheet\Shared\Date;

class EmployeesImport implements ToModel, WithHeadingRow
{

    public function model(array $row)
    {
     
        // Convert date fields to 'Y-m-d' format if not empty
        $dateOfJoining = !empty($row['date_of_joining']) ? Carbon::createFromFormat('d-M-Y', $row['date_of_joining'])->format('Y-m-d') : null;
        
        $dateOfLeavingRaw = $row['date_of_leaving'];
        $dateOfLeaving = !empty($row['date_of_leaving']) ? Carbon::createFromFormat('d-M-Y', $row['date_of_leaving'])->format('Y-m-d') : null;

        // Checking if the date is numeric (Excel serialized format) or a string (formatted as 'd-M-Y')
        if (is_numeric($row['last_working_date'])) {
            $lastWorkingDate = Carbon::instance(Date::excelToDateTimeObject($row['last_working_date']))->format('Y-m-d');
        } else {
            $lastWorkingDate = !empty($row['last_working_date']) ? Carbon::createFromFormat('d-M-Y', $row['last_working_date'])->format('Y-m-d') : null;
        }
        //Log::info('Last working date: ' . $lastWorkingDate);
        $dateOfBirth = !empty($row['date_of_birth']) ? Carbon::createFromFormat('d-M-Y', $row['date_of_birth'])->format('Y-m-d') : null;
    
        $pfFlag = ($row['pf_flag'] === 'Yes') ? 1 : 0;
        $esicStatus = ($row['esic_status'] === 'Yes') ? 1 : 0;
    
        // Employee data
        $employeeData = [
            'employee_code' => $row['employee_code'],
            'grade' => $row['grade'],
            'ax_iag_code' => $row['ax_iag_code'],
            'employee_name' => $row['employee_name'],
            'father_name' => $row['father_name'],
            'designation' => $row['designation'],
            'hq' => $row['hq'],
            'hq_state' => $row['hq_state'],
            'territory' => $row['territory'],
            'team' => $row['team'],
            'date_of_joining' => $dateOfJoining,
            'date_of_leaving' => $dateOfLeaving,
            'last_working_date' => $lastWorkingDate,
            'category' => $row['category'],
            'date_of_birth' => $dateOfBirth,
            'gender' => $row['gender'],
            'marital_status' => $row['marital_status'],
            'mobile_no' => $row['mobile_no'],
            'official_email_id' => $row['official_email_id'],
            'personal_email_id' => $row['personal_email_id'],
            'cc_email_id' => $row['cc_email_id'],
            'aadhar_number' => $row['aadhar_number'],
            'pf_flag' => $pfFlag,
            'uan_no' => $row['uan_no'],
            'pf_no' => $row['pf_no'],
            'esic_status' => $esicStatus,
            'esic_no' => $row['esic_no'],
            'compliance_branch' => $row['compliance_branch'],
            'department' => $row['department'],
            'pan' => $row['pan']
        ];
    
        $empdata = !Employee::exists(); 
        if($empdata){
            $employee = Employee::create($employeeData);
            Log::info('First Time import');
        }
        else{
            
        // Check if employee exists in the database
        $employee = Employee::where('employee_code', $employeeData['employee_code'])->first();
       
        if ($employee) {
            Log::info('Second Time import update');
            // // Check if lastWorkingDate exists and is more than 30 days in the past
            if ($lastWorkingDate) {
                $daysSinceLastWorkingDate = Carbon::parse($lastWorkingDate)->diffInDays(Carbon::now());
                
                if ($daysSinceLastWorkingDate > 30) {
                    // If it's been more than 30 days, delete the email

                    $authData = EmailService::authenticate();
                    if ($authData) {
                        EmailService::deleteEmail($employee->official_email_id, $authData);
                    }
                } else {
                    // If it's been 30 days or less, block the email
                    $authData = EmailService::authenticate();
                    if ($authData) {
                        EmailService::blockEmail($employee->official_email_id,$employeeData['employee_code'],$authData);
                    }
                }
            }
        
            // // Check if the official email has changed
            if ($employee->official_email_id !== $employeeData['official_email_id']) {
                $authData = EmailService::authenticate();
                if ($authData) {
                    // Block the old email and create a new one
                    EmailService::blockEmail($employee->official_email_id,$employeeData['employee_code'],$authData);
                   
                    $newEmail = EmailService::createEmail($employeeData, $authData);
                    $employeeData['official_email_id'] = $newEmail;
                }
            }
        
            // // Update the employee record
            $employee->update($employeeData);
        } else {
            Log::info('Second time new data');
            $employee = Employee::create($employeeData);
            // Employee doesn't exist, create new employee email and record
            $authData = EmailService::authenticate();
            if ($authData) {
                $newEmail = EmailService::createEmail($employeeData, $authData);
                if ($newEmail == 1) {
                    $employee = Employee::create($employeeData);
                }
            }
         }
       }
    
        // Bank data (associate with the employee)
        $bankData = [
            'employee_id' => $employee->id,
            'beneficiary_name' => $row['beneficiary_name'],
            'bank_name' => $row['bank_name'],
            'account_number' => $row['account_number'],
            'ifsc_code' => $row['ifsc_code'],
            'branch_name' => $row['branch_name'],
            'account_base_type' => $row['account_base_type'],
            'transfer_type' => $row['transfer_type']
        ];
    
        // Check if the bank details exist for the employee
        $bank = BankDetail::where('employee_id', $employee->id)->first();
    
        if ($bank) {
            // If bank details exist, update them
            $bank->update($bankData);
        } else {
            // Otherwise, create new bank details
            BankDetail::create($bankData);
        }
    }
    
    
}
