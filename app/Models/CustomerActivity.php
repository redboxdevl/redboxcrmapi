<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;

class CustomerActivity extends Model
{
    use HasFactory;

    protected $table="customer_activities";
    
    public function CustomerData(){
        return $this->belongsTo(ListMyLead::class,'customerId','id');
    }
    public function customer_activity_type(){
        return $this->belongsTo(ActivityTypes::class,'typeId','id');
    }
    public function customer_activity_outcome(){
        return $this->belongsTo(ActivityOutcome::class,'outcomeId','id');
    }
     public function getEmployeeDetails(){
        return $this->belongsTo(Employee::class,'agentid','id');
    }
    
    
   
}
