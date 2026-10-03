<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\LeadImportDatabase;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class ImportLeadExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    
    public function __construct($search,$fdate,$tdate,$project)
    {
        $this->search = $search;
        $this->fdate = $fdate;
        $this->tdate = $tdate;
        $this->project = $project;
    }
    
    public function collection()
    {
        
        ini_set('max_execution_time', '-1000'); //300 seconds = 5 minutes
        
        $Search = $this->search;
        
        if($this->search == '' && $this->project == '' && $this->fdate == '' && $this->tdate == ''){
            
             $response = LeadImportDatabase::all();
            
        }else if($this->search !='' && $this->project == '' && $this->fdate == '' && $this->tdate == ''){
            
            $response = LeadImportDatabase::where('clientPhone', 'like', '' . $this->search . '%')->get();
           
            
        }else if($this->search == '' && $this->project !='' && $this->fdate == '' && $this->tdate == ''){
            
            $response = LeadImportDatabase::where(['clientProject'=>$this->project])->get();
           
            
        }else if($this->search == '' && $this->project == '' && $this->fdate !='' && $this->tdate !=''){
            
            $response = LeadImportDatabase::whereBetween(DB::raw('DATE(created_at)'), array($this->fdate, $this->tdate))->get();
            
        }else{
            $response = LeadImportDatabase::all();
        }
        
        
        
        $importData = []; 
        
        foreach($response as $data){
           
            $importData[]=array(
                'clientName' => $data->clientName,
                'clientPhone' => $data->clientPhone,
                'clientemail' => $data->clientemail,
                'clientProject' => $data->clientProject,
                'duplicateStatus' => $data->leadType,
                'leadStatus' => $data->leadType,
                'nature' => $data->nature,
            );
        }
          
        return collect($importData); 
    }
}
