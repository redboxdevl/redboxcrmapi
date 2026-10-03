<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\ListAssignLeads;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class VirginLeadsExport implements FromCollection
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
            
            
            $AssignLeadsAlllx = ListAssignLeads::whereDoesntHave('LeadImpression')->with(['getleads','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['shiftStatus'=>'0'])->whereBetween(DB::raw('DATE(publishAgentDate)'), [$this->fdate, $this->tdate])->get();
            
            // $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee' => function ($query){
            //     $query->orderBy('id', 'desc')->get();
            // } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereBetween(DB::raw('DATE(publishAgentDate)'), [$this->fdate, $this->tdate])->get();
            
        }else if($this->fdate !='' && $this->tdate !='' && $this->search !=''){
            
            
            $AssignLeadsAlllx = ListAssignLeads::whereDoesntHave('LeadImpression')->with(['getleads','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['shiftStatus'=>'0','agentid'=>$this->search])->whereBetween(DB::raw('DATE(publishAgentDate)'), [$this->fdate, $this->tdate])->get();
            
            // $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee' => function ($query){
            //     $query->orderBy('id', 'desc')->get();
            // } ,])->where(['agentid'=>$this->search])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereBetween(DB::raw('DATE(publishAgentDate)'), [$this->fdate, $this->tdate])->get();
            
        }else{
            
            $AssignLeadsAlllx = ListAssignLeads::whereDoesntHave('LeadImpression')->with(['getleads','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['shiftStatus'=>'0'])->get();
            
        }
        
        
        
        $AssingDtaa = []; 
        
        foreach($AssignLeadsAlllx as $data){
            
            $leadStatux = "";
            if($data->shiftStatus == 0){
                $leadStatux = "Assign Leads";
            }else if($data->shiftStatus == 1){
                $leadStatux = "My Leads";
            }else if($data->shiftStatus == 2){
                $leadStatux = "No Interested";
            }else{
                $leadStatux="";
            }
            
            $AssingDtaa[]=array(
                'clientName' => $data->getleads == '' ? '' : $data->getleads->clientName,
                'clientPhone' => $data->getleads == '' ? '' : $data->getleads->clientPhone,
                'clientemail' => $data->getleads == '' ? '' : $data->getleads->clientemail,
                'clientProject' => $data->getleads == '' ? '' : $data->getleads->clientProject,
                'emp_name' => $data->getEmployee == '' ? '' : $data->getEmployee->emp_name,
                'agentid' => $data->agentid,
                'assignDate' => $data->publishAgentDate,
                'duplicateStatus' => $data->leadType,
                'leadStatus' => $leadStatux,
                'nature' => $data->nature,
                'transferStatus' => '',
            );
        }
          
        return collect($AssingDtaa); 
    }
}
