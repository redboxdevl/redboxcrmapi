<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocumentType;
use App\Models\propertyType;
use App\Models\HR\Company;
use App\Models\HR\Department;
use App\Models\HR\Itemtype;
use App\Models\HR\Employee;


class Purchasereqform extends Model
{
    use HasFactory;

    protected $table = "purchaseReqForm";
    protected $fillable = [
        'company_id',
        'reqtypeId',
        'agentId',
        'dateofReq',
        'departmentId',
        'Purpose',
        'EstimatedBudget',
        'RequiredDate',
        'CurrentSalary',
        'RequestedAmount',
        'TransferFrom',
        'TransferTo',
        'status',
        'DisbursedAmt',
        'PriorityId',
        'AttachementImage',
    ];
    
    public function getitemsDetails(){
        return $this->hasMany(Itemdetails::class,'purchaseReqId','id');
    }
    
    public function getRepaymentSchedule(){
        return $this->hasMany(Repaymentschedule::class,'purchaseReqId','id');
    }
    public function getRequestType(){
        return $this->belongsTo(Itemtype::class,'reqtypeId','id');
    }
    
    public function getDepartments(){
        return $this->belongsTo(Department::class,'departmentId','id');
    }
    
     public function getEmployees(){
        return $this->belongsTo(Employee::class,'agentId','id');
    }
    
    
    
    
}