<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Company;

class Outwardvoucher extends Model
{
    use HasFactory;

    protected $table = "outwardVoucher";
    protected $fillable = [
        'outwardId',
        'VoucherType',
        'VoucherNo',
        'VoucherDate',
        'IssueDate',
        'IssueTo',
        'ChequeDate',
        'PaymentMethod',
        'ChequeNo',
        'BankName',
        'Amount',
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