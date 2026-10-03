<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purposeprop extends Model
{
    use HasFactory;
    protected $table="Purposeprop";
    
    protected $fillable = ['purposetitle'];
}