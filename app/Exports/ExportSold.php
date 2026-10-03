<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Inventory\Inventorysold;
use App\Models\AffiliateProgram\AffiliateLeadsModule;
use App\Models\AffiliateProgram\AffiliateRegisterModule;

use App\Models\ListMyLead;
use App\Models\Inventory\Projectinventory;
use App\Models\Inventory\Project;
use App\Models\Inventory\Projectasset;
use App\Models\Finance\Occupationtbl;
use App\Models\HR\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use DB;

class ExportSold implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    
    public function __construct($proid)
    {
        $this->proid = $proid;
    }
    
    public function collection()
    {
        
        ini_set('max_execution_time', '-1000'); //300 seconds = 5 minutes
        
        $proid = $this->proid;
        
        if($this->proid !=''){
            //$InventorySoldData = Inventorysold::where(['project_id'=>$this->proid])->get();
             $InventorySoldData = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->whereHas('project_inventories', function($q) use ($proid) {
                    $q->where('project_id', $proid);
             })->whereNotIn('status',['Cancelled'])->get();
        }else{
            $InventorySoldData = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->whereNotIn('status',['Cancelled'])->get();
            // $InventorySoldData = Inventorysold::all();
        }
        
       
        $array=[];
        foreach($InventorySoldData as $value){
            
            
            if($value->Mystatus == 'affiliate'){
               $customerDetails = AffiliateLeadsModule::where(['id'=>$value->customer_id])->first();
           }else{
               $customerDetails = ListMyLead::where(['id'=>$value->customer_id])->first();
           }
           
           if($value->project_inventories == '' || $value->project_inventories == null){
               $projectMaterial="";
               $Projectasset="";
           }else{
               $projectMaterial = Project::where(['id'=>$value->project_inventories['project_id']])->first();
               
               $Projectasset = Projectasset::where(['project_id'=>$projectMaterial->id])->first();
           }
           
           if($value->Mystatus == 'affiliate'){
               $Employeess = AffiliateRegisterModule::where(['id'=>$value->employee_id])->first();
               $Employees="";
           }else{
               
               $Employees = Employee::where(['id'=>$value->employee_id])->first();
           }
           
        //   $jsonFromatx = json_decode($value->project_inventories == '' ? '' : $value->project_inventories['jsonFormat']);
           
           
           
           $Occupationtbl = Occupationtbl::where(['id'=>$value->OccupationId])->first();
           
           $Projectinventory = Projectinventory::where(['id'=>$value->inventory_id])->first();
           
        // $jsonDecode = json_encode($Projectinventory->jsonFormat);
        
         $jsonDecode = json_decode($Projectinventory->jsonFormat,JSON_UNESCAPED_SLASHES);
            
        //     $newData=$Projectinventory->jsonFormat;
        
        $newData=[];
          
            foreach($jsonDecode as $dataxs){
                $newData[]=$dataxs;
            }
            
            $exportInvent=array(
                "emp_name" => $Employees == '' || $Employees == null ? $Employeess->full_name : $Employees->emp_name,
                "project_name"=>$projectMaterial == null || $projectMaterial == '' ? '' : $projectMaterial->project_name,
                "father_name" => $value->father_name,
                "date_of_birth" => $value->date_of_birth,
                "cnic" => $value->cnic,
                "clientName"=>$customerDetails == '' || $customerDetails == null ? '' : $customerDetails->clientName,
                "mobileNo"=>$customerDetails == '' || $customerDetails == null ? '' : $customerDetails->mobileNo,
                "relationship_title" => $value->relationship_title,
                "nominee_name" => $value->nominee_name,
                "nomineeFatherHusbandName" => $value->nomineeFatherHusbandName,
                "nominee_cnic" => $value->nominee_cnic,
                "nominee_cell" => $value->nominee_cell,
            );
            
            $array[] = Arr::collapse([$exportInvent,$newData]);
          
        }
          
        return collect($array); 
    }
}
