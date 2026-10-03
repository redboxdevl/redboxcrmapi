<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $table = "trainings";

    protected $fillable = [
        'emp_id',
        'trainee_name',
        'department_id',
        'training_type_id',
        'training_title',
        'description',
        'from_date',
        'to_date',
        'start_time',
        'end_time',
        'status',
        'attachement',
    ];

    public function getEmployee(){
        return $this->belongsTo(Employee::class,'emp_id','id');
    }
    public function getDepartment(){
        return $this->belongsTo(Department::class,'department_id','id');
    }
    public function getTrainingType(){
        return $this->belongsTo(Trainingtype::class,'training_type_id','id');
    }
}
