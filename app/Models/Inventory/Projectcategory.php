<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projectcategory extends Model
{
    use HasFactory;

    protected $table = "project_categories";

    protected $fillable = [
        'project_id',
        'project_category_name',
    ];
}
