<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\LeadImpression;
use App\Models\ListMyLead;

class ListAssignLeads extends Model
{
    use HasFactory;
    protected $table ="leadsassigndatabase";

    public function getleads(){
        return $this->belongsTo(LeadImportDatabase::class,'leadid','id');
    }
    
    public function getMyLeadsData(){
        return $this->belongsTo(ListMyLead::class,'leadid','leadid');
    }
    
    public function getreminder(){
        return $this->belongsTo(AssignReminder::class,'leadid','leadid');
    }

    public function LeadImpression(){
        return $this->belongsTo(LeadImpression::class,'leadid','leadid');
    }
    
    public function getASsignreminderData(){
        return $this->hasMany(AssignReminder::class,'leadid');
    }
    
    public function getvirginleads(){
        return $this->belongsTo(LeadImpression::class,'leadid','leadid');
    }
    
    public function get_transferleads(){
         return $this->belongsTo(TranasferShareLeads::class,'leadid','leadid');
    }
    public function getEmployee(){
        return $this->belongsTo(Employee::class,'agentid','id');
    }
    
    public function getImpressionLeads(){
        return $this->belongsTo(LeadImpression::class,'leadid','leadid');
    }
    
    
    
}
