<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssignReminder;
use Illuminate\Http\Request;
use App\Models\ListAssignLeads;
use App\Models\ListNotInterested;
use App\Models\CustomerActivity;
use App\Models\ListMyLead;
use App\Models\HR\Employee;
use App\Models\TranasferShareLeads;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\LeadImportDatabase;
use App\Exports\CallingDataExport;
use App\Models\Zongcalling;


use Carbon\Carbon;
use DB;

use Validator;

class ListCallingExportSheet extends Controller
{
 
    public function index(Request $request){
        
        $type = $request->type;
        $agent = $request->agent;
        $status = $request->status;
        $fdate = $request->fdate;
        $edate = $request->edate;
        
        return Excel::download(new CallingDataExport($type,$agent,$status,$fdate,$edate), 'callingdataexport.xlsx');
    }

    
}
