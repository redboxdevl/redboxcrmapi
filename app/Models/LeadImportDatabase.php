<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadImportDatabase extends Model
{
    use HasFactory;

    protected $table="leadsimportdatabase";
    
    // protected $fillable = [
    //     'clientName',
    //     'clientPhone',
    //     'clientemail',
    //     'clientProject',
    //     'clientDesignation',
    //     'nature',
    //     'leadStatus',
    //     'leadType',
    // ];

    public function getleads(){
        return $this->hasOne(ListAssignLeads::class,'leadid');
    }
    
    
}
