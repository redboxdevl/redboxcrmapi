<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocumentType;
use App\Models\propertyType;
use App\Models\HR\Company;

class Dimensionlevel3 extends Model
{
    use HasFactory;

    protected $table = "dimensionlevel3";
    protected $fillable = [
        'level1Code',
        'level2Code',
        'Code',
        'Description',
        'status',
    ];
    
    public function getLevel1(){
        return $this->belongsTo(Dimensionlevel1::class,'level1Code','id');
    }
    public function getLevel2(){
        return $this->belongsTo(Dimensionlevel2::class,'level2Code','id');
    }
    
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