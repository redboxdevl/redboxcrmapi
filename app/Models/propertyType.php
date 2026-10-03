<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class propertyType extends Model
{
    use HasFactory;
    protected $table="cms_property_types";
    
    protected $fillable = ['type_name','shortName'];
}