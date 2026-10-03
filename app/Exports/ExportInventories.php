<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Inventory\Projectinventory;
use App\Models\Inventory\Project;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use DB;

class ExportInventories implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    
    public function __construct($status,$proid)
    {
        $this->status = $status;
        $this->proid = $proid;
    }
    
    public function collection()
    {
        
        ini_set('max_execution_time', '-1000'); //300 seconds = 5 minutes
        
        $status = $this->status;
        $proid = $this->proid;
        
        if($this->status !='' && $this->proid !=''){
            $InventoryData = Projectinventory::where(['project_id'=>$this->proid,'status'=>$this->status])->get();
        }else{
            $InventoryData = Projectinventory::where(['project_id'=>$this->proid])->get();
        }
        
       
        $array=[];
        foreach($InventoryData as $data){
            
            $Project = Project::where(['id'=>$data->project_id])->first();
            
            $jsonDecode = json_decode($data->jsonFormat);
            
            $newData=[];
          
            foreach($jsonDecode as $mydas => $dataxs){
                $newData[]=$dataxs;
            }
            
            $exportInvent=array(
                'id' => $data->id,
                'project_name'=>$Project == null || $Project == '' ? '' : $Project->project_name,
                'status' => $data->status,
            );
            
            $array[] = Arr::collapse([$exportInvent,$newData]);
        }
          
        return collect($array); 
    }
}
