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

class Stockregister extends Model
{
    use HasFactory;

    protected $table = "stockRegister";
    protected $fillable = [
        'stockIndate',
        'projectid',
        'stocknatureid',
        'stocktypeid',
        'quantityIn',
        'remainingQty',
        'stockrecby',
        'stockdeliveredby',
        'stockImg',
        'serialFrom',
        'serialTo'
    ];
    
    public function getStockTypeId(){
        return $this->belongsTo(stockType::class,'stocktypeid','id');
    }
    
    public function getProjectId(){
        return $this->belongsTo(Project::class,'projectid','id');
    }
    public function getNatureId(){
        return $this->belongsTo(stockNature::class,'stocknatureid','id');
    }
}