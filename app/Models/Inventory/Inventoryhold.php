<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventoryhold extends Model
{
    use HasFactory;

    protected $table = "inventory_hold";

    protected $fillable = [
        'inventory_id',
        'customer_id',
        'employee_id',
        'hold_date',
        'hold_time',
        'hold_desc',
    ];
}
