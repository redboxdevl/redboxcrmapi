<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;


class Payroll extends Model
{
    use HasFactory;

    protected $table = "Payroll";

    protected $fillable = [
        'emp_id',
        'attendance_count',
        'gross_salary',
        'guel_charges',
        'mobile_charges',
        'advance_amt',
        'tax_amt',
        'net_salary',
        'payrollDate',
    ];
    
     public function getEmployee(){
        return $this->belongsTo(Employee::class,'emp_id','id');
    }
  
}