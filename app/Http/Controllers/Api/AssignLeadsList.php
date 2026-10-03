<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ListAssignLeads;
use App\Models\LeadImpression;
use App\Models\AssignReminder;
use App\Models\ReasonNotInterested;
use App\Models\TranasferShareLeads;
use App\Models\ListMyLead;
use App\Models\Country;
use App\Models\City;
use App\Models\ProjectAdd;
use App\Models\apartmentType;
use App\Models\natureProject;
use App\Models\LeadImportDatabase;
use App\Models\ClientStatus;
use App\Models\Gender;
use App\Models\ActivityTypes;
use App\Models\Language;
use App\Models\Budget;
use App\Models\TimeFrame;
use App\Models\InterestedPercentage;
use App\Models\AgentEmployee;
use App\Models\Notinterestedopt;
use App\Models\HR\Employee;
use App\Models\AgeGroup;
use App\Models\MaritalStatus;
use App\Models\RolesPermission\CrmModule;
use App\Models\RolesPermission\EmployeeRole;
use App\Models\RolesPermission\CrmModuleFeatures;
use App\Models\Zongcalling;
use App\Models\EmploymentType;
use App\Models\HR\HolyDays;
use App\Models\EmpShift;
use App\Models\Assignreminderlist;
use App\Models\AffiliateProgram\AffiliateLeadsModule;
use App\Models\leadStatus;

use Illuminate\Support\Arr;

use App\Models\Inventory\Projectinventory;

use App\Http\Controllers\Api\CommonController;

use App\Models\TaskManagement\Priority;
use App\Models\TaskManagement\TaskStatus;

use App\Models\TaskManagement\TaskHistory;
use App\Models\TaskManagement\Task;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VirginLeadsExport;

use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use DB;


class AssignLeadsList extends Controller
{

public function assignList(Request $request){

$orderbY = $request->orderBy ?? "id";
$ordertype = $request->orderType ?? "desc";
$PerPage = $request->perPage ?? "10";
$page = $request->page ?? "1";
$agentid = $request->agentid;
$leadid = $request->leadid;
$search = $request->q;
$teamObj = $request->teamObj ?? "";
$ProjectN = $request->ProjectN ?? "";
$salesId = $request->salesId ?? "";
$companyId = $request->company_id ?? "";

if(is_null($agentid) && empty($teamObj) && empty($leadid) && empty($ProjectN) && empty($salesId) && empty($search)){

$listAssignTotalPages = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0','companyId'=>$companyId])->orderBy($orderbY, $ordertype)->paginate($PerPage)->total();

$listAssignCount = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0','companyId'=>$companyId])->orderBy($orderbY, $ordertype)->count();

}else if(empty($agentid) && empty($teamObj) && !empty($leadid) && empty($ProjectN) && empty($salesId) && empty($search)){

$listAssign = ListAssignLeads::with(['getleads','getvirginleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0','leadid'=>$leadid])->orderBy($orderbY, $ordertype)->get();

$listAssignTotalPages=0;
$listAssignCount=0;

}else if($agentid == 'all' && empty($teamObj) && empty($leadid) && empty($ProjectN) && empty($salesId) && empty($search)){

$listAssign = ListAssignLeads::with(['getleads','getvirginleads','getreminder','get_transferleads' => function ($query){
        $query->orderBy('id', 'desc')->get();
    } ,])->where(['shiftStatus'=>'0','companyId'=>$companyId])->orderBy($orderbY, $ordertype)->paginate($PerPage);
    
$listAssignTotalPages = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0','companyId'=>$companyId])->orderBy($orderbY, $ordertype)->paginate($PerPage)->total();

$listAssignCount = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0','companyId'=>$companyId])->orderBy($orderbY, $ordertype)->count();

}else if($agentid == 'all' && empty($teamObj) && empty($leadid) && empty($ProjectN) && empty($salesId) && !empty($search)){

$listAssign = ListAssignLeads::with(['getleads','getvirginleads','getreminder','get_transferleads' => function ($query){
        $query->orderBy('id', 'desc')->get();
    } ,])->whereHas('getleads', function($query) use($search) {
        if($search){
             $query->where('clientPhone','LIKE','%'.$search.'%');
        }
    })->where(['shiftStatus'=>'0','companyId'=>$companyId])->orderBy($orderbY, $ordertype)->paginate($PerPage);
    
$listAssignTotalPages = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0','companyId'=>$companyId])->orderBy($orderbY, $ordertype)->paginate($PerPage)->total();

$listAssignCount = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0','companyId'=>$companyId])->orderBy($orderbY, $ordertype)->count();

}else if($agentid == 'all' && !empty($teamObj) && empty($leadid) && empty($ProjectN) && empty($salesId) && empty($search)){

$myArray2 = explode(',', $teamObj);
$myArray = str_replace(array('[',']'),'',$myArray2);

$listAssign = ListAssignLeads::with(['getleads','getvirginleads','getreminder','get_transferleads' => function ($query){
        $query->orderBy('id', 'desc')->get();
    } ,])->where(['shiftStatus'=>'0','companyId'=>$companyId])->whereIn('agentid',$myArray)->orderBy($orderbY, $ordertype)->paginate($PerPage);
    
$listAssignTotalPages = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0'])->whereIn('agentid',$myArray)->orderBy($orderbY, $ordertype)->paginate($PerPage)->total();

$listAssignCount = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0','companyId'=>$companyId])->whereIn('agentid',$myArray)->orderBy($orderbY, $ordertype)->count();

}else if($agentid == 'all' && empty($teamObj) && empty($leadid) && empty($ProjectN) && empty($salesId) && empty($search)){

$myArray2 = explode(',', $teamObj);
$myArray = str_replace(array('[',']'),'',$myArray2);

$listAssign = ListAssignLeads::with(['getleads','getvirginleads','getreminder','get_transferleads' => function ($query){
        $query->orderBy('id', 'desc')->get();
    } ,])->where(['shiftStatus'=>'0','companyId'=>$companyId])->orderBy($orderbY, $ordertype)->paginate($PerPage);
    
$listAssignTotalPages = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0','companyId'=>$companyId])->orderBy($orderbY, $ordertype)->paginate($PerPage)->total();

$listAssignCount = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0','companyId'=>$companyId])->orderBy($orderbY, $ordertype)->count();

}else if($agentid == 'all' && !empty($teamObj) && empty($leadid) && empty($ProjectN) && !empty($salesId) && empty($search)){

$myArray2 = explode(',', $teamObj);
$myArray = str_replace(array('[',']'),'',$myArray2);

$listAssign = ListAssignLeads::with(['getleads','getvirginleads','getreminder','get_transferleads' => function ($query){
        $query->orderBy('id', 'desc')->get();
    } ,])->where(['shiftStatus'=>'0','companyId'=>$companyId])->whereIn('agentid',$myArray)->where(['agentid'=>$salesId])->orderBy($orderbY, $ordertype)->paginate($PerPage);
    
$listAssignTotalPages = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0','companyId'=>$companyId])->whereIn('agentid',$myArray)->where(['agentid'=>$salesId])->orderBy($orderbY, $ordertype)->paginate($PerPage)->total();

$listAssignCount = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0','companyId'=>$companyId])->whereIn('agentid',$myArray)->where(['agentid'=>$salesId])->orderBy($orderbY, $ordertype)->count();

}else if($agentid == 'all' && empty($teamObj) && empty($leadid) && empty($ProjectN) && !empty($salesId) && empty($search)){

$myArray2 = explode(',', $teamObj);
$myArray = str_replace(array('[',']'),'',$myArray2);

$listAssign = ListAssignLeads::with(['getleads','getvirginleads','getreminder','get_transferleads' => function ($query){
        $query->orderBy('id', 'desc')->get();
    } ,])->where(['shiftStatus'=>'0','companyId'=>$companyId])->where(['agentid'=>$salesId])->orderBy($orderbY, $ordertype)->paginate($PerPage);
    
$listAssignTotalPages = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0','companyId'=>$companyId])->where(['agentid'=>$salesId])->orderBy($orderbY, $ordertype)->paginate($PerPage)->total();

$listAssignCount = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0','companyId'=>$companyId])->where(['agentid'=>$salesId])->orderBy($orderbY, $ordertype)->count();

}else if($agentid == 'all' && empty($teamObj) && empty($leadid) && !empty($ProjectN) && !empty($salesId) && empty($search)){

$myArray2 = explode(',', $teamObj);
$myArray = str_replace(array('[',']'),'',$myArray2);

$listAssign = ListAssignLeads::with(['getleads','getvirginleads','getreminder','get_transferleads' => function ($query){
        $query->orderBy('id', 'desc')->get();
    } ,])->whereHas('getleads', function($query) use($ProjectN) {
        if($ProjectN){
             $query->where('clientProject','LIKE','%'.$ProjectN.'%');
        }
    })->where(['shiftStatus'=>'0','companyId'=>$companyId])->where(['agentid'=>$salesId])->orderBy($orderbY, $ordertype)->paginate($PerPage);
    
$listAssignTotalPages = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->whereHas('getleads', function($query) use($ProjectN) {
        if($ProjectN){
             $query->where('clientProject','LIKE','%'.$ProjectN.'%');
        }
    })->where(['shiftStatus'=>'0','companyId'=>$companyId])->where(['agentid'=>$salesId])->orderBy($orderbY, $ordertype)->paginate($PerPage)->total();

$listAssignCount = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->whereHas('getleads', function($query) use($ProjectN) {
        if($ProjectN){
             $query->where('clientProject','LIKE','%'.$ProjectN.'%');
        }
    })->where(['shiftStatus'=>'0','companyId'=>$companyId])->where(['agentid'=>$salesId])->orderBy($orderbY, $ordertype)->count();

}else if($agentid == 'all' && !empty($teamObj) && empty($leadid) && !empty($ProjectN) && !empty($salesId) && empty($search)){

$myArray2 = explode(',', $teamObj);
$myArray = str_replace(array('[',']'),'',$myArray2);

$listAssign = ListAssignLeads::with(['getleads','getvirginleads','getreminder','get_transferleads' => function ($query){
        $query->orderBy('id', 'desc')->get();
    } ,])->whereHas('getleads', function($query) use($ProjectN) {
        if($ProjectN){
             $query->where('clientProject','LIKE','%'.$ProjectN.'%');
        }
    })->where(['shiftStatus'=>'0','companyId'=>$companyId])->whereIn('agentid',$myArray)->where(['agentid'=>$salesId])->orderBy($orderbY, $ordertype)->paginate($PerPage);
    
$listAssignTotalPages = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->whereHas('getleads', function($query) use($ProjectN) {
        if($ProjectN){
             $query->where('clientProject','LIKE','%'.$ProjectN.'%');
        }
    })->where(['shiftStatus'=>'0','companyId'=>$companyId])->whereIn('agentid',$myArray)->where(['agentid'=>$salesId])->orderBy($orderbY, $ordertype)->paginate($PerPage)->total();

$listAssignCount = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->whereHas('getleads', function($query) use($ProjectN) {
        if($ProjectN){
             $query->where('clientProject','LIKE','%'.$ProjectN.'%');
        }
    })->where(['shiftStatus'=>'0','companyId'=>$companyId])->whereIn('agentid',$myArray)->where(['agentid'=>$salesId])->orderBy($orderbY, $ordertype)->count();

}else if($agentid == 'all' && !empty($teamObj) && empty($leadid) && empty($ProjectN) && empty($salesId) && !empty($search)){

$myArray2 = explode(',', $teamObj);
$myArray = str_replace(array('[',']'),'',$myArray2);

$listAssign = ListAssignLeads::with(['getleads','getvirginleads','getreminder','get_transferleads' => function ($query){
        $query->orderBy('id', 'desc')->get();
    } ,])->whereHas('getleads', function($query) use($search) {
        if($search){
             $query->where('clientPhone','LIKE','%'.$search.'%');
             $query->orWhere('clientName','LIKE','%'.$search.'%');
        }
    })->where(['shiftStatus'=>'0','companyId'=>$companyId])->whereIn('agentid',$myArray)->orderBy($orderbY, $ordertype)->paginate($PerPage);
    
$listAssignTotalPages = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->whereHas('getleads', function($query) use($search) {
        if($search){
             $query->where('clientPhone','LIKE','%'.$search.'%');
             $query->orWhere('clientName','LIKE','%'.$search.'%');
        }
    })->where(['shiftStatus'=>'0','companyId'=>$companyId])->whereIn('agentid',$myArray)->orderBy($orderbY, $ordertype)->paginate($PerPage)->total();

$listAssignCount = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->whereHas('getleads', function($query) use($search) {
        if($search){
             $query->where('clientPhone','LIKE','%'.$search.'%');
             $query->orWhere('clientName','LIKE','%'.$search.'%');
        }
    })->where(['shiftStatus'=>'0','companyId'=>$companyId])->whereIn('agentid',$myArray)->orderBy($orderbY, $ordertype)->count();

}else{
    
if($request->transferStatus == 'transfer'){
    
    $transStatus = $request->transferStatus;
    
    $listAssign = ListAssignLeads::join('tranasfer_share_leads', 'leadsassigndatabase.leadid', '=', 'tranasfer_share_leads.leadid')->select('leadsassigndatabase.*','tranasfer_share_leads.tagent')->where(['tranasfer_share_leads.tagent'=>$agentid,'leadsassigndatabase.shiftStatus'=>'0','leadsassigndatabase.companyId'=>$companyId])->orderBy($orderbY, $ordertype)->paginate($PerPage);
    
    $listAssignTotalPages = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
    } ,])->whereHas('get_transferleads', function($query) use($request,$transStatus) {
        if($request->transferStatus){
            $query->where('statusType','=',$transStatus);
        }
    })->where(['agentid'=>$agentid,'shiftStatus'=>'0','companyId'=>$companyId])->orderBy($orderbY, $ordertype)->paginate($PerPage)->total();
    
