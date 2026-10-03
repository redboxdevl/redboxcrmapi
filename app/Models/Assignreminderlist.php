<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignreminderlist extends Model
{
    use HasFactory;
    
    protected $fillable = ['typeTitle'];
    
    protected $table="reminderTypelist";
    
}
