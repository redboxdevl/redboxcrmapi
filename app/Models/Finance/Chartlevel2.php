<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocumentType;
use App\Models\propertyType;
use App\Models\HR\Company;

class Chartlevel2 extends Model
{
    use HasFactory;

    protected $table = "chartofaccountlevel2";
    protected $fillable = [
        'level1Code',
        'Code',
        'Description',
        'status',
    ];
    
    public function getLevel1(){
        return $this->belongsTo(Chartlevel1::class,'level1Code','id');
    }
    
    // public function getProTypeId(){
    //     return $this->belongsTo(propertyType::class,'proTypeId','id');
    // }
    // public function getCompanyId(){
    //     return $this->belongsTo(Company::class,'CompaniesId','id');
    // }
}