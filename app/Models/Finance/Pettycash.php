<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocumentType;
use App\Models\propertyType;
use App\Models\HR\Company;
use App\Models\Finance\Pettycashvoucher;

use Carbon\Carbon;

class Pettycash extends Model
{
    use HasFactory;

    protected $table = "PettyCashTbl";
    protected $fillable = [
        'company_id',
        'referenceId',
        'dateOfCash',
        'CompanyId',
        'CashAmount',
        'ModeOfPayment',
        'ChequeNo',
        'ChhequeDate',
        'BankId',
        'agentId',
        'Payee',
    ];
    
    // public function getDocTypeId(){
    //     return $this->belongsTo(DocumentType::class,'docTypeId','id');
    // }
    
    // public function getProTypeId(){
    //     return $this->belongsTo(propertyType::class,'proTypeId','id');
    // }
        public function getCompanies(){
            return $this->belongsTo(Company::class,'CompanyId','id');
        }
        
        public function getVoucherEntry()
        {
            return $this->hasMany(Pettycashvoucher::class,'pettyId')->where('VoucherDate','=', date("Y-m-d"));
        }
        
        public function getVoucherEntryWeekly()
        {
            return $this->hasMany(Pettycashvoucher::class,'pettyId')->whereBetween('VoucherDate', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        }
        
        public function getVoucherEntryMonthly()
        {
            return $this->hasMany(Pettycashvoucher::class,'pettyId')->whereMonth('created_at', Carbon::now()->month);
        }
        
        public function getVoucherEntryLastMonth()
        {
            return $this->hasMany(Pettycashvoucher::class,'pettyId')->whereBetween('created_at', 
            [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()]
            );
        }
    
    
}