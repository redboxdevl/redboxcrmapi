<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryCancelled extends Model
{
    use HasFactory;

    protected $table = "inventory_cancelled";

    protected $fillable = [
        'inventoryId',
        'customerId',
        'employeeId',
        'cancelledReason',
        'cancelledDate',
        'status',
    ];
}
