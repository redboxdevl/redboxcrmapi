<?php

namespace App\Models\AffiliateProgram;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\ActivityTypes;

class AffiliateLeadsModule extends Model
{
    use HasFactory;

    protected $table = "affiliates_leads_tbls";

    protected $fillable = [
        'affiliate_id',
        'countryid',
        'cityid',
        'projectid',
        'typeid',
        'natureid',
        'actMediumId',
        'clientName',
        'clientStatus',
        'Gender',
        'email',
        'mobileNo',
        'passportCnic',
        'whatsappNo',
        'prefLanguage',
        'Budget',
        'timeFrame',
        'RefferdBy',
        'clientInterst',
        'clientActivityDesc',
    ];
    
    
    public function getActivityMedium(){
        return $this->belongsTo(ActivityTypes::class,'actMediumId','id');
    }
    
}
