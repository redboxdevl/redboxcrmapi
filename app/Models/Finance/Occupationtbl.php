<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Occupationtbl extends Model
{
    use HasFactory;

    protected $table = "OccupationTbl";
    protected $fillable = [
        'OccuptationTitle',
        'status',
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