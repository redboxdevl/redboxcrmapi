<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = "emp_attendances";

    protected $fillable = [
        'emp_id',
        'machine_id',
        'location_lat',
        'location_long',
        'checkout_location_lat',
        'checkout_location_long',
        'is_mobile',
        'attendance_date',
        'checkin',
        'checkout',
        'checkintype',
        'checkouttype',
    ];
    
    public function getEmployee(){
        return $this->belongsTo(Employee::class,'emp_id','id');
    }
}
