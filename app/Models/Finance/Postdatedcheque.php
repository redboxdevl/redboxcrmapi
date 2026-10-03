<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Company;

class Postdatedcheque extends Model
{
    use HasFactory;

    protected $table = "Postdatedcheque";
    protected $fillable = [
        'chequeDate',
        'RecievedDate',
        'Amount',
        'AmountInWords',
        'BankName',
        'BranchName',
        'ChequeNo',
        'RecievedFrom',
        'Purpose',
        'Status',
        'PayeeType',
        'BeneficiaryTitle',
        'AttachementImage',
        'DateOfDeposit',
        'AcDeposit',
        'DateOfReleased',
        'ReleasedTitle',
        'DateOfReturned',
        'ReturnTitle',
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