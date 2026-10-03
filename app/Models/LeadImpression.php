<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;

class LeadImpression extends Model
{
    use HasFactory;
    protected $table="callingimpression";
    
    public function getvirginleads(){
        return $this->hasOne(ListAssignLeads::class,'leadid');
    }
    
    public function ReminderData(){
        return $this->hasOne(AssignReminder::class,'customerId');
    }

    public function HotReminderData(){
        return $this->hasOne(AssignReminder::class,'leadid');
    }

    public function ProjectData(){
        return $this->belongsTo(ProjectAdd::class,'projectid','id');
    }
    
    public function getleads(){
        return $this->belongsTo(LeadImportDatabase::class,'leadid','id');
    }
    
    public function getActivities(){
        return $this->belongsTo(CustomerActivity::class,'id','customerId');
    }
    
     public function getEmployeeDetails(){
        return $this->belongsTo(Employee::class,'agentid','id');
    }
    
      public function getActivitiesAll(){
        return $this->hasMany(CustomerActivity::class,'customerId');
    }
    
     public function getCustomerDetails(){
        return $this->belongsTo(ListMyLead::class,'leadid','leadid');
    }
    
    public function getAssignLeads(){
        return $this->belongsTo(ListAssignLeads::class,'leadid','leadid');
    }
    
    
}
