<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Company;
use App\Models\Finance\Voucherentry;


class Paymentvoucher extends Model
{
    use HasFactory;

    protected $table = "paymentvoucher";
    protected $fillable = [
        'company_id',
        'voucherNo',
        'companyId',
        'Glid',
        'dimensionId',
        'transactionDate',
        'payTo',
        'paymentType',
        'PayDesc',
        'status',
    ];
    
    public function getCompanies(){
        return $this->belongsTo(Company::class,'companyId','id');
    }
    
     public function getVoucherEntry()
    {
        return $this->hasMany(Voucherentry::class,'voucherId');
    }
    
    // public function getProTypeId(){
    //     return $this->belongsTo(propertyType::class,'proTypeId','id');
    // }
    // public function getCompanyId(){
    //     return $this->belongsTo(Company::class,'CompaniesId','id');
    // }
}