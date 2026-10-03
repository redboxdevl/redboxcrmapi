<?php

namespace App\Models\AffiliateProgram;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateRegisterModule extends Model
{
    use HasFactory;

    protected $table = "crm_affiliate_registration";

    protected $fillable = [
        'title',
        'full_name',
        'cell_no',
        'email_address',
        'business_name',
        'address',
        'emp_id',
        'status',
        'otpCode',
        'Password',
    ];
}
