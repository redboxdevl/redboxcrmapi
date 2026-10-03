<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocumentType;
use App\Models\propertyType;
use App\Models\HR\Company;
class SecurityFiles extends Model
{
    use HasFactory;

    protected $table = "security_files";
    protected $fillable = [
        'company_id',
        'docTypeId',
        'proTypeId',
        'docRef',
        'docStatus',
        'releaseName',
        'releaseDate',
        'issuingauthorityId',
        'status',
        'uploadedBy',
        'docDesc',
        'docNature',
        'ownerShipTitle',
        'CompaniesId',
        'Attachement',
        'PurposeId',
    ];
    
    public function getDocTypeId(){
        return $this->belongsTo(DocumentType::class,'docTypeId','id');
    }
    
    public function getProTypeId(){
        return $this->belongsTo(propertyType::class,'proTypeId','id');
    }
    public function getCompanyId(){
        return $this->belongsTo(Company::class,'CompaniesId','id');
    }
}