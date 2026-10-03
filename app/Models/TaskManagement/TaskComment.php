<?php

namespace App\Models\TaskManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    use HasFactory;

    protected $table = "task_comment";

    protected $fillable = [
        'task_id_pk',
        'emp_id',
        'comment',
        'comment_date_time',
    ];
}