    $listAssignCount = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
    } ,])->whereHas('get_transferleads', function($query) use($request,$transStatus) {
        if($request->transferStatus){
            $query->where('statusType','=',$transStatus);
        }
    })->where(['agentid'=>$agentid,'shiftStatus'=>'0','companyId'=>$companyId])->orderBy($orderbY, $ordertype)->count();
    
}else{
 
    $listAssign = ListAssignLeads::with(['getleads','getvirginleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->whereHas('getleads', function($query) use($request) {
        $query->where('nature','!=','Saeed Sir');
        if($request->q){
            $query->where('clientPhone','LIKE','%'.$request->q.'%');
            $query->orWhere('clientProject','LIKE','%'.$request->q.'%');
            $query->orWhere('clientName','LIKE','%'.$request->q.'%');
            $query->orWhere(['id'=>$request->q]);
        }else if($request->nature){
            $query->where('nature','LIKE','%'.$request->nature.'%');
        }
    })->where(['agentid'=>$agentid,'shiftStatus'=>'0','companyId'=>$companyId])->orderBy($orderbY, $ordertype)->paginate($PerPage);

$listAssignTotalPages = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->whereHas('getleads', function($query) use($request) {
        $query->where('nature','!=','Saeed Sir');
        if($request->q){
            $query->where('clientPhone','LIKE','%'.$request->q.'%');
            $query->orWhere('clientProject','LIKE','%'.$request->q.'%');
            $query->orWhere('clientName','LIKE','%'.$request->q.'%');
            $query->orWhere(['id'=>$request->q]);
        }else if($request->nature){
            $query->where('nature','LIKE','%'.$request->nature.'%');
        }
    })->where(['agentid'=>$agentid,'shiftStatus'=>'0','companyId'=>$companyId])->orderBy($orderbY, $ordertype)->paginate($PerPage)->total();

$listAssignCount = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->whereHas('getleads', function($query) use($request) {
        $query->where('nature','!=','Saeed Sir');
        if($request->q){
            $query->where('clientPhone','LIKE','%'.$request->q.'%');
            $query->orWhere('clientProject','LIKE','%'.$request->q.'%');
            $query->orWhere('clientName','LIKE','%'.$request->q.'%');
            $query->orWhere(['id'=>$request->q]);
        }else if($request->nature){
            $query->where('nature','LIKE','%'.$request->nature.'%');
        }
    })->where(['agentid'=>$agentid,'shiftStatus'=>'0','companyId'=>$companyId])->orderBy($orderbY, $ordertype)->count();
}
 

}


$countListassign = count($listAssign);

$TOtalPerPage = ceil($listAssignTotalPages/$PerPage);



if(count($listAssign) > 0){

$data = $this->AssignrenderList($listAssign);

return response()->json(['code'=>'200','success'=>true,'message'=>'Record Found','data'=>$data,'orderBy'=>$orderbY,'orderType'=>$ordertype,'perPage'=>$PerPage,'pageNo'=>$request->page,'totalRecords'=>$listAssignCount,'TotalPerPage'=>$TOtalPerPage,'search'=>$search]);
}else{
return response()->json(['code'=>'403','error'=>false,'message'=>'Record Not Found','data'=>$listAssign,'orderBy'=>$orderbY,'orderType'=>$ordertype,'perPage'=>'','pageNo'=>'','totalRecords'=>0]);
}

}


public function AssignrenderList(Object $apiResponse)
{
$data = [];
$myxs="";
$iosClientPhone="";
foreach ($apiResponse as $key => $value) {

if($value->get_transferleads == null){
$fAgent="";
}else{
$fAgent = Employee::where(['id'=>$value->get_transferleads->fagent])->first();
}

$queryAgentName = Employee::where(['id'=>$value->agentid])->first();

if($value->getleads == null || $value->getleads == ''){
    $replaceClientPhone = null;
    $replacewhatsappClientPhone = null;
}else{
    
    $haystack = $value->getleads->clientPhone;
    $needle   = '+92';
    
    $needlex   = '0';
    
    if (strpos($haystack, $needle) !== false) {
      
        $replaceWhatsappPhone = str_replace("+92","92",$value->getleads->clientPhone);
        $iosClientPhone = str_replace("+92","92",$value->getleads->clientPhone);
    }else if (strpos($haystack, $needlex) !== false) {
        
        $replaceWhatsappPhone = "+920".ltrim($value->getleads->clientPhone,"92");
        $iosClientPhone= "0".ltrim($value->getleads->clientPhone,"92");
        
    }else{
        $iosClientPhone= "0".ltrim($value->getleads->clientPhone,"92");
        $replaceWhatsappPhone = str_replace("0","92",ltrim($value->getleads->clientPhone,"92"));
    }
    
    $replacePhone = str_replace("+92","0",ltrim($value->getleads->clientPhone,"92"));
    
    $replaceClientPhone = $replacePhone;
    $replacewhatsappClientPhone = $replaceWhatsappPhone;
    
    $myxs = $replaceWhatsappPhone;
}

$timestamp = $value->publishAgentDate;

$timeSince = Carbon::parse($timestamp)->diffForHumans();
// Get the time ago from the timestamp
$timeAgo = Carbon::parse($timestamp)->ago();

array_push($data, (object) [
"id" => $value->id,
"myxs"=>$myxs,
"created_at" => $value->created_at,
"updated_at" => $value->updated_at,
"deleted_at" => $value->deleted_at,
"agentid" => $value->agentid,
"emp_name" => $queryAgentName == null ? '' : $queryAgentName->emp_name,
"leadid" => $value->leadid,
"shiftStatus" => $value->shiftStatus,
"publishAgentDate" => $value->publishAgentDate,
"PublishDate"=>date("d F Y",strtotime($value->publishAgentDate)),
"PublishTime"=>date("H:i",strtotime($value->publishAgentDate)),
// "Ago"=>$value->publishAgentDate->diffForHumans(),
"timeAgo"=>$timeAgo,
"timeSince"=>$timeSince,
"getleads" => $value->getleads,
"clientPhone" => $replaceClientPhone,
"whatsappclientPhone" => $replacewhatsappClientPhone,
"clientPhoneIos" => $iosClientPhone,
"getvirginleads" => $value->getvirginleads,
"getreminder" => $value->getreminder,
// "get_transferleads"=>$value->get_transferleads,
"get_transferleads" => $value->get_transferleads == '' ? null : array(
    "id" => $value->get_transferleads == '' ? '' : $value->get_transferleads->id,
    "leadid" => $value->get_transferleads == '' ? '' : $value->get_transferleads->leadid,
    "fagent" => $value->get_transferleads == '' ? '' : $value->get_transferleads->fagent,
    "fagentName" => $fAgent == '' ? '' : $fAgent->emp_name,
    "tagent" => $value->get_transferleads == '' ? '' : $value->get_transferleads->tagent,
    "transferNote" => $value->get_transferleads == '' ? '' : $value->get_transferleads->transferNote,
    "sharePercentage" => $value->get_transferleads == '' ? '' : $value->get_transferleads->sharePercentage,
    "statusType" => $value->get_transferleads == '' ? '' : $value->get_transferleads->statusType,
    "publishStatus" => $value->get_transferleads == '' ? '' : $value->get_transferleads->publishStatus,
    "created_at" => $value->get_transferleads == '' ? '' : $value->get_transferleads->created_at,
    "updated_at" => $value->get_transferleads == '' ? '' : $value->get_transferleads->updated_at,
 ),
]);
}

return $data;
}


public function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
         'y' => 'year',
         'm' => 'month',
         'w' => 'week',
         'd' => 'day',
         'h' => 'hour',
         'i' => 'minute',
         's' => 'second',
     );
     foreach ($string as $k => &$v) {
         if ($diff->$k) {
             $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
         } else {
             unset($string[$k]);
         }
     }

     if (!$full) $string = array_slice($string, 0, 1);
     return $string ? implode(', ', $string) . ' ago' : 'just now';
 }


