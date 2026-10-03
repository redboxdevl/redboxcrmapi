<?php

namespace App\Models\TaskManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAttachment extends Model
{
    use HasFactory;

    protected $table = "task_attachment";

    protected $fillable = [
        'task_id_pk',
        'emp_id',
        'comment_id',
        'attachement',
        'attachement_date_time',
    ];
}
