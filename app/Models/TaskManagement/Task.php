<?php

namespace App\Models\TaskManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = "task";

    protected $fillable = [
        'task_id',
        'agentId',
        'task_title',
        'description',
        'taskStatus',
        'departmentId',
        'dueDate',
        'priorityId',
        'assigned_by',
        'assigned_to'
    ];
    
     public function getcheckList(){
        return $this->hasMany(TaskChecklist::class,'task_id_pk','id');
    }
    
     public function gettaskComment(){
        return $this->hasMany(TaskComment::class,'task_id_pk');
    }
    
     public function gettaskAttachement(){
        return $this->belongsTo(TaskAttachment::class,'id','task_id_pk');
    }
    
     public function gettaskHistory(){
        return $this->belongsTo(TaskHistory::class,'id','task_id_pk');
    }
    
    
    
    
    
    
    
    
}
