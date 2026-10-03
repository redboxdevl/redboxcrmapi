<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocumentType;
use App\Models\propertyType;
use App\Models\HR\Company;
class Paymentreciptmode extends Model
{
    use HasFactory;

    protected $table = "paymentRecieptMode";
    protected $fillable = [
        'planId',
        'statementId',
        'recieptId',
        'mode',
        'amount',
        'voucherno',
        'bankname',
        'transactionid',
        'modeDate',
        'payorderno',
        'fileno',
        'chequeNo',
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