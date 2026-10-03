<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\ListAssignLeads;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class AssignLeadsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    
    public function __construct($agentId)
    {
        $this->agentId = $agentId;
    }
    
    public function collection()
    {
        
        ini_set('max_execution_time', '-1000'); //300 seconds = 5 minutes
        
        // $Search = $this->search;
        
       $data = ListAssignLeads::with(['getEmployee' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->get();
        
        
        
        return collect($data->toArray()); 
    }
}