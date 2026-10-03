<?php

namespace App\Models\RolesPermission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeRole extends Model
{
    use HasFactory;

    protected $table = "employee_roles";
    
    protected $fillable = [
        'emp_id',
        'role_id',
        'feature_id',
        'permission_type',
        'status',
    ];
    
    public function getRole(){
        return $this->belongsTo(RoleCategory::class,'role_id','id');
    }
     public function getFeature(){
        return $this->belongsTo(CrmModuleFeatures::class,'feature_id','id');
    }

}
