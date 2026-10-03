<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complainlist extends Model
{
    use HasFactory;

    protected $table = "Complainmanagement";

    protected $fillable = [
        'agentId',
        'complainNote',
        'status',
    ];

    public function getCountry(){
        return $this->belongsTo('App\Models\Country'::class,'country_id','id');
    }
    public function getCity(){
        return $this->belongsTo('App\Models\City'::class,'city_id','id');
    }
    public function getCompany(){
        return $this->belongsTo(Company::class,'company_id','id');
    }
}
