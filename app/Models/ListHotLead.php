<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\HR\Employee;

class ListHotLead extends Model
{
    use HasFactory;
    protected $table="customer_leads_tbls";
    
    public function ReminderData(){
        return $this->hasOne(AssignReminder::class,'customerId');
    }

    public function HotReminderData(){
        return $this->hasOne(AssignReminder::class,'leadid');
    }

    public function ProjectData(){
        return $this->belongsTo(ProjectAdd::class,'projectid','id');
    }
    
    public function getActivities(){
        return $this->belongsTo(CustomerActivity::class,'id','customerId');
    }
    public function getEmployeeDetails(){
        return $this->belongsTo(Employee::class,'agentid','id');
    }
}
