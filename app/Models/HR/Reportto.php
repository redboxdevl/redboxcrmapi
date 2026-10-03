<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reportto extends Model
{
    use HasFactory;

    protected $table = "reportto";

    protected $fillable = [
        'departmentId',
        'designationId',
        'reportToTitle',
    ];
}
