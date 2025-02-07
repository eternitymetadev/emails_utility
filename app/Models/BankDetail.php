<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankDetail extends Model
{
    protected $fillable = [
        'employee_id', 'beneficiary_name', 'bank_name', 'account_number',
        'ifsc_code', 'branch_name', 'account_base_type', 'transfer_type'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}

