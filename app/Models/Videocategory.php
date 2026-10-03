<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Videocategory extends Model
{
    use HasFactory;
    protected $table ="videocategories";

    protected $fillable = [
        'video_category_name',
    ];
}
