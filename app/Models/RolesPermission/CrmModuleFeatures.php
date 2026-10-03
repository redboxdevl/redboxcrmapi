<?php

namespace App\Models\RolesPermission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmModuleFeatures extends Model
{
    use HasFactory;
    
    protected $table = "crm_module_feature";

    protected $fillable = [
        'module_id',
        'feature_name',
        'PermissionsData',
    ];
}
