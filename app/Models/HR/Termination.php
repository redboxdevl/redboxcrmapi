<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Termination extends Model
{
    use HasFactory;

    protected $table = "emp_terminations";

    protected $fillable = [
        'emp_id',
        'subject',
        'type',
        'description',
        'notice_date',
        'termination_date',
        'termination_by'
    ];

    public function getEmployee(){
        return $this->belongsTo(Employee::class,'emp_id','id');
    }
    public function getTerminationBy(){
        return $this->belongsTo(Employee::class,'termination_by','id');
    }
}
