<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\ListAssignLeads;
use App\Models\ListMyLead;
use App\Models\HR\Employee;
use App\Models\CustomerActivity;



use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use DB;

class SuperDatabaseExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    
    public function __construct($q,$fdate,$tdate)
    {
        $this->q = $q;
        $this->fdate = $fdate;
        $this->tdate = $tdate;
    }
    
    public function collection()
    {
        
        // $Search = $this->q;

        if($this->fdate !='' && $this->tdate !=''  && $this->q ==''){
            
    
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getleads','getMyLeadsData' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->whereBetween(DB::raw('DATE(publishAgentDate)'), [$this->fdate, $this->tdate])->get();
            
        }else if($this->fdate !='' && $this->tdate !='' && $this->q !=''){
            
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['agentid'=>$this->q])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereBetween(DB::raw('DATE(publishAgentDate)'), [$this->fdate, $this->tdate])->get();
            
        }else if($this->fdate =='' && $this->tdate =='' && $this->q !=''){
            
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['leadsassigndatabase.agentid'=>$this->q])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->get();
            
        }else{
            
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->get();
            
        }
        
        
        
        $newxxs = [];
        $array=[];
        
        foreach($AssignLeadsAlllx as $data){
            
            // $ListMyLead = ListMyLead::where(['leadid'=>$data->id])->first();
            
            $leadStatux = "";
            if($data->shiftStatus == 0){
                $leadStatux = "Assign Leads";
                $activityData="";
            }else if($data->shiftStatus == 1){
                if($data->getMyLeadsData == '' || $data->getMyLeadsData == null){
                    $leadStatux="";
                }else if($data->getMyLeadsData->LeadStatus == 'My Lead'){
                    $leadStatux = "My Leads";
                }else{
                    $leadStatux = "Hot Leads";
                }
                
                if($data->getMyLeadsData == null || $data->getMyLeadsData == ''){
                    $activityData=[];
                }else{
                    $activityData = CustomerActivity::where(['customerId'=>$data->getMyLeadsData->id])->get();
                    
                     $newxxs = [];
            
                    foreach($activityData as $imxs){
                        
                        $newxxs[]=$imxs['activityDetails'];
                    }
                }
                
                
               
                
            }else if($data->shiftStatus == 2){
                $leadStatux = "No Interested";
                $activityData="";
            }else{
                $leadStatux="";
                $activityData="";
            }
            
            
            
            $array[]=array(
                'clientName' => $data->getleads == '' || $data->getleads == null ? '' : $data->getleads->clientName,
                'clientPhone' => $data->getleads == '' || $data->getleads == null ? '' : $data->getleads->clientPhone,
                'clientemail' => $data->getleads == '' || $data->getleads == null ? '' : $data->getleads->clientemail,
                'clientProject' => $data->getleads == '' || $data->getleads == null ? '' : $data->getleads->clientProject,
                'emp_name' => $data->getEmployee == '' ? '' : $data->getEmployee->emp_name,
                'agentid' => $data->agentid,
                'assignDate' => $data->publishAgentDate,
                'duplicateStatus' => $data->getleads == '' || $data->getleads == null ? '' : $data->getleads->leadType,
                'leadStatus' => $leadStatux,
                'nature' => $data->getleads == '' || $data->getleads == null ? '' : $data->getleads->nature,
                'transferStatus' => '',
                '$newxxs'=>$newxxs,
            );
            
            
            // $array[] = Arr::collapse([$AssingDtaass,$newxxs]);
            
        }
          
        return collect($array); 
    }
}
