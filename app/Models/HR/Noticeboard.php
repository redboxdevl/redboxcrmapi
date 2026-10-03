<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Noticeboard extends Model
{
    use HasFactory;

    protected $table = "notice_boards";

    protected $fillable = [
        'emp_id',
        'notice_title',
        'description',
        'status',
        'attachement',
        'published_date',
    ];

    public function getEmployee(){
        return $this->belongsTo(Employee::class,'emp_id','id');
    }
}
