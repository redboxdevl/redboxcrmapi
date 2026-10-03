<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leavebalance extends Model
{
    use HasFactory;

    protected $table = "emp_leave_balances";
    
    protected $fillable = [
        'emp_id',
        'leave_type_id',
        'leave_balance',
    ];
    
     public function getLeaveType(){
        return $this->belongsTo(Leavetype::class,'leave_type_id','id');
    }
}
