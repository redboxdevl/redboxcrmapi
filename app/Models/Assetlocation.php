<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assetlocation extends Model
{
    use HasFactory;
    
    protected $table="tbl_location";
    
    protected $fillable = ['locationName','status'];
}
