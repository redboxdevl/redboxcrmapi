<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;

class TranasferShareLeads extends Model
{
    use HasFactory;
    protected $table="tranasfer_share_leads";
    
    
    public function LeadData(){
        return $this->belongsTo(LeadImportDatabase::class,'leadid','id');
    }
    
    public function toAgent(){
        return $this->belongsTo(Employee::class,'tagent','id');
    }
}
