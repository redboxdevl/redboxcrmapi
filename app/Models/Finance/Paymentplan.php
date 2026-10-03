<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocumentType;
use App\Models\propertyType;
use App\Models\HR\Company;
class Paymentplan extends Model
{
    use HasFactory;

    protected $table = "paymentPlan";
    protected $fillable = [
        'bookingDate',
        'CompanyId',
        'ProjectId',
        'TypeId',
        'BlockName',
        'PropertyNo',
        'Size',
        'Category',
        'CustomerId',
        'ScheduleType',
        'SchedulePlan',
        'Booking',
        'Allocation',
        'COnfirmation',
        'Demarcation',
        'Possession',
        'MonthlyAmount',
        'QuarterlyAmount',
        'HalfYearAmount',
        'YearlyAmount',
        'TotalSalesAmount',
        'DiscountPrice',
        'NetSalesPrice',
        'TotalScheduleAmount',
        'BalanaceScheduleAmount',
        'MonthlyNoOfPayment',
        'QuarterlyNoOfPayment',
        'HalfYearNoOfPayment',
        'YearlyNoofPayment',
        'customerStatus',
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