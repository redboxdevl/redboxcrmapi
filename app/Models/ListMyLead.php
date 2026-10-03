<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\leadStatus;
use App\Models\ProjectAdd;
use App\Models\Zongcalling;

class ListMyLead extends Model
{
    use HasFactory;

    protected $table="customer_leads_tbls";

    public function ReminderData(){
        return $this->hasOne(AssignReminder::class,'customerId')->latest();
    }
    
    public function getlastactivity(){
        return $this->hasOne(CustomerActivity::class,'customerId')->latest();
    }
    
    public function getleadstatus(){
        return $this->belongsTo(leadStatus::class,'clientInterst','id');
    }
    
    public function getcallingdata(){
        return $this->hasMany(Zongcalling::class);
    }
    
    
    public function getallactivity(){
        return $this->hasMany(CustomerActivity::class,'customerId','id');
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
    
    
    
    
    
    
}
