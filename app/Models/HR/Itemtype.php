<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Itemtype extends Model
{
    use HasFactory;

    protected $table = "purchaseitemType";

    protected $fillable = [
        'typeTitle',
        'status',
    ];

}
