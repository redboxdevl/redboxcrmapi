<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocumentType;
use App\Models\propertyType;
use App\Models\HR\Company;
use App\Models\HR\Department;
use App\Models\Assetlocation;


class Vehcilepass extends Model
{
    use HasFactory;

    protected $table = "vehcilePass";
    protected $fillable = [
        'agentId',
        'PassDatetime',
        'DepartmentId',
        'VehcileType',
        'PlaceToVisit',
        'PurposeofVisit',
        'Meterreading',
        'TimeIn',
        'TimeOut',
        'MeterreadingArrival',
        'DistanceCovered',
        'TotalDuration',
        'status',
    ];
    
    // public function getDocTypeId(){
    //     return $this->belongsTo(DocumentType::class,'docTypeId','id');
    // }
    
    public function getAssetLocation(){
        return $this->belongsTo(Assetlocation::class,'AssetLocationId','id');
    }
    public function getCompanyId(){
        return $this->belongsTo(Company::class,'CompanyId','id');
    }
    
    public function getDepartmentId(){
        return $this->belongsTo(Department::class,'DepartmentId','id');
    }
    
     public function getAssetType(){
        return $this->belongsTo(AssetType::class,'assetTypeId','id');
    }
    
    
}