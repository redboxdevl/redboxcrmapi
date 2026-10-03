<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workexperience extends Model
{
    use HasFactory;

    protected $table = "Workexperience";
    
    protected $fillable = [
        'empId',
        'WorkCompany',
        'Workaddress',
        'Workdesignation',
        'Worksalary',
        'tenureFrom',
        'tenureTo'
    ];
   
}
