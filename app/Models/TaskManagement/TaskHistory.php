<?php

namespace App\Models\TaskManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskHistory extends Model
{
    use HasFactory;
    
     protected $table = "task_history";

    protected $fillable = [
        'task_id_pk',
        'status',
        'status_changed_by',
        'status_date_time',
        'assigned_by',
        'assigned_to',
        'assigned_date_time',
        'priority',
        'priority_changed_by',
        'priority_date_time',
        'due_date',
    ];
}
