<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocumentType;
use App\Models\Finance\Outwardvoucher;
use App\Models\HR\Company;
use App\Models\Finance\Banklist;


class Checkoutwardregister extends Model
{
    use HasFactory;

    protected $table = "ChequeOutwardRegister";
    protected $fillable = [
        'company_id',
        'chequeNoId',
        'voucherNo',
        'accountId',
        'issueDate',
        'chequeDate',
        'chequeAmount',
        'ChequeAmountInWord',
        'Purpose',
        'IssuedTo',
        'clearedDate',
        'stopDate',
        'stopReason',
        'cancelledDate',
        'cancelledReason',
        'holdDate',
        'holdReason',
    ];
    
    public function ChequeListData(){
        return $this->belongsTo(Chequelist::class,'chequeNoId','id');
    }
    
    public function getAccountWise(){
        return $this->hasMany(Checkoutwardregister::class,'accountId');
    }
    
    public function getBankAccounts(){
        return $this->belongsTo(Banklist::class,'accountId','id');
    }
}