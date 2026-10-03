<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocumentType;
use App\Models\propertyType;
use App\Models\HR\Company;
class Payreciepts extends Model
{
    use HasFactory;

    protected $table = "PayReciepts";
    protected $fillable = [
        'company_id',
        'dateOfReciept',
        'RecieptNo',
        'ProjectId',
        'CompanyId',
        'PropTypeId',
        'ProNo',
        'BlockNo',
        'Size',
        'CustomerName',
        'Cnic',
        'ContactNo',
        'Reference',
        'ReferenceContactNo',
        'SaleAmount',
        'Discount',
        'Remarks',
        'PostedBy',
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