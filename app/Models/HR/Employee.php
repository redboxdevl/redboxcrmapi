<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Zongcalling;
class Employee extends Model
{
    use HasFactory;

    protected $table = "employees";
    
    protected $fillable = [
        'emp_code',
        'emp_title',
        'email',
        'password',
        'dob',
        'cnic',
        'emp_name',
        'fatherName',
        'joining_date',
        'phone_number',
        'mobile_number',
        'company_id',
        'department_id',
        'designation_id',
        'branch_id',
        'profile_pic',
        'utilityBill',
        'CnicFront',
        'CnicBack',
        'resumeUpload',
        'status',
        'resetCode',
        'device_id',
        'NotificationToken',
        'holidy_id',
        'health_details',
        'personal_email',
        'ReportTo',
        'employment_type',
        'date_of_confirmation',
        'probation_period',
        'date_of_probation_period',
        'current_address',
        'permanent_address',
        'current_address_type',
        'permanent_address_type',
        'emergency_contact_no',
        'emergency_phone_no',
        'relationship',
        'ref_contact_name',
        'ref_phone_no',
        'default_shift',
        'salary_amount',
        'currency',
        'salary_mode',
        'bank_name',
        'bank_account',
        'blood_group',
        'family_background',
        'fuel',
        'mobilecharge',
        'incommingPhone',
        'outgoingPhone',
        'lockStatus',
        'Hod'
      
    ];
   
    public function getCompany(){
        return $this->belongsTo(Company::class,'company_id','id');
    }
    public function getDesignation(){
        return $this->belongsTo(Designation::class,'designation_id','id');
    }
    public function getDepartment(){
        return $this->belongsTo(Department::class,'department_id','id');
    }
    public function getBranch(){
        return $this->belongsTo(Branch::class,'branch_id','id');
    }
    
    public function getRole(){
        return $this->belongsTo('App\Models\RolesPermission\EmployeeRole'::class,'id','emp_id');
    }
    
    public function getOutgoingCalls(){
        return $this->belongsTo(Zongcalling::class,'ext','outgoingPhone');
    }
    
    
}
