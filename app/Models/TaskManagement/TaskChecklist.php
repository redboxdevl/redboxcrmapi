<?php

namespace App\Models\TaskManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskChecklist extends Model
{
    use HasFactory;

    protected $table = "task_checklist";

    protected $fillable = [
        'task_id_pk',
        'checklist_item',
        'status',
    ];

}
