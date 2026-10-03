<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warning extends Model
{
    use HasFactory;

    protected $table = "emp_warnings";

    protected $fillable = [
        'emp_id',
        'subject',
        'type',
        'description',
        'warning_date',
        'warning_by'
    ];

    public function getEmployee(){
        return $this->belongsTo(Employee::class,'emp_id','id');
    }
    public function getWarningBy(){
        return $this->belongsTo(Employee::class,'warning_by','id');
    }

}
