<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HolyDays extends Model
{
    use HasFactory;

    protected $table = "HolyDays";

    protected $fillable = [
        'holydays',
        'holydayDate',
    ];
}