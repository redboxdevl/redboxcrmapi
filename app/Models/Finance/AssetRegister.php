<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocumentType;
use App\Models\propertyType;
use App\Models\HR\Company;
use App\Models\HR\Department;
use App\Models\Assetlocation;


class AssetRegister extends Model
{
    use HasFactory;

    protected $table = "AssetRegister";
    protected $fillable = [
        'company_id',
        'assetTypeId',
        'rec_id',
        'purchasedDate',
        'AssetBookValue',
        'assetLifeId',
        'Location',
        'AssetLocationId',
        'CompanyId',
        'DepartmentId',
        'Quantity',
        'AssetImage',
        'AssetImage2',
        'AssetImage3',
        'AssetDesc',
        'status',
        'uploadBy',
        'agentId',
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