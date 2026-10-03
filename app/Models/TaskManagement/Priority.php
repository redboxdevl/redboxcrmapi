<?php

namespace App\Models\TaskManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    use HasFactory;

    protected $table = "task_priority";

    protected $fillable = [
        'priorityTitle',
        'status',
    ];
    
  
}
