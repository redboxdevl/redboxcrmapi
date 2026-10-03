<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocumentType;
use App\Models\Finance\Outwardvoucher;
use App\Models\HR\Company;
use App\Models\Finance\Banklist;


class Bankdeposit extends Model
{
    use HasFactory;

    protected $table = "Bankdeposit";
    protected $fillable = [
        'company_id',
        'bankdepositId',
        'depositDate',
        'depositAmount',
        'referenceNo',
        'modeofdeposit',
        'transactionDetails',
        'recievedFrom',
        'amountinwords',
        'status',
        'accountId',
        'CompanyId',
        'ChequeNo',
        'ChequeDate',
        'BankId'
    ];
    
    public function getcompanyDetails(){
        return $this->belongsTo(Company::class,'CompanyId','id');
    }
    
    // public function getAccountWise(){
    //     return $this->hasMany(Checkoutwardregister::class,'accountId');
    // }
    
    public function getAccountdetails(){
        return $this->belongsTo(Banklist::class,'accountId','id');
    }
}