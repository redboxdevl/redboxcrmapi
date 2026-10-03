<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class empTeam extends Model
{
    use HasFactory;

    protected $table = "empTeam";

    protected $fillable = [
        'agentId',
        'teamObj',
        'teamName',
        'teamLeadId',
    ];


// getEmployeesDetails

public function getEmployeesDetails(){
        return $this->belongsTo(Employee::class,'agentId','id');
    }
}
