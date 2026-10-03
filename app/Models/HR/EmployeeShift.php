<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeShift extends Model
{
    use HasFactory;

    protected $table = "employee_shift";

    protected $fillable = [
        'shift_name',
        'shift_in',
        'shift_out',
    ];
}