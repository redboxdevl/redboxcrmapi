<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jobpost extends Model
{
    use HasFactory;

    protected $table = "job_posts";

    protected $fillable = [
        'job_title',
        'description',
        'status',
        'published_date',
        'deadline',
    ];
}
