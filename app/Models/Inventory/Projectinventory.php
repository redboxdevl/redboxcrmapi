<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projectinventory extends Model
{
    use HasFactory;

    protected $table = "project_inventories";

    protected $fillable = [
        'project_id',
        'jsonFormat',
    ];
    
    public function Holdinventories(){
        return $this->belongsTo(Inventoryhold::class,'id','inventory_id');
    }
    public function ProjectData(){
        return $this->belongsTo(Project::class,'project_id','id');
    }
    
    
}
