<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainingtype extends Model
{
    use HasFactory;

    protected $table = "training_types";

    protected $fillable = [
        'training_type_name',
    ];
}
