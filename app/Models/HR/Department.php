<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = "departments";

    protected $fillable = [
        'department_name',
        'branch_id',
    ];

    public function getBranch(){
        return $this->belongsTo(Branch::class,'branch_id','id');
    }
}
