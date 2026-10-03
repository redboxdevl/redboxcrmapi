<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zongcalling extends Model
{
    use HasFactory;
    protected $table="zong_calling_data";
    
     public function getCallingDetails(){
        return $this->belongsTo(LeadImportDatabase::class,'phone','clientPhone');
    }
    
    public function getOutgoingCalls(){
        return $this->belongsTo(Zongcalling::class,'phone');
    }
    
    
}
