<?php

namespace App\Models\WebsiteMaterial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocumentType;
use App\Models\propertyType;
use App\Models\HR\Company;


class Pricingpage extends Model
{
    use HasFactory;

    protected $table = "Pricingpage";
    protected $fillable = [
        'pageTitle',
        'pageBanner',
        'headerEditor',
        'videoLink',
        'middleEditor',
        'footerEditor',
        'pageUrl',
    ];
    
    // public function getDocTypeId(){
    //     return $this->belongsTo(DocumentType::class,'docTypeId','id');
    // }
    
    // public function getProTypeId(){
    //     return $this->belongsTo(propertyType::class,'proTypeId','id');
    // }
    // public function getCompanyId(){
    //     return $this->belongsTo(Company::class,'CompaniesId','id');
    // }
}