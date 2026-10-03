<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jobcandidate extends Model
{
    use HasFactory;

    protected $table = "job_candidates";
    protected $fillable = [
        'job_post_id',
        'applied_date',
        'status',
    ];
}
