<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocumentType;
use App\Models\propertyType;
use App\Models\HR\Company;

class Salesform extends Model
{
    use HasFactory;

    protected $table = "SalesFormdata";
    protected $fillable = [
        'company_id',
        'agentId',
        'projectId',
        'typeId',
        'propertyNo',
        'Size',
        'sizeType',
        'categoryName',
        'SaleAmount',
        'Discount',
        'TotalCost',
        'totalPaymentRecieved',
        'saleformVoucherNo',
        'customerName',
        'Cnic',
        'contactNo',
        'totalCommission',
        'CompanyId',
        'status',
        'blockName',
        'dateOfSale',
        'ReferenceContactNo',
        'salesConsultant',
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