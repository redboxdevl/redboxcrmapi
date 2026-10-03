<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\TranasferShareLeads;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use App\Models\LeadImportDatabase;
use App\Models\HR\Employee;

class TransferDatabaseExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    
    public function __construct($q,$fdate,$tdate)
    {
        $this->search = $q;
        $this->fdate = $fdate;
        $this->tdate = $tdate;
    }
    
    public function collection()
    {
        
        ini_set('max_execution_time', '-1000'); //300 seconds = 5 minutes
        
        $Search = $this->search;
        
        if($this->fdate !='' && $this->tdate !=''  && $this->search ==''){
            
            
            $AssignLeadsAlllx = TranasferShareLeads::with(['LeadData','toAgent' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->whereBetween(DB::raw('DATE(created_at)'), [$this->fdate, $this->tdate])->get();
            
        }else if($this->fdate !='' && $this->tdate !='' && $this->search !=''){
            
            $AssignLeadsAlllx = TranasferShareLeads::with(['LeadData','toAgent' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->whereBetween(DB::raw('DATE(created_at)'), [$this->fdate, $this->tdate])->get();
            
        }else{
            
            $AssignLeadsAlllx = TranasferShareLeads::with(['LeadData','toAgent' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->get();
            
        }
        
        
        
        $AssingDtaa = []; 
        
        foreach($AssignLeadsAlllx as $data){
            
            $importData = LeadImportDatabase::where(['id'=>$data->leadid])->get();
            
            foreach($importData as $itemDx){
                
                
                $empDatax = Employee::where(['id'=>$data->tagent])->get();
                
                foreach($empDatax as $mxs){
                     $AssingDtaa[]=array(
                        'clientName' => $itemDx->clientName,
                       'clientProject' => $itemDx->clientProject,
                       'emp_name' => $mxs->emp_name,
                       'transferNote' => $data->transferNote,
                       'statusType' => $data->statusType,
                       'publishStatus' => $data->publishStatus,
                       'created_at' => $data->created_at,
                    );
                }
               
                
            }
            
            
        }
          
        return collect($AssingDtaa); 
    }
}