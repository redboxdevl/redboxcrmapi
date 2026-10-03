<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leaveapplication extends Model
{
    use HasFactory;

    protected $table = "emp_leave_applications";

    protected $fillable = [
        'leave_type_id',
        'emp_id',
        'from_date',
        'to_date',
        'description',
        'no_of_days',
        'status',
    ];
    
    
    
    public function getleavetype(){
        return $this->belongsTo(Leavetype::class,'leave_type_id','id');
    }
}
