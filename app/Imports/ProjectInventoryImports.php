<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use App\Models\LeadImportDatabase;
use App\Models\Inventory\Projectinventory;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectInventoryImports implements ToModel, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
  

    public function model(array $row)
    {
        $project_id = request()->post('project_id');

        return new Projectinventory([
            'project_id' => $project_id,
            'jsonFormat' => json_encode($row,JSON_UNESCAPED_SLASHES),
        ]);
        
    }
    
}
