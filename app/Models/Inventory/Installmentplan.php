<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installmentplan extends Model
{
    use HasFactory;

    protected $table = "installment_plans";

    protected $fillable = [
        'sold_id',
        'total_amount',
        'received_amount',
        'installment_date',
        'installment_amount',
        'remaining_amount',
    ];
}
