<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocumentType;
use App\Models\propertyType;
use App\Models\HR\Company;
use App\Models\Finance\stockType;
use App\Models\Inventory\Project;
use App\Models\Finance\stockNature;

class Stockoutregister extends Model
{
    use HasFactory;

    protected $table = "stockOutreg";
    protected $fillable = [
        'stockOutdate',
        'projectId',
        'stockTypeid',
        'stockNatureid',
        'quantityOut',
        'stockDeliveredId',
        'CustomerName',
        'PropertyNo',
        'Size',
        'PropertyType',
        'StockInId',
    ];
    
    // public function getStockTypeId(){
    //     return $this->belongsTo(stockType::class,'stocktypeid','id');
    // }
    
    // public function getProjectId(){
    //     return $this->belongsTo(Project::class,'projectid','id');
    // }
    // public function getNatureId(){
    //     return $this->belongsTo(stockNature::class,'stocknatureid','id');
    // }
}