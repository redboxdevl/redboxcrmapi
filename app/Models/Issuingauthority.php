<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issuingauthority extends Model
{
    use HasFactory;

    protected $table = "Issuingauthority";
    protected $fillable = ['issuingTitle'];
    

}