<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Educationqualify extends Model
{
    use HasFactory;

    protected $table = "Educationqualify";
    
    protected $fillable = [
        'empId',
        'schoolUniv',
        'schoolQualify',
        'schoolLevel',
        'SchoolPassingYear',
    ];
   
}
