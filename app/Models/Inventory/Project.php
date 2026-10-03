<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = "projects";

    protected $fillable = [
        'project_name',
        'project_short_name',
        'location_id',
        'country_id',
        'city_id',
        'address',
        'commission',
        'kam',
        'paymentPlanYearly',
        'companyId',
    ];

    public function getProCategory(){
        return $this->hasMany(Projectcategory::class,'project_id','id');
    }

    public function getProLogo(){
        return $this->hasOne(Projectasset::class,'project_id','id');
    }
}
