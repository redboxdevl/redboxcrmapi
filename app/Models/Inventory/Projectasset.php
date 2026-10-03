<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projectasset extends Model
{
    use HasFactory;

    protected $table = "project_assets";

    protected $fillable = [
        'project_id',
        'asset_name',
        'project_asset_path',
        'asset_type'
    ];
}
