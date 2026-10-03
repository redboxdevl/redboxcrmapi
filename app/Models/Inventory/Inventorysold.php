<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventorysold extends Model
{
    use HasFactory;

    protected $table = "inventory_sold";

    protected $fillable = [
        'companyId',
        'inventory_id',
        'customer_id',
        'employee_id',
        'trackingId',
        'father_name',
        'date_of_birth',
        'client_address',
        'client_image',
        'cnic',
        'cnic_front_image',
        'cnic_back_image',
        'relationship_title',
        'nominee_name',
        'nomineeFatherHusbandName',
        'nominee_image',
        'nominee_cnic',
        'nominee_cell',
        'nominee_cnic_front',
        'nominee_cnic_back',
        'nominee_address',
        'discount_amount',
        'after_discount_amount',
        'received_amount',
        'payment_type',
        'cheque_no',
        'mode_of_payment',
        'reference_type',
        'bank_name',
        'OccupationId',
        'Nationality',
        'Mystatus',
        'status',
    ];
    
    
    public function customer_leads_tbls(){
         return $this->belongsTo('App\Models\ListMyLead'::class,'customer_id','id');
    }
    
     public function project_inventories(){
         return $this->belongsTo('App\Models\Inventory\Projectinventory'::class,'inventory_id','id');
    }
}
