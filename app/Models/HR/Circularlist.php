<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Circularlist extends Model
{
    use HasFactory;

    protected $table = "circularPolicy";

    protected $fillable = [
        'catpolicyId',
        'subjectName',
        'policyDate',
        'Attachment',
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