public function assignListTestApi(Request $request){

$orderbY = $request->orderby;
$orderType = $request->ordertype;
$perPage = $request->perpage;
$agentid = $request->agentid;

if(is_null($orderbY)){ $orderbY = "id"; }else{
$orderbY = $orderbY;
}

if(is_null($orderType)){ $ordertype = 'desc'; }else{
$ordertype = $orderType;
}

if(is_null($perPage)){

$listAssignCount = ListAssignLeads::with(['getleads','getreminder' => function ($query){
$query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0'])->orderBy($orderbY, $ordertype)->count();

$PerPage = $listAssignCount;

}else{

if($perPage){ $PerPage = $perPage; }else{
$PerPage = 5;
}

}


if(is_null($agentid)){
 $listAssign = ListAssignLeads::with(['getleads','getreminder' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0'])->orderBy($orderbY, $ordertype)->paginate($PerPage);
}else{
$listAssign = ListAssignLeads::with(['getleads','getreminder' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['agentid'=>$agentid,'shiftStatus'=>'0'])->orderBy($orderbY, $ordertype)->paginate($PerPage);
}

$countListassign = count($listAssign);

if(count($listAssign) > 0){
return response()->json(['code'=>'200','success'=>true,'message'=>'Record Found','data'=>$listAssign,'orderBy'=>$orderbY,'orderType'=>$ordertype,'perPage'=>$PerPage,'pageNo'=>$request->paginate,'totalRecords'=>$countListassign]);
}else{
return response()->json(['code'=>'403','error'=>false,'message'=>'Record Not Found','data'=>$listAssign,'orderBy'=>'','orderType'=>'','perPage'=>'','pageNo'=>'','totalRecords'=>0]);
}

}



public function assignVirginList(Request $request){

$orderbY = $request->orderby;
$orderType = $request->ordertype;
$perPage = $request->perpage;
$agentid = $request->agentid;
$leadid = $request->leadid;
$teamObj = $request->teamObj ?? "";

if(is_null($orderbY)){ $orderbY = "id"; }else{
$orderbY = $orderbY;
}

if(is_null($orderType)){ $ordertype = 'desc'; }else{
$ordertype = $orderType;
}

if(is_null($perPage)){

$listAssignCount = ListAssignLeads::whereDoesntHave('LeadImpression')->where(['shiftStatus'=>'0'])->orderBy($orderbY,$ordertype)->count();

$PerPage = $listAssignCount;
$totalRec = $listAssignCount;

}else{

if($perPage){ $PerPage = $perPage; }else{
$PerPage = 20;
}

}

if(is_null($agentid) && empty($teamObj)){

$listAssign = ListAssignLeads::whereDoesntHave('LeadImpression')->with(['getleads','getreminder','getEmployee' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0'])->orderBy($orderbY,$ordertype)->paginate($PerPage);

$listAssignTotalPages = ListAssignLeads::whereDoesntHave('LeadImpression')->where(['shiftStatus'=>'0'])->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();

$listAssignCount = ListAssignLeads::whereDoesntHave('LeadImpression')->where(['shiftStatus'=>'0'])->orderBy($orderbY,$ordertype)->count();

}else if($agentid == 'all' && empty($teamObj)){
if($request->agentS){
$listAssign = ListAssignLeads::whereDoesntHave('LeadImpression')->with(['getleads','getreminder','getEmployee' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0','agentid'=>$request->agentS])->orderBy($orderbY,$ordertype)->paginate($PerPage);

}else{
 $listAssign = ListAssignLeads::whereDoesntHave('LeadImpression')->with(['getleads','getreminder','getEmployee' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0'])->orderBy($orderbY,$ordertype)->paginate($PerPage);

}

$listAssignTotalPages = ListAssignLeads::whereDoesntHave('LeadImpression')->where(['shiftStatus'=>'0'])->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();

$listAssignCount = ListAssignLeads::whereDoesntHave('LeadImpression')->where(['shiftStatus'=>'0'])->orderBy($orderbY,$ordertype)->count();

}else if($agentid == 'all' && !empty($teamObj)){
    
    $myArray2 = explode(',', $teamObj);
$myArray = str_replace(array('[',']'),'',$myArray2);
    
if($request->agentS){
$listAssign = ListAssignLeads::whereDoesntHave('LeadImpression')->with(['getleads','getreminder','getEmployee' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0','agentid'=>$request->agentS])->orderBy($orderbY,$ordertype)->paginate($PerPage);

}else{
 $listAssign = ListAssignLeads::whereDoesntHave('LeadImpression')->with(['getleads','getreminder','getEmployee' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0'])->whereIn('agentid',$myArray)->orderBy($orderbY,$ordertype)->paginate($PerPage);

}

$listAssignTotalPages = ListAssignLeads::whereDoesntHave('LeadImpression')->where(['shiftStatus'=>'0'])->whereIn('agentid',$myArray)->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();

$listAssignCount = ListAssignLeads::whereDoesntHave('LeadImpression')->where(['shiftStatus'=>'0'])->whereIn('agentid',$myArray)->orderBy($orderbY,$ordertype)->count();

}else{

$listAssign = ListAssignLeads::whereDoesntHave('LeadImpression')->with(['getleads','getreminder','getEmployee' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['agentid'=>$agentid,'shiftStatus'=>'0'])->orderBy($orderbY,$ordertype)->paginate($PerPage);

$listAssignTotalPages = ListAssignLeads::whereDoesntHave('LeadImpression')->where(['agentid'=>$agentid,'shiftStatus'=>'0'])->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();

$listAssignCount = ListAssignLeads::whereDoesntHave('LeadImpression')->where(['agentid'=>$agentid,'shiftStatus'=>'0'])->orderBy($orderbY,$ordertype)->count();

}


$countListassign = count($listAssign);



if(count($listAssign) > 0){

$TOtalPerPage = ceil($listAssignTotalPages/$PerPage);

return response()->json(['code'=>'200','success'=>true,'message'=>'Record Found','data'=>$listAssign,'orderBy'=>$orderbY,'orderType'=>$ordertype,'perPage'=>$PerPage,'pageNo'=>$request->paginate,'totalRecords'=>$listAssignCount,'TotalPerPage'=>$TOtalPerPage]);
}else{
return response()->json(['code'=>'403','error'=>false,'message'=>'Record Not Found','data'=>$listAssign,'orderBy'=>'','orderType'=>'','perPage'=>'','pageNo'=>'','totalRecords'=>0]);
}


}

public function assignReminders(Request $req ){

$rules=array(
'agentid'=>'required',
'leadid'=>'required',
'reminderSet'=>'required',
'priority'=>'required',
'reminderinfo'=>'required',
'reminderTypeId'=>'required',
);

$validator = Validator::make($req->all(),$rules);
if($validator->fails()){
return $validator->errors();
}else{
    
    
    
$ReminderListId=0;

if($req->reminderTypeId == 'Other'){
    
    $OtherReminderType = Assignreminderlist::create([
        'typeTitle' =>  $req->otherreminderTypeId,
        'status' =>  1,
    ]);
    
    $lastId = $OtherReminderType->id;
    $ReminderListId = $lastId;
    
}else{
    $ReminderListId = $req->reminderTypeId;
}

$model=new AssignReminder();

$model->agentid = $req->post('agentid');
$model->leadid = $req->post('leadid');
$model->customerId = $req->post('customerId');
$model->reminderSet = str_replace('/','-',$req->post('reminderSet'));
$model->priority = $req->post('priority');
$model->reminderinfo = $req->post('reminderinfo');
$model->reminderTypeId = $ReminderListId;
$data = $model->save();
if($data){
return response()->json(['code'=>'200','success'=>true,'message'=>'Reminder Successfully Set']);
}else{
return response()->json(['code'=>'403','error'=>false,'message'=>'Reminder No Set Please Try Again']);
}

}

}

public function assignCallingImpression(Request $req){

$agentid = $req->get('agentid');
$leadid = $req->get('leadid');
$customerid = $req->get('customerid');
$companyId = $req->get('companyId');
$rules=array(
'agentid'=>'required',
'leadid'=>'required',
);

$validator = Validator::make($req->all(),$rules);
if($validator->fails()){
return $validator->errors();
}else{

$PhoneCount = GetImpressionCount($agentid,$leadid,"phone",$companyId);
$whatsappCount = GetImpressionCount($agentid,$leadid,"whatsapp",$companyId);
$smsCount = GetImpressionCount($agentid,$leadid,"sms",$companyId);
$emailCount = GetImpressionCount($agentid,$leadid,"email",$companyId);

$dataArray = array(
'Phone'=>$PhoneCount,
'whatsapp'=>$whatsappCount,
'sms'=>$smsCount,
'email'=>$emailCount,
);

return response()->json(['code'=>'200','success'=>true,'data'=>$dataArray,'message'=>'success']);

}

}

public function assignNotInterested(Request $req){

$leadid = $req->post('leadid');
$client_opt = $req->post('client_opt');
$OtherNote = $req->post('OtherNote');
$agentid = $req->post('agentid');
$companyId = $req->post('companyId');

$rules=array(
'agentid'=>'required',
'leadid'=>'required',
'client_opt'=>'required',
);

$validator = Validator::make($req->all(),$rules);
if($validator->fails()){
return $validator->errors();
}else{

// $model = ListAssignLeads::where('leadid','=',$leadid)->first();
// $model->shiftStatus = '2';
// $model->update();

    $ReasonQ = new ReasonNotInterested();
    $ReasonQ->leadid = $leadid;
    $ReasonQ->agentid = $agentid;
    $ReasonQ->reasonTitle = $client_opt;
    $ReasonQ->budgetPrice = null;
    $ReasonQ->otherReason = $OtherNote;
    $ReasonQ->companyId = $companyId;
    
    $data = $ReasonQ->save();
    
    if($data){
        
        ListAssignLeads::where(['leadid'=>$leadid])->update([
            'shiftStatus' =>  '2',
        ]);
        
    return response()->json(['code'=>'200','success'=>true,'message'=>'Not Interested Successfully']);
    }else{
    return response()->json(['code'=>'403','error'=>false,'message'=>'Error Please Try Again']);
    }

}

}

public function assignAddLeads(Request $request){

$rules=array(
'agentid'=>'required',
'leadid'=>'required',
'clientName'=>'required',
'clientStatus'=>'required',
'gender'=>'required',
'mobileNo'=>'required',
'country'=>'required',
'city'=>'required',
'projectPrefer'=>'required',
'propertyType'=>'required',
'propertyPrefer'=>'required',
'activityMedium'=>'required',
'interested'=>'required',
'desc'=>'required',
'passCnic'=>'',
'whatsapp'=>'',
'preferredLang'=>'',
'budget'=>'',
'timeFrame'=>'',
'referredBy'=>''

);

$validator = Validator::make($request->all(),$rules);
if($validator->fails()){
return $validator->errors();
}else{

$model=new ListMyLead();

$model->agentid = $request->post('agentid');
$model->leadid = $request->post('leadid');
$model->countryid = $request->post('country');
$model->cityid = $request->post('city');
$model->projectid = $request->post('projectPrefer');
$model->typeid = $request->post('propertyType');
$model->natureid = $request->post('propertyPrefer');
$model->actMediumId = $request->post('activityMedium');
$model->clientName = $request->post('clientName');
$model->clientStatus = $request->post('clientStatus');
$model->Gender = $request->post('gender');
$model->email = $request->post('email');
$model->mobileNo = $request->post('mobileNo');
$model->passportCnic = $request->post('passCnic');
$model->whatsappNo = $request->post('whatsapp');
$model->prefLanguage = $request->post('preferredLang');
$model->Budget = $request->post('budget');
$model->timeFrame = $request->post('timeFrame');
$model->RefferdBy = $request->post('referredBy');
$model->clientInterst = $request->post('interested');
$model->clientActivityDesc = $request->post('desc');
$model->companyId = $request->post('companyId');

$status="My Lead";
// if($request->post('interested') >= 60){
// $status = "hot";
// }else{
// 
// }

// $CheckMylead = ListMyLead::where('leadid','=',$request->post('leadid'))->count();

if($request->post('leadid') > 0){

 $checkASsing = ListMyLead::where('leadid','=',$request->post('leadid'))->count();
 if($checkASsing > 0){
     
     $msg="Lead Id Not Valid Please Enter Valid Id";
     $data="";
     
 }else{
     
     $model->LeadStatus = $status;
$data = $model->save();

$model2 = ListAssignLeads::where('leadid','=',$request->post('leadid'))->first();
$model2->shiftStatus = '1';
$model2->update(); 

$msg="Lead Add Successfully";
     
 }



}else{

$model->LeadStatus = $status;
$data = $model->save();
$msg = "Self Lead Successfully Add";

}

if($data){
return response()->json(['code'=>'200','success'=>true,'message'=>$msg,'lastleadid'=>$model->id]);
}else{
return response()->json(['code'=>'403','error'=>false,'message'=>$msg]);
}

}

}


public function assignClientStatusList(Request $request){


$agentid = $request->get('agentid');

$ClientStatus = ClientStatus::all();
$ClientGender = Gender::all();
$dataCOuntry = Country::all();
$dataActivityTypes = ActivityTypes::all();
$dataLanguage = Language::all();
$dataBudget = Budget::all();
$dataTimeFrame = TimeFrame::all();
$dataInterestPercen = InterestedPercentage::all();
$dataAgentEmp = AgentEmployee::where(['Status'=>'Approved'])->get();

$MaritalStatus = MaritalStatus::all();

$AgeGroup = AgeGroup::all();

$PriorityData =  Priority::all();
$TaskStatus =  TaskStatus::all();
$leadStatus =  leadStatus::all();
$ProjectAdd =  ProjectAdd::all();

if(is_null($agentid)){
$dataAllCustomer = ListMyLead::where(['ReasonStatus'=>'No','DormentStatus'=>'No'])->whereIn('LeadStatus', ['My Lead','hot'])->orderBy('clientName','asc')->get();

$dataAffiliateAllCustomer = AffiliateLeadsModule::all();
}else{
$dataAllCustomer = ListMyLead::where(['ReasonStatus'=>'No','DormentStatus'=>'No','agentid'=>$agentid])->whereIn('LeadStatus', ['My Lead','hot'])->orderBy('clientName','asc')->get();

$dataAffiliateAllCustomer = AffiliateLeadsModule::where(['affiliate_id'=>$agentid])->get();

}

$BlockInve = Projectinventory::select('jsonFormat->block as block')->groupBy('jsonFormat->block')->get();

$CategoryInve = Projectinventory::select('jsonFormat->property_category as property_category')->groupBy('jsonFormat->property_category')->get();


if($ClientStatus){
return response()->json(['code'=>'200','success'=>true,'dataClientStatus'=>$ClientStatus,'dataGender'=>$ClientGender,'dataCountry'=>$dataCOuntry,'dataActivityMedium'=>$dataActivityTypes,'dataLanguage'=>$dataLanguage,'dataBudget'=>$dataBudget,'dataTimeFrame'=>$dataTimeFrame,'dataInterestper'=>$dataInterestPercen,'dataEmployee'=>$dataAgentEmp,'dataCustomer'=>$dataAllCustomer,'ageGroup'=>$AgeGroup,'maritalStatus'=>$MaritalStatus,'PriorityData'=>$PriorityData,'TaskStatus'=>$TaskStatus,'dataAffiliateAllCustomer'=>$dataAffiliateAllCustomer,'BlockInve'=>$BlockInve,'CategoryInve'=>$CategoryInve,'leadStatus'=>$leadStatus,'ProjectAdd'=>$ProjectAdd]);
}else{
return response()->json(['code'=>'403','error'=>false,'data'=>'Data Not Found']);
}

}

public function EmployeeListData(){

$dataAgentEmp = Employee::where(['status'=>'active'])->get();

if($dataAgentEmp){
return response()->json(['code'=>'200','success'=>true,'data'=>$dataAgentEmp]);
}else{
return response()->json(['code'=>'403','error'=>false,'data'=>'Data Not Found']);
}


}

public function assignTransferLeads(Request $req){

$tagent = $req->post('tagentid');
$fagent = $req->post('fagentid');
$transferNote = $req->post('transferNote');
$leadid = $req->post('leadid');
$transferstatus = 'approved';
$statusType = $req->post('statusType');
$companyId = $req->post('companyId');

$rules=array(
'fagentid'=>'required',
'tagentid'=>'required',
'leadid'=>'required',
'transferNote'=>'required',
'statusType'=>'required',
);

$validator = Validator::make($req->all(),$rules);
if($validator->fails()){
return $validator->errors();
}else{

$leadAssign=ListAssignLeads::where(['leadid'=>$leadid,'companyId'=>$companyId])->first();

ListAssignLeads::where(['leadid'=>$leadid,'companyId'=>$companyId])
->update([
'agentid' => $tagent,
'created_at' => date("Y-m-d H:i:s"),
'updated_at' => date("Y-m-d H:i:s"),
'publishAgentDate'=> date('Y-m-d H:i:s')
]);

LeadImportDatabase::where(['id'=>$leadid,'companyId'=>$companyId])
->update([
'created_at' => date("Y-m-d H:i:s"),
'updated_at' => date("Y-m-d H:i:s"),
]);

$tranasferData = new TranasferShareLeads();

$tranasferData->leadid = $leadid;
$tranasferData->fagent = $fagent;
$tranasferData->tagent = $tagent;
$tranasferData->transferNote = $transferNote;
$tranasferData->statusType = $statusType;
$tranasferData->publishStatus = $transferstatus;
$tranasferData->companyId = $companyId;
$data = $tranasferData->save();

if($data){
return response()->json(['code'=>'200','success'=>true,'message'=>'Lead Transfer Successfully']);
}else{
return response()->json(['code'=>'403','error'=>false,'message'=>'Error Please Try Again']);
}

}

}

public function assignLeadImpression(Request $req){

$rules=array(
'agentid'=>'required',
'impType'=>'required',
'leadid'=>'required',
);

$validator = Validator::make($req->all(),$rules);
if($validator->fails()){
return $validator->errors();
}else{

$model=new LeadImpression();

$model->impDate = date("Y-m-d H:i:s");
$model->agentid = $req->post('agentid');
$model->impType = $req->post('impType');
$model->leadid = $req->post('leadid');
$model->customerId = $req->post('customerId');
$model->companyId = $req->post('companyId');

$data = $model->save();

if($data){
return response()->json(['code'=>'200','success'=>true,'message'=>'Lead Impression Successfully!']);
}else{
return response()->json(['code'=>'403','error'=>false,'message'=>'Error Please Try Again']);
}

}

}

public function getCountryApi(){

$data = Country::all();
if($data){
return response()->json(['code'=>'200','success'=>true,'message'=>'successfully','data'=>$data]);
}else{
return response()->json(['code'=>'403','error'=>false,'message'=>'Error Please Try Again']);
}

}


public function getProjectsListApi(){

$data = LeadImportDatabase::groupBy('clientProject')
->selectRaw('count(*) as total, clientProject')
->get();
if($data){
return response()->json(['code'=>'200','success'=>true,'message'=>'successfully','data'=>$data]);
}else{
return response()->json(['code'=>'403','error'=>false,'message'=>'Error Please Try Again']);
}

}

public function getCountryCityWiseApi(Request $request){

$countryid = $request->get('countryid');
if($countryid == ''){
return response()->json(['code'=>'403','error'=>false,'message'=>'Select Country First']);
}else{

$data = City::WHERE(['countryid'=>$countryid])->get();
if(count($data) > 0){
return response()->json(['code'=>'200','success'=>true,'message'=>'data fetch','data'=>$data]);
}else{
return response()->json(['code'=>'403','error'=>false,'message'=>'data not fetch']);
}

}
}


public function getCityProjectsWiseApi(Request $request){

$cityid = $request->get('cityid');
if($cityid == ''){
return response()->json(['code'=>'403','error'=>false,'message'=>'Select City First']);
}else{

$data = ProjectAdd::WHERE(['cityid'=>$cityid])->get();
if(count($data) > 0){
return response()->json(['code'=>'200','success'=>true,'message'=>'data fetch','data'=>$data]);
}else{
return response()->json(['code'=>'403','error'=>false,'message'=>'data not fetch']);
}

}
}

public function getProtypeProjectsWiseApi(Request $request){

$proid = $request->get('proid');
if($proid == ''){
return response()->json(['code'=>'403','error'=>false,'message'=>'Select Project First']);
}else{

$data = ProjectAdd::WHERE(['id'=>$proid])->get();
$pid = explode(',',$data[0]->protype);
$edata = apartmentType::whereIn('id',$pid)->get();
if(count($edata) > 0){
return response()->json(['code'=>'200','success'=>true,'message'=>'data fetch','data'=>$edata]);
}else{
return response()->json(['code'=>'403','error'=>false,'message'=>'data not fetch']);
}

}
}

public function getNatureTypeWiseApi(Request $request){

$typeid = $request->get('typeid');
if($typeid == ''){
return response()->json(['code'=>'403','error'=>false,'message'=>'Select Property Type First']);
}else{

// $data = ProjectAdd::WHERE(['id'=>$typeid])->get();
// $pid = explode(',',$data[0]->pronature);
$edata = natureProject::where(['typeid'=>$typeid])->get();
if(count($edata) > 0){
return response()->json(['code'=>'200','success'=>true,'message'=>'data fetch','data'=>$edata]);
}else{
return response()->json(['code'=>'403','error'=>false,'message'=>'data not fetch']);
}

}
}


public function NotInterestedReasonOpt(){

$data = Notinterestedopt::all();
if(count($data) > 0){
return response()->json(['code'=>'200','success'=>true,'message'=>'data fetch','data'=>$data]);
}else{
return response()->json(['code'=>'403','error'=>false,'message'=>'data not fetch']);
}
}


public function TaskStatusChange(Request $request){

$rules=array(
'task_id_pk'=>'required',
'status'=>'required',
'status_changed_by'=>'required',
);

$validator = Validator::make($request->all(),$rules);
if($validator->fails()){
return $validator->errors();
}else{

$model = TaskHistory::where('task_id_pk','=',$request->post('task_id_pk'))->first();
$model->task_id_pk = $request->post('task_id_pk');
$model->status = $request->post('status');
$model->status_changed_by = $request->post('status_changed_by');
$model->status_date_time = date("Y-m-d H:i:s");
$data = $model->update();

if($data){
    return response()->json(['code'=>'200','success'=>true,'message'=>'Task Successfully Updated','data'=>$model]);
}else{
    return response()->json(['code'=>'403','error'=>false,'message'=>'Error Please Try Again']);
}

}

}


public function TaskAssignedChange(Request $request){

$rules=array(
'assigned_by'=>'required',
'task_id_pk'=>'required',
'assigned_to'=>'required',
);

$validator = Validator::make($request->all(),$rules);
if($validator->fails()){
return $validator->errors();
}else{

$fetchData = TaskHistory::where('task_id_pk','=',$request->post('task_id_pk'))->first();

$model=new TaskHistory();

$model->task_id_pk = $fetchData->task_id_pk;
$model->status = $fetchData->status;
$model->status_changed_by = $fetchData->status_changed_by;
$model->status_date_time = $fetchData->status_date_time;
$model->assigned_by = $request->post('assigned_by');
$model->assigned_to = $request->post('assigned_to');
$model->assigned_date_time = date("Y-m-d H:i:s");
$model->priority = $fetchData->priority;
$model->priority_changed_by = $fetchData->priority_changed_by;
$model->priority_date_time = $fetchData->priority_date_time;
$model->due_date = $fetchData->due_date;

$data = $model->save();


Task::where('id', $request->post('task_id_pk'))
->update([
'agentId' => $request->post('assigned_to'),
]);


if($data){
return response()->json(['code'=>'200','success'=>true,'message'=>'Task Assign Successfully','data'=>$fetchData,'taskId'=>$request->post('task_id_pk'),'empId'=>$request->post('assigned_to')]);
}else{
return response()->json(['code'=>'403','error'=>false,'message'=>'Error Please Try Again']);
}

}

}



public function TaskPriorityChange(Request $request){

$rules=array(
'task_id_pk'=>'required',
'priority'=>'required',
'priority_changed_by'=>'required',
);

$validator = Validator::make($request->all(),$rules);
if($validator->fails()){
return $validator->errors();
}else{

$model = TaskHistory::where('task_id_pk','=',$request->post('task_id_pk'))->first();
$model->task_id_pk = $request->post('task_id_pk');
$model->priority = $request->post('priority');
$model->priority_changed_by = $request->post('priority_changed_by');
$model->priority_date_time = date("Y-m-d H:i:s");
$data = $model->update();

if($data){
    return response()->json(['code'=>'200','success'=>true,'message'=>'Task Successfully Updated','data'=>$model]);
}else{
    return response()->json(['code'=>'403','error'=>false,'message'=>'Error Please Try Again']);
}

}

}


public function getRolePermission(Request $request){

$orderBy = $request->orderBy ?? "id";
$orderType = $request->orderType ?? "desc";
$perPage = $request->perPage ?? "20";
$page = $request->page ?? "1";

$agentId = $request->agentId;

if (!is_null($agentId)) {

$response = Employee::where(['id'=>$agentId])->orderBy($orderBy, $orderType)->get();
// SUCCESS
if (count($response) > 0) {
$data = $this->renderList($response);

return response()->json([
    'code' => '200',
    'success' => true,
    'message' => 'Employee Found',
    'data' => $data,
    
]);
}

// FAIL
else {
return app(CommonController::class)->getEmptyListResponse();
}
}

// AGENT ID NOT SELECTED
else {
return app(CommonController::class)->getExceptionResponse("Agent Id is required");
}

}

public function renderList(Object $apiResponse)
{
$data = [];

foreach ($apiResponse as $key => $value) {

$EmpRoleData = EmployeeRole::with(['getRole','getFeature' => function ($query){
$query->orderBy('id', 'desc')->get();
} ,])->where(['emp_id'=>$value->id])->get();

$dataitem=[];
$datamoduleitem=[];

foreach($EmpRoleData as $moduleItem){

$roleName = $moduleItem->getRole == null ? '' : $moduleItem->getRole->role_category_name;

$moduleQuery = CrmModule::where(['id'=>$moduleItem->getFeature->module_id])->first();

$moduleName = $moduleItem->getFeature == null ? '' : $moduleQuery->module_name;

$moduleId = $moduleItem->getFeature == null ? '' : $moduleQuery->id;

$roleId = $moduleItem->getRole == null ? '' : $moduleItem->getRole->id;

$featureName = $moduleItem->getFeature == null ? '' : $moduleItem->getFeature->feature_name;
$featureId = $moduleItem->getFeature == null ? '' : $moduleItem->getFeature->id;

// $featureData = CrmModuleFeatures::where(['id'=>$moduleItem->feature_id])->first();
// $moduleData = CrmModule::where(['id'=>$featureData->module_id])->first();
// $datamoduleitem[]=$moduleData;
 array_push($dataitem, (object) [
  'role_name'=>$roleName,
  'module_name'=>$moduleName,
  'module_id'=>$moduleId,
  'role_id'=>$roleId,
  'features'=>[
     'feature_name' =>$featureName,
     'feature_id' =>$featureId,
   ],
   'sub_features'=>$moduleItem->permission_type
]);
}



array_push($data, (object) [
"id" => $value->id,
"created_at" => $value->created_at,
"updated_at" => $value->updated_at,
"deleted_at" => $value->deleted_at,
"emp_name" => $value->emp_name,
"email" => $value->email,
"role"=>$dataitem,
]);
}

return $data;
}


public function getZongCallingData(Request $request){


$orderBy = $request->orderBy ?? "id";
$orderType = $request->orderType ?? "desc";
$perPage = $request->perPage ?? "12";
$page = $request->page ?? "1";
$type = $request->Type;
$Source = $request->Source;
$CommentStatus = $request->CommentStatus;
$Consultant = $request->Consultant;
$Project = $request->Project;
$ext = $request->ext;
$search = $request->q;
$agentId = $request->agentId ?? "";
$SearchDate = $request->SearchDate ?? "";
$empTeam = $request->empTeam ?? "";
$StartDates = $request->StartDates ?? "";
$EndDate = $request->EndDate ?? "";
$Status = $request->Status ?? "";
$company_id = $request->company_id ?? "";

if(empty($ext) && empty($search) && empty($type) && empty($Source) && empty($CommentStatus) && empty($Consultant) && empty($Project) && empty($SearchDate) && empty($empTeam) && empty($StartDates) && empty($EndDate) && empty($Status)){

$myEmpDataIm =  Employee::where(['status'=>'active','company_id'=>$company_id])->where('incommingPhone','!=','')->where('outgoingPhone','!=','')->pluck('outgoingPhone')->join(',');
// $expEmp = explode(",",$myEmpDataIm);
// $myEmpDataIxsm = implode("','",$expEmp);

$teamObject = explode(',', $myEmpDataIm);
$myEmpDataIxsm = str_replace(array('[',']'),'',$teamObject);

$response = Zongcalling::with(['getCallingDetails' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->whereIn('status',['Answered','Missed','Connected'])->whereIn('ext',$myEmpDataIxsm)->orderBy($orderBy, $orderType)->paginate($perPage);
 $count = Zongcalling::whereIn('status',['Answered','Missed','Connected'])->whereIn('ext',$myEmpDataIxsm)->count();
    
}else if(empty($ext) && empty($search) && !empty($type) && empty($Source) && empty($CommentStatus) && empty($Consultant) && empty($Project) && empty($SearchDate) && empty($empTeam) && empty($StartDates) && empty($EndDate) && empty($Status)){
    
    $response = Zongcalling::with(['getCallingDetails' => function ($query){
    $query->orderBy('id', 'desc')->get();
    } ,])->where(['Type'=>$type])->whereIn('status',['Answered','Missed','Connected'])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Zongcalling::where(['Type'=>$type])->whereIn('status',['Answered','Missed','Connected'])->count();
    
}else if(empty($ext) && empty($search) && empty($type) && !empty($Source) && empty($CommentStatus) && empty($Consultant) && empty($Project) && empty($SearchDate) && empty($empTeam) && empty($StartDates) && empty($EndDate) && empty($Status)){
    
    $response = Zongcalling::with(['getCallingDetails' => function ($query){
    $query->orderBy('id', 'desc')->get();
    } ,])->where(['Source'=>$Source])->whereIn('status',['Answered','Missed','Connected'])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Zongcalling::where(['Source'=>$Source])->whereIn('status',['Answered','Missed','Connected'])->count();
    
}else if(empty($ext) && empty($search) && empty($type) && empty($Source) && !empty($CommentStatus) && empty($Consultant) && empty($Project) && empty($SearchDate) && empty($empTeam) && empty($StartDates) && empty($EndDate) && empty($Status)){
    
    $response = Zongcalling::with(['getCallingDetails' => function ($query){
    $query->orderBy('id', 'desc')->get();
    } ,])->where(['CommentStatus'=>$CommentStatus])->whereIn('status',['Answered','Missed','Connected'])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Zongcalling::where(['CommentStatus'=>$CommentStatus])->whereIn('status',['Answered','Missed','Connected'])->count();
    
}else if(empty($ext) && $ext !='m2' && empty($search) && empty($type) && empty($Source) && empty($CommentStatus) && !empty($Consultant) && empty($Project) && empty($SearchDate) && empty($empTeam) && empty($StartDates) && empty($EndDate) && empty($Status)){
    
    $empData = Employee::where(['id'=>$Consultant])->first();
    $implodeCon = array($empData->incommingPhone,$empData->outgoingPhone);
    
    $response = Zongcalling::with(['getCallingDetails' => function ($query) use($Consultant){
        $query->orderBy('id', 'desc')->get();
    } ,])->whereIn('ext',$implodeCon)->whereIn('status',['Answered','Missed','Connected'])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Zongcalling::whereIn('ext',$implodeCon)->whereIn('status',['Answered','Missed','Connected'])->count();
    
}else if(!empty($ext) && $ext !='m2' && empty($search) && empty($type) && empty($Source) && empty($CommentStatus) && empty($Consultant) && empty($Project) && empty($SearchDate) && empty($empTeam) && empty($StartDates) && empty($EndDate) && empty($Status)){
    
    $empData = Employee::where(['outgoingPhone'=>$ext])->first();
    $implodeCon = array($empData->incommingPhone,$empData->outgoingPhone);
    
    $response = Zongcalling::with(['getCallingDetails' => function ($query) use($Consultant){
        $query->orderBy('id', 'desc')->get();
    } ,])->whereIn('ext',$implodeCon)->whereIn('status',['Answered','Missed','Connected'])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Zongcalling::whereIn('ext',$implodeCon)->whereIn('status',['Answered','Missed','Connected'])->count();
    
}else if(empty($ext) && empty($search) && empty($type) && empty($Source) && empty($CommentStatus) && empty($Consultant) && empty($Project) && empty($SearchDate) && $empTeam !='' && empty($StartDates) && empty($EndDate) && empty($Status)){
    
    $empTeams = str_replace("[]"," ",$empTeam);
    
    $empteamA = ltrim($empTeam,"[");
    $empTeamB = rtrim($empteamA,"]");
    
   
   
     $myArray = explode(',', $empTeamB);
     
      $Employeeasd = Employee::whereIn('id',$myArray)->get();
    $incomming = [];
    $outgoing = [];
    
    foreach($Employeeasd as $intes){
        if($intes->incommingPhone == '' || $intes->incommingPhone == null){
        }else{
            $incomming[]=$intes->incommingPhone;
        }
        if($intes->outgoingPhone == '' || $intes->outgoingPhone == null){
        }else{
            $outgoing[]=$intes->outgoingPhone;
        }
        
    }
    
    $mergerInOut = Arr::collapse([$incomming,$outgoing]);
    
    $response = Zongcalling::with(['getCallingDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
    } ,])->whereIn('ext',$mergerInOut)->whereIn('status',['Answered','Missed','Connected'])->orderBy($orderBy, $orderType)->paginate($perPage);
     $count = Zongcalling::whereIn('ext',$mergerInOut)->whereIn('status',['Answered','Missed','Connected'])->count();
    
}else if(empty($ext) && empty($search) && empty($type) && empty($Source) && empty($CommentStatus) && !empty($Consultant) && empty($Project) && empty($SearchDate) && $empTeam !='' && empty($StartDates) && empty($EndDate) && empty($Status)){
    
    $empData = Employee::where(['id'=>$Consultant])->first();
    $implodeCon = array($empData->incommingPhone,$empData->outgoingPhone);
    
    $response = Zongcalling::with(['getCallingDetails' => function ($query) use($Consultant){
        $query->orderBy('id', 'desc')->get();
    } ,])->whereIn('ext',$implodeCon)->whereIn('status',['Answered','Missed','Connected'])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Zongcalling::whereIn('ext',$implodeCon)->whereIn('status',['Answered','Missed','Connected'])->count();
    
}else if(empty($ext) && empty($search) && empty($type) && empty($Source) && empty($CommentStatus) && !empty($Consultant) && empty($Project) && !empty($SearchDate) && $empTeam !='' && empty($StartDates) && empty($EndDate) && empty($Status)){
    
    $empData = Employee::where(['id'=>$Consultant])->first();
    $implodeCon = array($empData->incommingPhone,$empData->outgoingPhone);
    
    $response = Zongcalling::with(['getCallingDetails' => function ($query) use($Consultant){
        $query->orderBy('id', 'desc')->get();
    } ,])->whereIn('ext',$implodeCon)->whereIn('status',['Answered','Missed','Connected'])->where(DB::raw('DATE(datetime)'), $SearchDate)->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Zongcalling::whereIn('ext',$implodeCon)->whereIn('status',['Answered','Missed','Connected'])->where(DB::raw('DATE(datetime)'), $SearchDate)->count();
    
}else if(!empty($ext) && !empty($search) && empty($type) && empty($Source) && empty($CommentStatus) && empty($Consultant) && empty($Project) && empty($SearchDate) && empty($empTeam) && empty($StartDates) && empty($EndDate) && empty($Status)){
    
        $empData = Employee::where(['outgoingPhone'=>$ext])->first();
        $implodeCon = array($empData->outgoingPhone,$empData->incommingPhone);
    
    $response = Zongcalling::with(['getCallingDetails' => function ($query){
    $query->orderBy('id', 'desc')->get();
    } ,])->whereIn('ext',$implodeCon)->whereIn('status',['Answered','Missed','Connected'])->where('phone','LIKE','%'.$search.'%')->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Zongcalling::whereIn('ext',$implodeCon)->whereIn('status',['Answered','Missed','Connected'])->where('phone','LIKE','%'.$search.'%')->count();
    
}else if(!empty($ext) && empty($search) && !empty($type) && empty($Source) && empty($CommentStatus) && empty($Consultant) && empty($Project) && empty($SearchDate) && empty($empTeam) && empty($StartDates) && empty($EndDate) && empty($Status)){
    
        $empData = Employee::where(['outgoingPhone'=>$ext])->first();
        $implodeCon = array($empData->outgoingPhone,$empData->incommingPhone);
    
    $response = Zongcalling::with(['getCallingDetails' => function ($query){
    $query->orderBy('id', 'desc')->get();
    } ,])->whereIn('ext',$implodeCon)->whereIn('status',['Answered','Missed','Connected'])->where(['Type'=>$type])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Zongcalling::whereIn('ext',$implodeCon)->whereIn('status',['Answered','Missed','Connected'])->where(['Type'=>$type])->count();
    
}else if(empty($ext) && empty($search) && empty($type) && empty($Source) && empty($CommentStatus) && empty($Consultant) && empty($Project) && !empty($SearchDate) && empty($empTeam) && empty($StartDates) && empty($EndDate) && empty($Status)){
    
    
    $response = Zongcalling::with(['getCallingDetails' => function ($query){
    $query->orderBy('id', 'desc')->get();
    } ,])->where(DB::raw('DATE(datetime)'), $SearchDate)->whereIn('status',['Answered','Missed','Connected'])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Zongcalling::where(DB::raw('DATE(datetime)'), $SearchDate)->whereIn('status',['Answered','Missed','Connected'])->count();
    
}else if(empty($ext) && empty($search) && empty($type) && empty($Source) && empty($CommentStatus) && !empty($Consultant) && empty($Project) && !empty($SearchDate) && empty($empTeam) && empty($StartDates) && empty($EndDate) && empty($Status)){
   
    $empData = Employee::where(['id'=>$Consultant])->first();
    $implodeCon = array($empData->incommingPhone,$empData->outgoingPhone);
    
    $response = Zongcalling::with(['getCallingDetails' => function ($query){
    $query->orderBy('id', 'desc')->get();
    } ,])->whereIn('ext',$implodeCon)->whereIn('status',['Answered','Missed','Connected'])->where(DB::raw('DATE(datetime)'), $SearchDate)->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Zongcalling::whereIn('ext',$implodeCon)->whereIn('status',['Answered','Missed','Connected'])->where(DB::raw('DATE(datetime)'), $SearchDate)->count();
    
}else if(empty($ext) && empty($search) && !empty($type) && empty($Source) && empty($CommentStatus) && !empty($Consultant) && empty($Project) && !empty($SearchDate) && empty($empTeam) && empty($StartDates) && empty($EndDate) && empty($Status)){
    
    $empData = Employee::where(['id'=>$Consultant])->first();
    $implodeCon = array($empData->incommingPhone,$empData->outgoingPhone);
    
    $response = Zongcalling::with(['getCallingDetails' => function ($query){
    $query->orderBy('id', 'desc')->get();
    } ,])->whereIn('ext',$implodeCon)->whereIn('status',['Answered','Missed','Connected'])->where(DB::raw('DATE(datetime)'), $SearchDate)->where(['Type'=>$type])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Zongcalling::whereIn('ext',$implodeCon)->whereIn('status',['Answered','Missed','Connected'])->where(DB::raw('DATE(datetime)'), $SearchDate)->where(['Type'=>$type])->count();
    
}else if(empty($ext) && !empty($search) && empty($type) && empty($Source) && empty($CommentStatus) && empty($Consultant) && empty($Project) && empty($SearchDate) && empty($empTeam) && empty($StartDates) && empty($EndDate) && empty($Status)){

        $empData = Employee::where(['outgoingPhone'=>$ext])->first();
        $implodeCon = array($empData->outgoingPhone,$empData->incommingPhone);
    
    $response = Zongcalling::with(['getCallingDetails' => function ($query){
    $query->orderBy('id', 'desc')->get();
    } ,])->where('phone','LIKE','%'.$search.'%')->whereIn('status',['Answered','Missed','Connected'])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Zongcalling::where('phone','LIKE','%'.$search.'%')->whereIn('status',['Answered','Missed','Connected'])->count();
    
}else if(empty($ext) && empty($search) && !empty($type) && empty($Source) && empty($CommentStatus) && !empty($Consultant) && empty($Project) && empty($SearchDate) && empty($empTeam) && empty($StartDates) && empty($EndDate) && empty($Status)){
    $empData = Employee::where(['id'=>$Consultant])->first();
    $implodeCon = array($empData->incommingPhone,$empData->outgoingPhone);
    
    $response = Zongcalling::with(['getCallingDetails' => function ($query) use($Consultant){
        $query->orderBy('id', 'desc')->get();
    } ,])->whereIn('ext',$implodeCon)->whereIn('status',['Answered','Missed','Connected'])->where(['Type'=>$type])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Zongcalling::whereIn('ext',$implodeCon)->whereIn('status',['Answered','Missed','Connected'])->where(['Type'=>$type])->count();
    
}else if(empty($ext) && empty($search) && empty($type) && empty($Source) && empty($CommentStatus) && empty($Consultant) && empty($Project) && empty($SearchDate) && empty($empTeam) && !empty($StartDates) && !empty($EndDate) && empty($Status)){
    
    
    $response = Zongcalling::with(['getCallingDetails' => function ($query) use($Consultant){
        $query->orderBy('id', 'desc')->get();
    } ,])->whereIn('status',['Answered','Missed','Connected'])->whereBetween(DB::raw('DATE(datetime)'),[$StartDates,$EndDate])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Zongcalling::whereBetween(DB::raw('DATE(datetime)'),[$StartDates,$EndDate])->whereIn('status',['Answered','Missed','Connected'])->count();
    
}else if(empty($ext) && empty($search) && empty($type) && empty($Source) && empty($CommentStatus) && empty($Consultant) && empty($Project) && empty($SearchDate) && empty($empTeam) && empty($StartDates) && empty($EndDate) && !empty($Status)){
    
    
    $response = Zongcalling::with(['getCallingDetails' => function ($query) use($Consultant){
        $query->orderBy('id', 'desc')->get();
    } ,])->where(['status'=>$Status])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Zongcalling::where(['status'=>$Status])->count();
    
}else if(!empty($ext) && empty($search) && empty($type) && empty($Source) && empty($CommentStatus) && empty($Consultant) && empty($Project) && empty($SearchDate) && empty($empTeam) && empty($StartDates) && empty($EndDate) && !empty($Status)){
    
        $empData = Employee::where(['outgoingPhone'=>$ext])->first();
        $implodeCon = array($empData->outgoingPhone,$empData->incommingPhone);
    
    $response = Zongcalling::with(['getCallingDetails' => function ($query){
    $query->orderBy('id', 'desc')->get();
    } ,])->whereIn('ext',$implodeCon)->where(['status'=>$Status])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Zongcalling::whereIn('ext',$implodeCon)->where(['status'=>$Status])->count();
    
}else if(!empty($ext) && empty($search) && empty($type) && empty($Source) && empty($CommentStatus) && empty($Consultant) && empty($Project) && empty($SearchDate) && empty($empTeam) && !empty($StartDates) && !empty($EndDate) && !empty($Status)){
    
        $empData = Employee::where(['outgoingPhone'=>$ext])->first();
        $implodeCon = array($empData->outgoingPhone,$empData->incommingPhone);
    
    $response = Zongcalling::with(['getCallingDetails' => function ($query){
    $query->orderBy('id', 'desc')->get();
    } ,])->whereIn('ext',$implodeCon)->where(['status'=>$Status])->whereBetween(DB::raw('DATE(datetime)'),[$StartDates,$EndDate])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Zongcalling::whereIn('ext',$implodeCon)->where(['status'=>$Status])->whereBetween(DB::raw('DATE(datetime)'),[$StartDates,$EndDate])->count();
    
}else if(empty($ext) && empty($search) && empty($type) && empty($Source) && empty($CommentStatus) && !empty($Consultant) && empty($Project) && empty($SearchDate) && empty($empTeam) && empty($StartDates) && empty($EndDate) && !empty($Status)){
    $empData = Employee::where(['id'=>$Consultant])->first();
    $implodeCon = array($empData->incommingPhone,$empData->outgoingPhone);
    
    $response = Zongcalling::with(['getCallingDetails' => function ($query) use($Consultant){
        $query->orderBy('id', 'desc')->get();
    } ,])->whereIn('ext',$implodeCon)->where(['status'=>$Status])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Zongcalling::whereIn('ext',$implodeCon)->where(['status'=>$Status])->count();
    
}else if(empty($ext) && empty($search) && empty($type) && empty($Source) && empty($CommentStatus) && !empty($Consultant) && empty($Project) && empty($SearchDate) && empty($empTeam) && !empty($StartDates) && !empty($EndDate) && !empty($Status)){
    $empData = Employee::where(['id'=>$Consultant])->first();
    $implodeCon = array($empData->incommingPhone,$empData->outgoingPhone);
    
    $response = Zongcalling::with(['getCallingDetails' => function ($query) use($Consultant){
        $query->orderBy('id', 'desc')->get();
    } ,])->whereIn('ext',$implodeCon)->whereBetween(DB::raw('DATE(datetime)'),[$StartDates,$EndDate])->where(['status'=>$Status])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Zongcalling::whereIn('ext',$implodeCon)->whereBetween(DB::raw('DATE(datetime)'),[$StartDates,$EndDate])->where(['status'=>$Status])->count();
    
}else if(empty($ext) && empty($search) && !empty($type) && empty($Source) && empty($CommentStatus) && !empty($Consultant) && empty($Project) && empty($SearchDate) && empty($empTeam) && !empty($StartDates) && !empty($EndDate) && !empty($Status)){
    $empData = Employee::where(['id'=>$Consultant])->first();
    $implodeCon = array($empData->incommingPhone,$empData->outgoingPhone);
    
    $response = Zongcalling::with(['getCallingDetails' => function ($query) use($Consultant){
        $query->orderBy('id', 'desc')->get();
    } ,])->whereIn('ext',$implodeCon)->where(['Type'=>$type])->whereBetween(DB::raw('DATE(datetime)'),[$StartDates,$EndDate])->where(['status'=>$Status])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Zongcalling::whereIn('ext',$implodeCon)->where(['Type'=>$type])->whereBetween(DB::raw('DATE(datetime)'),[$StartDates,$EndDate])->where(['status'=>$Status])->count();
    
}else if(empty($ext) && empty($search) && !empty($type) && empty($Source) && empty($CommentStatus) && empty($Consultant) && empty($Project) && empty($SearchDate) && empty($empTeam) && !empty($StartDates) && !empty($EndDate) && !empty($Status)){
 
    $response = Zongcalling::with(['getCallingDetails' => function ($query) use($Consultant){
        $query->orderBy('id', 'desc')->get();
    } ,])->whereBetween(DB::raw('DATE(datetime)'),[$StartDates,$EndDate])->where(['status'=>$Status,'Type'=>$type])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Zongcalling::whereBetween(DB::raw('DATE(datetime)'),[$StartDates,$EndDate])->where(['status'=>$Status,'Type'=>$type])->count();
    
}else if(empty($ext) && empty($search) && !empty($type) && empty($Source) && empty($CommentStatus) && empty($Consultant) && empty($Project) && empty($SearchDate) && empty($empTeam) && empty($StartDates) && empty($EndDate) && !empty($Status)){
 
    $response = Zongcalling::with(['getCallingDetails' => function ($query) use($Consultant){
        $query->orderBy('id', 'desc')->get();
    } ,])->where(['status'=>$Status,'Type'=>$type])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Zongcalling::where(['status'=>$Status,'Type'=>$type])->count();
    
}else{
    
    
    
    return response()->json([
        'code' => '201',
        'success' => true,
        'message' => 'Calling Not Found',
        'data' => '',
        'orderBy' => $orderBy,
        'orderType' => $orderType,
        'perPage' => $perPage,
        'pageNo' => $page,
        'TotalCount'=>0,
    ]);

}


if (count($response) > 0) {
$data = $this->callingrenderList($response);

return response()->json([
    'code' => '200',
    'success' => true,
    'message' => 'Calling Found',
    'data' => $data,
    'orderBy' => $orderBy,
    'orderType' => $orderType,
    'perPage' => $perPage,
    'pageNo' => $page,
    'TotalCount'=>$count,
]);
}

// FAIL
else {
return app(CommonController::class)->getEmptyListResponse();
}



return response()->json(['code'=>'403','error'=>false,'message'=>'Record Found','data'=>$data]);

}

public function callingrenderList(Object $apiResponse)
{
$data = [];

foreach ($apiResponse as $key => $value) {
    
if($value->Type == 'Incomming Calls'){
    $empData = Employee::where(['incommingPhone'=>$value->ext])->first();
}else{
    $empData = Employee::where(['outgoingPhone'=>$value->ext])->first();
}

$PhoneMatch = $value->phone;

$str = ltrim($value->phone, '770');

$importData = LeadImportDatabase::where(['clientPhone'=>$str])->first();
$customerDx = ListMyLead::where('mobileNo','LIKE','%'.$str.'%')->first();

$statActive = "";

if($value->status == 'Missed' || $value->status == 'Connected' && $value->phone  == $str){
    $statusActive="active";
}else{
    $statusActive="";
}

$ProjectAdd = $customerDx == null || $customerDx == '' ? '' : ProjectAdd::where(['id'=>$customerDx->projectid])->first();
$ActivityTypes = $customerDx == null || $customerDx == '' ? '' : ActivityTypes::where(['id'=>$customerDx->actMediumId])->first();

// $exp = explode(".",$value->recording);
$ext = pathinfo($value->recording, PATHINFO_EXTENSION);

array_push($data, (object) [
"id" => $value->id,
"created_at" => $value->created_at,
"updated_at" => $value->updated_at,
"deleted_at" => $value->deleted_at,
"phone" => $value->phone,
"str"=>$str,
"ext" => $value->ext,
"ActivityTypes"=>$ActivityTypes,
"emp_name" => $empData == null || $empData == '' ? '' : $empData->emp_name,
"duration" => $value->duration,
"recording" => $value->recording,
"status" => $value->status,
"ClientName" => $value->ClientName,
"projectName" => $value->projectName == null || $value->projectName == '' ? @$ProjectAdd->title : $value->projectName,
"activityName"=> $ActivityTypes == null || $ActivityTypes == '' ? '' : $ActivityTypes->activityName,
"CallBack" => $value->CallBack,
"Source" => $value->Source,
"Quality" => $value->Quality,
"Comment" => $value->Comment,
"CommentStatus" => $value->CommentStatus,
"ClientStatus" => $value->ClientStatus,
"datetime" => $value->datetime,
"Type" => $value->Type,
"getCallingDetails"=>$importData == '' ? '' : $importData,
"customerDx"=>$customerDx,
'statusActive'=>$statusActive,
"exp"=>$ext
]);
}

return $data;
}


public function facebookchatFunc (Request $request){

$rules=array(
'ClientDesignation'=>'required',
'ClientProject'=>'required',
'ClientEmail'=>'required',
'clientPhone'=>'required',
'ClientName'=>'required',
'LeadTypes'=>'required',
);

$validator = Validator::make($request->all(),$rules);
if($validator->fails()){
return $validator->errors();
}else{

$importDataCheck = LeadImportDatabase::where(['clientPhone'=>$request->post('clientPhone')])->count();

// return response()->json(['code'=>'203','error'=>false,'message'=>'Record Inserted Successfully','data'=>$importDataCheck]);
if($importDataCheck > 0){
 return response()->json(['code'=>'203','error'=>false,'message'=>'Client No Already Registered','data'=>'Client No Already Registered']);
}else{
 
$modelImport=new LeadImportDatabase();

$modelImport->clientName = $request->post('ClientName');
$modelImport->companyId = $request->post('company_id');
$modelImport->clientPhone = $request->post('clientPhone');
$modelImport->clientemail = $request->post('ClientEmail');
$modelImport->clientProject = $request->post('ClientProject');
$modelImport->clientDesignation = $request->post('ClientDesignation');
// $modelImport->nature = $request->post('LeadTypes') == 'facebook-chat' ? 'Facebook Chat Fresh' : 'Youtube';
$modelImport->nature = $request->post('LeadTypes');
$modelImport->leadStatus = 'active';
$modelImport->leadType = 'unique';
$modelImport->created_at = date("Y-m-d H:i:s");

$data = $modelImport->save();

if($data){
    
    $insertId = $modelImport->id;
    
    $modelAssignIns=new ListAssignLeads();
    
    $modelAssignIns->agentid = $request->post('company_id') == 36 ? '3912' : '38';
    $modelAssignIns->companyId = $request->post('company_id');
    $modelAssignIns->leadid = $insertId;
    $modelAssignIns->shiftStatus = '0';
    $modelAssignIns->publishAgentDate = date("Y-m-d H:i:s");
    $modelAssignIns->created_at = date("Y-m-d H:i:s");
    
    $modelAssignIns->save();
    
     return response()->json(['code'=>'200','error'=>false,'message'=>'Record Inserted Successfully','data'=>'Record Inserted Successfully']);

}

 
}


}

}


public function ZongDataUpdate (Request $request){

$rules=array(
'items'=>'required',
);

$validator = Validator::make($request->all(),$rules);
if($validator->fails()){
return $validator->errors();
}else{
  
    
    $myarr = [];
    foreach($request->post('items')  as $myData => $key){
        
        // $myarr[]=$key;
        
        $clientName= empty($key['clientName']) ? '' : $key['clientName'];
        $clientId= empty($key['id']) ? '' : $key['id'];
        $projectName= empty($key['projectName']) ? '' : $key['projectName'];
        $ClientStatus= empty($key['ClientStatus']) ? '' : $key['ClientStatus'];
        $Comment= empty($key['Comment']) ? '' : $key['Comment'];
        $CommentStatus= empty($key['CommentStatus']) ? '' : $key['CommentStatus'];
        $Quality= empty($key['Quality']) ? '' : $key['Quality'];
        $callbackStatus= empty($key['callbackStatus']) ? '' : $key['callbackStatus'];
        $source= empty($key['source']) ? '' : $key['source'];
        
        if(!is_null($key)){
            $model = Zongcalling::where('id','=',$clientId)->first();
        $model->ClientName = $clientName == null || $clientName == '' ? $model->ClientName : $clientName;
        $model->projectName = $projectName == null || $projectName == '' ? $model->projectName : $projectName;
        $model->CallBack = $callbackStatus == null || $callbackStatus == '' ? $model->CallBack : $callbackStatus;
        $model->Source = $source == null || $source == '' ? $model->Source : $source;
        $model->Quality = $Quality == null || $Quality == '' ? $model->Quality : $Quality;
        $model->Comment = $Comment == null || $Comment == '' ? $model->Comment : $Comment;
        $model->CommentStatus = $CommentStatus == null || $CommentStatus == '' ? $model->CommentStatus : $CommentStatus;
        $model->ClientStatus = $ClientStatus == null || $ClientStatus == '' ? $model->ClientStatus : $ClientStatus;
        $model->update();
        }
        
        //  $myarr[]=array(
        //     'clientName' => $clientName,
        //     'id' => $clientId,
        //     'projectName' => $projectName,
        //     'callbackStatus'=>$callbackStatus,
        //     'source' => $source,
        //     'Quality' => $Quality,
        //     'Comment' => $Comment,
        //     'CommentStatus' => $CommentStatus,
        //     'ClientStatus' => $ClientStatus,
        // );
        
        // // $myarr[$myData]=array(
        // //  'clientName'=>$key['clientName']    
        // // );
       
    }
    
  
     return response()->json(['code'=>'200','error'=>false,'message'=>'Record Updated Successfully','data'=>$myarr]);
    
}

}

public function leadsDeleteData (Request $request){


foreach($request->get('isCheck') as $myLeads){
    
    ListAssignLeads::where('leadid', $myLeads)->delete();

}

 return response()->json(['code'=>'200','success'=>false,'message'=>'Record Delete Successfully','data'=>'']);
}

public function leadsTransferData (Request $request){


$consultant = $request->SalesConsul;
$companyId = $request->companyId;

foreach($request->get('isCheck') as $myLeads){
    
    // ListAssignLeads::where('leadid', $myLeads)->delete();
    ListAssignLeads::where('leadid', $myLeads)->update(['agentid'=>$consultant,'companyId'=>$companyId]);

}

 return response()->json(['code'=>'200','success'=>false,'message'=>'Record Updated Successfully','data'=>'']);
}

public function ListVirginLeadsExport (Request $request){

ini_set('memory_limit','-1');
   
    
    $q = $request->q;
    $fdate = $request->fdate;
    $tdate = $request->tdate;
    
    return Excel::download(new VirginLeadsExport($q,$fdate,$tdate), 'users.xlsx');
}



public function WHatsappACtivity (Request $request){
    
    ini_set('memory_limit','-1');
ini_set('max_execution_time', '-1000'); //300 seconds = 5 minutes

$orderbY = $request->orderby;
$orderType = $request->ordertype;
$perPage = $request->perpage;
$agentid = $request->agentid;
$leadid = $request->leadid;
$search = $request->q;

if(is_null($orderbY)){ $orderbY = "id"; }else{
$orderbY = $orderbY;
}

if(is_null($orderType)){ $ordertype = 'desc'; }else{
$ordertype = $orderType;
}

if(is_null($perPage)){

$listAssignCount = ListAssignLeads::with(['getleads','getVirginLeads','getreminder' => function ($query){
$query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0'])->orderBy('created_at', $ordertype)->count();

$PerPage = $listAssignCount;
$totalRec = $listAssignCount;

}else{

if($perPage){ $PerPage = $perPage; }else{
$PerPage = 12;
}

}


if(is_null($agentid)){

 if(is_null($leadid)){
     
     $listAssign = ListAssignLeads::with(['getleads','getvirginleads','getreminder','get_transferleads' => function ($query){
        $query->orderBy('id', 'desc')->get();
    } ,])->where(['shiftStatus'=>'0'])->orderBy($orderbY, $ordertype)->paginate($PerPage);
    
 }else{
     $listAssign = ListAssignLeads::with(['getleads','getvirginleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0','leadid'=>$leadid])->orderBy($orderbY, $ordertype)->get();
 }

 

$listAssignTotalPages = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0'])->orderBy($orderbY, $ordertype)->paginate($PerPage)->total();

$listAssignCount = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['shiftStatus'=>'0'])->orderBy($orderbY, $ordertype)->count();

}else if($agentid == 'all'){

$listAssign = ListAssignLeads::with(['getleads','getvirginleads','getreminder','get_transferleads' => function ($query){
        $query->orderBy('id', 'desc')->get();
    } ,])->whereHas('getleads', function($query) use($request) {
        if($request->q){
            $query->where('clientPhone','LIKE','%'.$request->q.'%');
            $query->orWhere('clientProject','LIKE','%'.$request->q.'%');
            $query->orWhere('clientName','LIKE','%'.$request->q.'%');
        }else if($request->nature){
            $query->where('nature','LIKE','%'.$request->nature.'%');
        }else if($request->salesId){
            $query->where('agentid','LIKE','%'.$request->salesId.'%');
        }
    })->where(['shiftStatus'=>'0'])->orderBy($orderbY, $ordertype)->paginate($PerPage);
    
$listAssignTotalPages = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->whereHas('getleads', function($query) use($request) {
        if($request->q){
            $query->where('clientPhone','LIKE','%'.$request->q.'%');
            $query->orWhere('clientProject','LIKE','%'.$request->q.'%');
            $query->orWhere('clientName','LIKE','%'.$request->q.'%');
        }else if($request->nature){
            $query->where('nature','LIKE','%'.$request->nature.'%');
        }else if($request->salesId){
            $query->where('agentid','LIKE','%'.$request->salesId.'%');
        }
    })->where(['shiftStatus'=>'0'])->orderBy($orderbY, $ordertype)->paginate($PerPage)->total();

$listAssignCount = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->whereHas('getleads', function($query) use($request) {
        if($request->q){
            $query->where('clientPhone','LIKE','%'.$request->q.'%');
            $query->orWhere('clientProject','LIKE','%'.$request->q.'%');
            $query->orWhere('clientName','LIKE','%'.$request->q.'%');
        }else if($request->nature){
            $query->where('nature','LIKE','%'.$request->nature.'%');
        }else if($request->salesId){
            $query->where('agentid','LIKE','%'.$request->salesId.'%');
        }
    })->where(['shiftStatus'=>'0'])->orderBy($orderbY, $ordertype)->count();

}else{
    echo 'asd';
if($request->transferStatus == 'transfer'){
    
    $listAssign = ListAssignLeads::with(['getleads','getvirginleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
    } ,])->whereHas('get_transferleads', function($query) use($request) {
        if($request->q){
            $query->where('created_at','LIKE','%'.$request->q.'%');
        }
    })->where(['agentid'=>$agentid,'shiftStatus'=>'0'])->orderBy($orderbY, $ordertype)->paginate($PerPage);
    
    $listAssignTotalPages = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
    } ,])->whereHas('get_transferleads', function($query) use($request) {
            if($request->q){
                $query->where('created_at','LIKE','%'.$request->q.'%');
            }
        })->where(['agentid'=>$agentid,'shiftStatus'=>'0'])->orderBy($orderbY, $ordertype)->paginate($PerPage)->total();
    
    $listAssignCount = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
    } ,])->whereHas('get_transferleads', function($query) use($request) {
            if($request->q){
                $query->where('created_at','LIKE','%'.$request->q.'%');
            }
        })->where(['agentid'=>$agentid,'shiftStatus'=>'0'])->orderBy($orderbY, $ordertype)->count();
    
}else{
    
    $listAssign = ListAssignLeads::with(['getleads','getvirginleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->whereDoesntHave('LeadImpression')->whereHas('getleads', function($query) use($request) {
        $query->where('nature','=','Saeed Sir');
        if($request->q){
            $query->where('clientPhone','LIKE','%'.$request->q.'%');
            $query->orWhere('clientProject','LIKE','%'.$request->q.'%');
            $query->orWhere('clientName','LIKE','%'.$request->q.'%');
        }else if($request->nature){
            $query->where('nature','LIKE','%'.$request->nature.'%');
        }
    })->where(['agentid'=>$agentid,'shiftStatus'=>'0'])->orderBy($orderbY, $ordertype)->paginate($PerPage);

$listAssignTotalPages = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->whereDoesntHave('LeadImpression')->whereHas('getleads', function($query) use($request) {
        $query->where('nature','=','Saeed Sir');
        if($request->q){
            $query->where('clientPhone','LIKE','%'.$request->q.'%');
            $query->orWhere('clientProject','LIKE','%'.$request->q.'%');
            $query->orWhere('clientName','LIKE','%'.$request->q.'%');
        }else if($request->nature){
            $query->where('nature','LIKE','%'.$request->nature.'%');
        }
    })->where(['agentid'=>$agentid,'shiftStatus'=>'0'])->orderBy($orderbY, $ordertype)->paginate($PerPage)->total();

$listAssignCount = ListAssignLeads::with(['getleads','getreminder','get_transferleads' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->whereDoesntHave('LeadImpression')->whereHas('getleads', function($query) use($request) {
        $query->where('nature','=','Saeed Sir');
        if($request->q){
            $query->where('clientPhone','LIKE','%'.$request->q.'%');
            $query->orWhere('clientProject','LIKE','%'.$request->q.'%');
            $query->orWhere('clientName','LIKE','%'.$request->q.'%');
        }else if($request->nature){
            $query->where('nature','LIKE','%'.$request->nature.'%');
        }
    })->where(['agentid'=>$agentid,'shiftStatus'=>'0'])->orderBy($orderbY, $ordertype)->count();
}
 

}


$countListassign = count($listAssign);

$TOtalPerPage = ceil($listAssignTotalPages/$PerPage);



if(count($listAssign) > 0){

$data = $this->WhatsapprenderList($listAssign);

return response()->json(['code'=>'200','success'=>true,'message'=>'Record Found','data'=>$data,'orderBy'=>$orderbY,'orderType'=>$ordertype,'perPage'=>$PerPage,'pageNo'=>$request->page,'totalRecords'=>$listAssignCount,'TotalPerPage'=>$TOtalPerPage,'search'=>$search]);
}else{
return response()->json(['code'=>'403','error'=>false,'message'=>'Record Not Found','data'=>$listAssign,'orderBy'=>$orderbY,'orderType'=>$ordertype,'perPage'=>'','pageNo'=>'','totalRecords'=>0]);
}

    
}

public function WhatsapprenderList(Object $apiResponse)
{
$data = [];

foreach ($apiResponse as $key => $value) {

if($value->get_transferleads == null){
$fAgent="";
}else{
$fAgent = Employee::where(['id'=>$value->get_transferleads->fagent])->first();
}

$queryAgentName = Employee::where(['id'=>$value->agentid])->first();

if($value->getleads == null || $value->getleads == ''){
    $replaceClientPhone = null;
    $replacewhatsappClientPhone = null;
}else{
    
    $haystack = $value->getleads->clientPhone;
    $needle   = '+92';
    
    $needlex   = '0';
    
    if (strpos($haystack, $needle) !== false) {
      
        $replaceWhatsappPhone = str_replace("+92","92",$value->getleads->clientPhone);
    }else if (strpos($haystack, $needlex) !== false) {
        
        $replaceWhatsappPhone = "+92".ltrim($value->getleads->clientPhone,'0');
        
    }else{
      
        $replaceWhatsappPhone = str_replace("0","92",$value->getleads->clientPhone);
    }
    
    $replacePhone = str_replace("+92","0",$value->getleads->clientPhone);
    
    $replaceClientPhone = $replacePhone;
    $replacewhatsappClientPhone = $replaceWhatsappPhone;
    
    $myxs = $replaceWhatsappPhone;
}

array_push($data, (object) [
"id" => $value->id,
"myxs"=>$myxs,
"created_at" => $value->created_at,
"updated_at" => $value->updated_at,
"deleted_at" => $value->deleted_at,
"agentid" => $value->agentid,
"emp_name" => $queryAgentName == null ? '' : $queryAgentName->emp_name,
"leadid" => $value->leadid,
"shiftStatus" => $value->shiftStatus,
"publishAgentDate" => $value->publishAgentDate,
"getleads" => $value->getleads,
"clientPhone" => $replaceClientPhone,
"whatsappclientPhone" => $replacewhatsappClientPhone,
"getvirginleads" => $value->getvirginleads,
"getreminder" => $value->getreminder,
// "get_transferleads"=>$value->get_transferleads,
"get_transferleads" => $value->get_transferleads == '' ? null : array(
    "id" => $value->get_transferleads == '' ? '' : $value->get_transferleads->id,
    "leadid" => $value->get_transferleads == '' ? '' : $value->get_transferleads->leadid,
    "fagent" => $value->get_transferleads == '' ? '' : $value->get_transferleads->fagent,
    "fagentName" => $fAgent == '' ? '' : $fAgent->emp_name,
    "tagent" => $value->get_transferleads == '' ? '' : $value->get_transferleads->tagent,
    "transferNote" => $value->get_transferleads == '' ? '' : $value->get_transferleads->transferNote,
    "sharePercentage" => $value->get_transferleads == '' ? '' : $value->get_transferleads->sharePercentage,
    "statusType" => $value->get_transferleads == '' ? '' : $value->get_transferleads->statusType,
    "publishStatus" => $value->get_transferleads == '' ? '' : $value->get_transferleads->publishStatus,
    "created_at" => $value->get_transferleads == '' ? '' : $value->get_transferleads->created_at,
    "updated_at" => $value->get_transferleads == '' ? '' : $value->get_transferleads->updated_at,
 ),
]);
}

return $data;
}



public function getEmployementType(){
    
    $data = EmploymentType::all();
    
    if($data){
        return response()->json(['code'=>'200','success'=>true,'message'=>'Record Found','data'=>$data]);
    }else{
        return response()->json(['code'=>'203','success'=>false,'message'=>'Record Not Found','data'=>'']);
    }
}


public function getHolyDays(){
    
    $data = HolyDays::all();
    
    if($data){
        return response()->json(['code'=>'200','success'=>true,'message'=>'Record Found','data'=>$data]);
    }else{
        return response()->json(['code'=>'203','success'=>false,'message'=>'Record Not Found','data'=>'']);
    }
}

public function getEmpShift(){
    
    $data = EmpShift::all();
    
    if($data){
        return response()->json(['code'=>'200','success'=>true,'message'=>'Record Found','data'=>$data]);
    }else{
        return response()->json(['code'=>'203','success'=>false,'message'=>'Record Not Found','data'=>'']);
    }
}




}
