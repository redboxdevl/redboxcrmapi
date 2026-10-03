<?php

namespace App\Models\RolesPermission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmModule extends Model
{
    use HasFactory;

    protected $table = "crm_modules";

    protected $fillable = [
        'module_name',
    ];

    public function getFeatures(){
        return $this->hasMany(CrmModuleFeatures::class,'module_id');
    }
}
