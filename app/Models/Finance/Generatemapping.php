<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocumentType;
use App\Models\propertyType;
use App\Models\HR\Company;

class Generatemapping extends Model
{
    use HasFactory;

    protected $table = "accountMapping";
    protected $fillable = [
        'glLevel3id',
        'dimensionL2D',
        'status',
    ];
    
    
     public function getLevel3(){
        return $this->belongsTo(Chartlevel3::class,'glLevel3id','id');
    }
    
    public function getmappLevel2(){
        return $this->hasMany(Generatemappinglevel::class,'mapid','id');
    }
    
    // public function getProTypeId(){
    //     return $this->belongsTo(propertyType::class,'proTypeId','id');
    // }
    // public function getCompanyId(){
    //     return $this->belongsTo(Company::class,'CompaniesId','id');
    // }
}