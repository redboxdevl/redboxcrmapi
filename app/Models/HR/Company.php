<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = "company";

    protected $fillable = [
        'company_name',
        'country_id',
        'city_id',
        'postal_code',
        'address',
        'contact_number',
        'company_abbr',
        'ownerTitle',
        'companyLogo',
    ];

    public function getCountry(){
        return $this->belongsTo('App\Models\Country'::class,'country_id','id');
    }
    public function getCity(){
        return $this->belongsTo('App\Models\City'::class,'city_id','id');
    }
}
