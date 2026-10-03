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
use App\Exports\SuperDatabaseExport;
use App\Models\Zongcalling;


use Carbon\Carbon;
use DB;

use Validator;

class ListExportSheetController extends Controller
{
 
    public function ListSuperDatabaseExport(Request $request){
        
        $q = $request->q;
        $fdate = $request->fdate;
        $tdate = $request->tdate;
        
        return Excel::download(new SuperDatabaseExport($q,$fdate,$tdate), 'users.xlsx');
    }

    
}
