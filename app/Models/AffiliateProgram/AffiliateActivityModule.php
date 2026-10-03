<?php

namespace App\Models\AffiliateProgram;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateActivityModule extends Model
{
    use HasFactory;

    protected $table = "affiliates_activities_tbls";

    protected $fillable = [
        'affiliate_lead_id',
        'typeId',
        'outcomeId',
        'InterestPercentage',
        'activityDetails',
        'actStatus',
    ];
}
