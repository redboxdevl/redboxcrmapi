<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocumentType;
use App\Models\propertyType;
use App\Models\HR\Company;

class Finbills extends Model
{
    use HasFactory;

    protected $table = "Finbills";
    protected $fillable = [
        'company_id',
        'billId',
        'companyId',
        'billTypeId',
        'Beneficiary',
        'billDate',
        'billNo',
        'dueDate',
        'BillAmount',
        'billattachement',
        'BillMonth',
        'status',
        'PaidAmount',
        'LastpaymentDate',
    ];
    
    public function getBilltype(){
        return $this->belongsTo(Billstype::class,'billTypeId','id');
    }
     public function getCompany(){
        return $this->belongsTo(Company::class,'companyId','id');
    }
    
    public function billamount(){
        return $this->hasMany(Finbills::class);
    }
    // public function getCompanyId(){
    //     return $this->belongsTo(Company::class,'CompaniesId','id');
    // }
}