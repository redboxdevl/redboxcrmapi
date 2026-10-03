<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocumentType;
use App\Models\propertyType;
use App\Models\HR\Company;

class Chartlevel3 extends Model
{
    use HasFactory;

    protected $table = "chartofaccountlevel3";
    protected $fillable = [
        'level1Code',
        'level2Code',
        'Code',
        'Description',
        'status',
        'glstatus',
    ];
    
    public function getLevel1(){
        return $this->belongsTo(Chartlevel1::class,'level1Code','id');
    }
    public function getLevel2(){
        return $this->belongsTo(Chartlevel2::class,'level2Code','id');
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