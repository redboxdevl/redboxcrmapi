<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListNotInterested extends Model
{
    use HasFactory;
    protected $table="notinterestedreason";
    
    public function getleads(){
        return $this->belongsTo(LeadImportDatabase::class,'id');
    }
    
    public function getreminder(){
        return $this->hasOne(AssignReminder::class,'leadid');
    }

    public function LeadImpression(){
        return $this->hasMany(LeadImpression::class,'leadid','id');
    }
    
    public function getASsignreminderData(){
        return $this->hasMany(AssignReminder::class,'leadid');
    }
    
    public function getleads_no(){
        return $this->belongsTo(LeadImportDatabase::class,'leadid','id');
    }
}
