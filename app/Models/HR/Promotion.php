<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $table = "emp_promotions";

    protected $fillable = [
        'emp_id',
        'old_dept_id',
        'new_dept_id',
        'old_designation_id',
        'new_designation_id',
        'old_salary',
        'new_salary',
        'description',
        'promotion_date',
    ];
}
