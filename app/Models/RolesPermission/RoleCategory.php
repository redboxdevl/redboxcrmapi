<?php

namespace App\Models\RolesPermission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleCategory extends Model
{
    use HasFactory;

    protected $table = "role_categories";
    
    protected $fillable = [
        'role_category_name',
    ];
}
