<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leavetype extends Model
{
    use HasFactory;

    protected $table = "leave_types";

    protected $fillable = [
        'leave_type_name',
        'num_of_days',
    ];
}
