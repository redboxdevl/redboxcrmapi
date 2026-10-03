<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;

class AssignReminder extends Model
{
    use HasFactory;
    
    protected $table ="assign_reminders";

    public function getAssignreminder(){
        return $this->belongsTo(ListAssignLeads::class,'id');
    }

    public function CustomerLeadsTbl(){
        return $this->belongsTo(ListMyLead::class,'leadid');
    }
    
    public function getleads_home(){
        return $this->belongsTo(LeadImportDatabase::class,'leadid','id');
    }
    
    public function getleads(){
        return $this->belongsTo(LeadImportDatabase::class,'leadid','id');
    }
     public function getEmployeeDetails(){
        return $this->belongsTo(Employee::class,'agentid','id');
    }
    
    public function getAssignLeads(){
        return $this->belongsTo(ListAssignLeads::class,'leadid','leadid');
    }
    public function getCallingImpress(){
        return $this->belongsTo(LeadImpression::class,'leadid','leadid');
    }
    
    
    
}
