<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ListAssignLeads;
use App\Models\ListMyLead;
use App\Models\CustomerActivity;
use App\Models\ActivityTypes;
use App\Models\ActivityOutcome;
use App\Models\TranasferShareLeads;
use App\Models\AgentEmployee;
use App\Models\ReasonNotInterested;
use App\Models\AssignReminder;
use App\Models\Assignreminderlist;

use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;

class ListMyLeads extends Controller
{
public function listMyLeads(Request $request){


$orderbY = $request->orderBy ?? "id";
$ordertype = $request->orderType ?? "desc";
$PerPage = $request->perPage ?? "12";
$page = $request->page ?? "1";

// $orderbY = $request->orderby;
// $orderType = $request->ordertype;
// $perPage = $request->perpage;

$agentid = $request->agentid;
$page = $request->page;
$cusid = $request->cusid;
$search = $request->q;
$teamObj = $request->teamObj ?? "";

$browserName = $request->browserName ?? "";
$SalesConsul = $request->SalesConsul ?? "";
$ProjectN = $request->ProjectN ?? "";
$SelfStatus = $request->SelfStatus ?? "";

// if(is_null($orderbY)){ $orderbY = "id"; }else{
//     $orderbY = $orderbY;
// }

// if(is_null($orderType)){ $ordertype = 'desc'; }else{
//     $ordertype = $orderType;
// }

// if(is_null($perPage)){
    
//      $listMyLeadCount = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails' => function ($query){
//         $query->orderBy('id', 'desc')->get();
//         } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->count();
    
//     $PerPage = $listMyLeadCount;
//     $totalRec = $listMyLeadCount;
    
// }else{
    
//     if($perPage){ $PerPage = $perPage; }else{
//         $PerPage = 5;
//     }
    
// }


if(is_null($agentid) && empty($teamObj) && empty($SalesConsul) && empty($ProjectN) && empty($search) && empty($SelfStatus)){
    
    if(!empty($cusid)){
    
    $listAssign = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->whereIn('customer_leads_tbls.LeadStatus',['My Lead','hot'])->where(['customer_leads_tbls.ReasonStatus'=>'No','customer_leads_tbls.id'=>$cusid])->orderBy($orderbY,$ordertype)->paginate($PerPage);
        
    $listMyLeadCOunt = ListMyLead::with(['ReminderData','getleads','getActivities' => function ($query){
    $query->orderBy('id', 'desc')->get();
    } , 'ProjectData'])->whereIn('customer_leads_tbls.LeadStatus',['My Lead','hot'])->where(['customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();
        
    }else{
    
    $listAssign = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->paginate($PerPage);
        
    $listMyLeadCOunt = ListMyLead::with(['ReminderData','getleads','getActivities' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();
        
    }
        
}else if($agentid == 'all' && empty($teamObj) && empty($SalesConsul) && empty($ProjectN) && empty($search) && empty($SelfStatus)){
    
     $listAssign = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->paginate($PerPage);
        
    $listMyLeadCOunt = ListMyLead::with(['ReminderData','getleads','getActivities' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();
    
}else if($agentid == 'all' && !empty($teamObj) && empty($SalesConsul) && empty($ProjectN) && empty($search) && empty($SelfStatus)){
        
    $myArray2 = explode(',', $teamObj);
    $myArray = str_replace(array('[',']'),'',$myArray2);
    
        
        if($SelfStatus == 0){
        
         $listAssign = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No','customer_leads_tbls.leadid'=>$SelfStatus])->orderBy($orderbY,$ordertype)->paginate($PerPage);
        
        $listMyLeadCOunt = ListMyLead::with(['ReminderData','getleads','getActivities' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No','customer_leads_tbls.leadid'=>$SelfStatus])->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();
            
        }else{
            $listAssign = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->whereIn('customer_leads_tbls.agentid',$myArray)->orderBy($orderbY,$ordertype)->paginate($PerPage);
        
    $listMyLeadCOunt = ListMyLead::with(['ReminderData','getleads','getActivities' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->whereIn('customer_leads_tbls.agentid',$myArray)->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();
        }
    
     
    
}else if($agentid == 'all' && !empty($teamObj) && !empty($SalesConsul) && empty($ProjectN) && empty($search) && empty($SelfStatus)){
        
    $myArray2 = explode(',', $teamObj);
    $myArray = str_replace(array('[',']'),'',$myArray2);
        
     if($SelfStatus == 0){
        
         $listAssign = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No','customer_leads_tbls.leadid'=>$SelfStatus])->where(['customer_leads_tbls.agentid'=>$SalesConsul])->orderBy($orderbY,$ordertype)->paginate($PerPage);
        
        $listMyLeadCOunt = ListMyLead::with(['ReminderData','getleads','getActivities' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No','customer_leads_tbls.leadid'=>$SelfStatus])->where(['customer_leads_tbls.agentid'=>$SalesConsul])->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();
            
        }else{
      $listAssign = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->where(['customer_leads_tbls.agentid'=>$SalesConsul])->orderBy($orderbY,$ordertype)->paginate($PerPage);
        
    $listMyLeadCOunt = ListMyLead::with(['ReminderData','getleads','getActivities' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->where(['customer_leads_tbls.agentid'=>$SalesConsul])->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();
        }
    
     
    
}else if($agentid == 'all' && empty($teamObj) && !empty($SalesConsul) && empty($ProjectN) && empty($search) && empty($SelfStatus)){
        
    $myArray2 = explode(',', $teamObj);
    $myArray = str_replace(array('[',']'),'',$myArray2);
    
    
     $listAssign = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->where(['customer_leads_tbls.agentid'=>$SalesConsul])->orderBy($orderbY,$ordertype)->paginate($PerPage);
        
    $listMyLeadCOunt = ListMyLead::with(['ReminderData','getleads','getActivities' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->where(['customer_leads_tbls.agentid'=>$SalesConsul])->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();
    
}else if($agentid == 'all' && empty($teamObj) && empty($SalesConsul) && !empty($ProjectN) && empty($search) && empty($SelfStatus)){
        
    $myArray2 = explode(',', $teamObj);
    $myArray = str_replace(array('[',']'),'',$myArray2);
    
    
     $listAssign = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->whereHas('getleads', function($query) use($request,$ProjectN) {
            $query->where(['clientProject'=>$ProjectN]);
    })->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->paginate($PerPage);
        
    $listMyLeadCOunt = ListMyLead::with(['ReminderData','getleads','getActivities' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->whereHas('getleads', function($query) use($request,$ProjectN) {
            $query->where(['clientProject'=>$ProjectN]);
    })->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();
    
}else if($agentid == 'all' && empty($teamObj) && empty($SalesConsul) && empty($ProjectN) && empty($search) && !empty($SelfStatus)){
        
    $myArray2 = explode(',', $teamObj);
    $myArray = str_replace(array('[',']'),'',$myArray2);
    
    
     $listAssign = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No','customer_leads_tbls.leadid'=>$SelfStatus])->orderBy($orderbY,$ordertype)->paginate($PerPage);
        
    $listMyLeadCOunt = ListMyLead::with(['ReminderData','getleads','getActivities' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No','customer_leads_tbls.leadid'=>$SelfStatus])->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();
    
}else if($agentid !='all' && empty($teamObj) && empty($SalesConsul) && !empty($ProjectN) && empty($search) && empty($SelfStatus)){
        
    $myArray2 = explode(',', $teamObj);
    $myArray = str_replace(array('[',']'),'',$myArray2);
    
  
     $listAssign = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->whereHas('getleads', function($query) use($request,$ProjectN) {
            $query->where(['clientProject'=>$ProjectN]);
    })->where(['customer_leads_tbls.agentid'=>$agentid])->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->paginate($PerPage);
        
    $listMyLeadCOunt = ListMyLead::with(['ReminderData','getleads','getActivities' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->whereHas('getleads', function($query) use($request,$ProjectN) {
            $query->where(['clientProject'=>$ProjectN]);
    })->where(['customer_leads_tbls.agentid'=>$agentid])->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();
    
}else if($agentid == 'all' && !empty($teamObj) && empty($SalesConsul) && empty($ProjectN) && !empty($search) && empty($SelfStatus)){
        
    $myArray2 = explode(',', $teamObj);
    $myArray = str_replace(array('[',']'),'',$myArray2);
    
    
     $listAssign = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->whereIn('agentid',$myArray)->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->where('customer_leads_tbls.mobileNo','LIKE','%'.$search.'%')->orderBy($orderbY,$ordertype)->paginate($PerPage);
        
    $listMyLeadCOunt = ListMyLead::with(['ReminderData','getleads','getActivities' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->whereIn('agentid',$myArray)->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->where('customer_leads_tbls.mobileNo','LIKE','%'.$search.'%')->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();
    
}else if($agentid == 'all' && !empty($teamObj) && empty($SalesConsul) && empty($ProjectN) && empty($search) && !empty($SelfStatus)){
        
    $myArray2 = explode(',', $teamObj);
    $myArray = str_replace(array('[',']'),'',$myArray2);
    
    
    $listAssign = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->whereIn('agentid',$myArray)->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No','customer_leads_tbls.leadid'=>$SelfStatus])->where('customer_leads_tbls.mobileNo','LIKE','%'.$search.'%')->orderBy($orderbY,$ordertype)->paginate($PerPage);
        
    $listMyLeadCOunt = ListMyLead::with(['ReminderData','getleads','getActivities' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->whereIn('agentid',$myArray)->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No','customer_leads_tbls.leadid'=>$SelfStatus])->where('customer_leads_tbls.mobileNo','LIKE','%'.$search.'%')->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();
    
}else if($agentid == 'all' && !empty($teamObj) && empty($SalesConsul) && !empty($ProjectN) && empty($search) && empty($SelfStatus)){
        
    $myArray2 = explode(',', $teamObj);
    $myArray = str_replace(array('[',']'),'',$myArray2);
    
    
     $listAssign = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->whereHas('getleads', function($query) use($request,$ProjectN) {
                    $query->where(['clientProject'=>$ProjectN]);
            })->whereIn('agentid',$myArray)->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->where('customer_leads_tbls.mobileNo','LIKE','%'.$search.'%')->orderBy($orderbY,$ordertype)->paginate($PerPage);
        
    $listMyLeadCOunt = ListMyLead::with(['ReminderData','getleads','getActivities' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->whereHas('getleads', function($query) use($request,$ProjectN) {
            $query->where(['clientProject'=>$ProjectN]);
    })->whereIn('agentid',$myArray)->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->where('customer_leads_tbls.mobileNo','LIKE','%'.$search.'%')->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();
    
}else if($agentid == 'all' && !empty($teamObj) && !empty($SalesConsul) && !empty($ProjectN) && empty($search) && empty($SelfStatus)){
        
    $myArray2 = explode(',', $teamObj);
    $myArray = str_replace(array('[',']'),'',$myArray2);
    
    
     $listAssign = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->whereHas('getleads', function($query) use($request,$ProjectN) {
                    $query->where(['clientProject'=>$ProjectN]);
            })->whereIn('agentid',$myArray)->where(['customer_leads_tbls.agentid'=>$SalesConsul])->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->where('customer_leads_tbls.mobileNo','LIKE','%'.$search.'%')->orderBy($orderbY,$ordertype)->paginate($PerPage);
        
    $listMyLeadCOunt = ListMyLead::with(['ReminderData','getleads','getActivities' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->whereHas('getleads', function($query) use($request,$ProjectN) {
            $query->where(['clientProject'=>$ProjectN]);
    })->whereIn('agentid',$myArray)->where(['customer_leads_tbls.agentid'=>$SalesConsul])->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->where('customer_leads_tbls.mobileNo','LIKE','%'.$search.'%')->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();
    
}else if($agentid == 'all' && empty($teamObj) && empty($SalesConsul) && empty($ProjectN) && !empty($search) && empty($SelfStatus)){
     
    $myArray2 = explode(',', $teamObj);
    $myArray = str_replace(array('[',']'),'',$myArray2);
    
    
    $listAssign = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->whereHas('getleads', function($query) use($request,$search) {
            $query->where('clientName','LIKE','%'.$search.'%');
             $query->orWhere('clientPhone','LIKE','%'.$search.'%');
    })->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->paginate($PerPage);
        
    $listMyLeadCOunt = ListMyLead::with(['ReminderData','getleads','getActivities' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->whereHas('getleads', function($query) use($request,$search) {
            $query->where('clientName','LIKE','%'.$search.'%');
             $query->orWhere('clientPhone','LIKE','%'.$search.'%');
    })->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();
    
}else if($agentid !='all' && empty($teamObj) && empty($SalesConsul) && empty($ProjectN) && !empty($search) && empty($SelfStatus)){
      
    $myArray2 = explode(',', $teamObj);
    $myArray = str_replace(array('[',']'),'',$myArray2);
    
   
     $listAssign = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.agentid'=>$agentid])->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->where('customer_leads_tbls.mobileNo','LIKE','%'.$search.'%')->orderBy($orderbY,$ordertype)->paginate($PerPage);
        
    $listMyLeadCOunt = ListMyLead::with(['ReminderData','getleads','getActivities' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.agentid'=>$agentid])->where('customer_leads_tbls.mobileNo','LIKE','%'.$search.'%')->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();
    
}else if($agentid !='all' && empty($teamObj) && empty($SalesConsul) && empty($ProjectN) && empty($search) && !empty($SelfStatus)){
    
}else{
    
   if($SelfStatus == '0'){
       
         $listAssign = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.agentid'=>$agentid,'customer_leads_tbls.leadid'=>0])->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->paginate($PerPage);
        
    $listMyLeadCOunt = ListMyLead::with(['ReminderData','getleads','getActivities' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.agentid'=>$agentid,'customer_leads_tbls.leadid'=>0])->where(['customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();
       
   }else{
       
       
     if($request->q){
         
        $listAssign = ListMyLead::with(['ReminderData','getleads','ProjectData','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        }])->whereHas('getleads', function($query) use($request) {
            if($request->q){
                $query->where('clientName','LIKE','%'.$request->q.'%');
            }
        })->where(['customer_leads_tbls.agentid'=>$agentid,'customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->paginate($PerPage);
        
        $listMyLeadCOunt = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->whereHas('getleads', function($query) use($request) {
            if($request->q){
                $query->where('clientName','LIKE','%'.$request->q.'%');
            }
        })->where(['customer_leads_tbls.agentid'=>$agentid,'customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();
         
     }else{
        $listAssign = ListMyLead::with(['ReminderData','getleads','ProjectData','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        }])->where(['customer_leads_tbls.agentid'=>$agentid,'customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->paginate($PerPage);
    
        $listMyLeadCOunt = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.agentid'=>$agentid,'customer_leads_tbls.LeadStatus'=>'My Lead','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();
     }
   }
   
    
    
}

$countListassign = count($listAssign);


if(count($listAssign) > 0){
    
    $TOtalPerPage = ceil($listMyLeadCOunt/$PerPage);
    
    $data = $this->AssignrenderList($listAssign,$browserName);
    
    return response()->json(['code'=>'200','success'=>true,'message'=>'Record Found','data'=>$data,'orderBy'=>$orderbY,'orderType'=>$ordertype,'perPage'=>$PerPage,'pageNo'=>$request->page,'totalRecords'=>$listMyLeadCOunt,'TotalPerPage'=>$TOtalPerPage,'search'=>$search,'browserName'=>$browserName]);
}else{
    return response()->json(['code'=>'403','error'=>false,'message'=>'Record Not Found','data'=>$listAssign,'orderBy'=>'','orderType'=>'','perPage'=>'','pageNo'=>'','totalRecords'=>0,'search'=>$search,'browserName'=>$browserName]);
}

}

public function AssignrenderList(Object $apiResponse,$browserName)
{
$data = [];

foreach ($apiResponse as $key => $value) {
    
    if($browserName == 'Mobile Safari'){
        $iosClientPhone= "0".ltrim($value->getleads == '' || $value->getleads == null ? '' : $value->getleads->clientPhone,"92");
    }else{
        $iosClientPhone=$value->mobileNo;
    }
    $timeSince = Carbon::parse($value->created_at)->diffForHumans();
    
    $ActivityTypes = ActivityTypes::where(['id'=>$value->actMediumId])->first();

    array_push($data, (object) [
    "id" => $value->id,
    "created_at" => $value->created_at,
    "updated_at" => $value->updated_at,
    "deleted_at" => $value->deleted_at,
    "reminder_data"=>$value->ReminderData,
    "get_employee_details"=>$value->getEmployeeDetails,
    "getleads"=>$value->getleads,
    "leadid"=>$value->leadid,
    "agentid"=>$value->agentid,
    "email"=>$value->email,
    "clientName"=>$value->clientName,
    "mobileNo"=>$iosClientPhone,
    "PublishDate"=>date("d F Y",strtotime($value->created_at)),
    "PublishTime"=>date("H:i",strtotime($value->created_at)),
    "project_data"=>$value->ProjectData,
    "get_activities"=>$value->getActivities,
    "iosClientPhone"=>$iosClientPhone,
    // "value"=>$value,
    "clientActivityDesc"=>$value->clientActivityDesc,
    "timeSince"=>$timeSince,
    "activityName"=>$ActivityTypes == null || $ActivityTypes == '' ? '' : $ActivityTypes->activityName,
    "actMediumId"=>$value->actMediumId,
    
    ]);
}

return $data;
}




public function singleMylist(Request $request , $id){
    
    $data = ListMyLead::where(['id'=>$id])->get();
    
    if(empty($data)){
        return response()->json(['code'=>'200','success'=>true,'message'=>'Record Not Found','data'=>'']);
    }else{
        
        $mydata = $this->renderListItems($data);
        
     return response()->json(['code'=>'200','success'=>true,'message'=>'Record Found','data'=>$mydata]);
        
    }
    
    
}

public function renderListItems(Object $apiResponse)
{
$data = [];

foreach ($apiResponse as $key => $value) {
 
    
    array_push($data, (object) [
    "id" => $value->id,
    "created_at" => $value->created_at,
    "updated_at" => $value->updated_at,
    "deleted_at" => $value->deleted_at,
    "clientName"=>$value->clientName,
    
    ]);
}

return $data;
}


public function listMyLeadsStatusHot(Request $req){

$rules=array(
    'customerid'=>'required',
);

$validator = Validator::make($req->all(),$rules);
if($validator->fails()){
    return $validator->errors();
}else{

    $model = ListMyLead::where('id','=',$req->post('customerid'))->first();

    $data = ListMyLead::where('id', $req->post('customerid'))
    ->update([
    'LeadStatus' => 'hot',
    'clientInterst' => '60',
    ]);

    if($data){
        return response()->json(['code'=>'200','success'=>true,'message'=>'Lead Status Update']);
    }else{
        return response()->json(['code'=>'403','error'=>false,'message'=>'Error Please Try Again']);
    }

}


}


public function listMyLeadsActivity(Request $request){

$rules=array(
    'customerid'=>'required',
    'agentid'=>'required',
    'ActivityType'=>'required',
    'Outcome'=>'required',
    // 'clientInterest'=>'required',
    'desc'=>'required',
    'activityStatus'=>'required',
    'NextActivityDate'=>'required',
);

$validator = Validator::make($request->all(),$rules);
if($validator->fails()){
    return $validator->errors();
}else{
    
     if($request->hasFile("Whatsappscreenshot")){
            $image = $request->file("Whatsappscreenshot");
            $ext = $image->extension();
            $image_name="Whatsappscreenshot".time().'.'.$ext;
            $image->storeAs('/public/AssetImages/',$image_name);
             $FileData = "public/storage/AssetImages/".$image_name;
            
        }else{
            $FileData="";
        }

    $model=new CustomerActivity();
    $model->customerId = $request->post('customerid');
    $model->agentid =  $request->post('agentid');
    $model->typeId = $request->post('ActivityType');
    $model->outcomeId = $request->post('Outcome');
    $model->InterestPercentage = $request->post('clientInterest') == null || $request->post('clientInterest') == '' ? 10 : $request->post('clientInterest');
    $model->activityDetails = $request->post('desc');
    $model->actStatus = $request->post('activityStatus');
    $model->Mettingtime = $request->post('Mettingtime');
    $model->MettingLocation = $request->post('MettingLocation');
    $model->NextActivityDate = $request->post('NextActivityDate');
    $model->ReminderPriority = $request->post('ReminderPriority');
    $model->companyId = $request->post('companyId');
    $model->Whatsappscreenshot = $FileData;
     $model->Whatsapptalkduration = $request->post('Whatsapptalkduration');
    
    $data = $model->save();
    
    
    $modelEw=new AssignReminder();
    
    $modelEw->agentid = $request->post('agentid');
    $modelEw->leadid = $request->post('leadid') == null || $request->post('leadid') == '' ? 0 : $request->post('leadid');
    $modelEw->customerId = $request->post('customerid');
    $modelEw->reminderSet = $request->post('NextActivityDate');
    $modelEw->priority = $request->post('priority');
    $modelEw->reminderinfo = $request->post('desc');
    $modelEw->reminderTypeId = $request->post('ActivityType');
    $modelEw->activityId = $model->id;
    $modelEw->companyId = $request->post('companyId');
    
    $modelEw->save();
    
    

    if($data){
        return response()->json(['code'=>'200','success'=>true,'message'=>'Activity Successfully Add']);
    }else{
        return response()->json(['code'=>'403','error'=>false,'message'=>'Error Please Try Again']);
    }

}

}


public function listMyLeadsActivityReminder(Request $request){

$rules=array(
    'customerid'=>'required',
    'agentid'=>'required',
    'ActivityType'=>'required',
    'reminderSet'=>'required',
    'priority'=>'required',
    'reminderinfo'=>'required',
    'reminderTypeId'=>'required',
    
);

$validator = Validator::make($request->all(),$rules);
if($validator->fails()){
    return $validator->errors();
}else{
    
    $ReminderListId=0;
    
    if($request->reminderTypeId == 'Other'){
        
        $OtherReminderType = Assignreminderlist::create([
            'typeTitle' =>  $request->otherreminderTypeId,
            'status' =>  1,
        ]);
        
        $lastId = $OtherReminderType->id;
        $ReminderListId = $lastId;
        
    }else{
        $ReminderListId = $request->reminderTypeId;
    }


    $model=new CustomerActivity();
    $model->customerId = $request->post('customerid');
    $model->agentid =  $request->post('agentid');
    $model->typeId = $request->post('ActivityType');
    $model->outcomeId = $request->post('Outcome');
    $model->InterestPercentage = $request->post('clientInterest') == '' || $request->post('clientInterest') == null ? '10' : $request->post('clientInterest');
    $model->activityDetails = $request->post('reminderinfo');
    $model->actStatus = 'On Going';
    $model->Mettingtime = $request->post('Mettingtime');
    $model->MettingLocation = $request->post('MettingLocation');
    $model->companyId = $request->post('companyId');

    $data = $model->save();
    
    
    $modelEw=new AssignReminder();
    
    $modelEw->agentid = $request->post('agentid');
    $modelEw->leadid = $request->post('leadid');
    $modelEw->customerId = $request->post('customerid');
    $modelEw->reminderSet = str_replace('/','-',$request->post('reminderSet'));
    $modelEw->priority = $request->post('priority');
    $modelEw->reminderinfo = $request->post('reminderinfo');
    $modelEw->reminderTypeId = $ReminderListId;
    $modelEw->activityId = $model->id;
    $modelEw->companyId = $request->post('companyId');
    
    $modelEw->save();

    if($data){
        return response()->json(['code'=>'200','success'=>true,'message'=>'Activity Reminder Successfully Add','data'=>$model]);
    }else{
        return response()->json(['code'=>'403','error'=>false,'message'=>'Error Please Try Again']);
    }

}

}

public function getActivityTypeApi(){

$data = ActivityTypes::where(['Estatus'=>'Active','parentId'=>'0'])->get();
if($data){
    return response()->json(['code'=>'200','success'=>true,'message'=>'successfully','data'=>$data]);
}else{
    return response()->json(['code'=>'403','error'=>false,'message'=>'Error Please Try Again']);
}

}

public function getActivityTypeParnetApi(Request $request){

$id = $request->id;
$data = ActivityTypes::where(['Estatus'=>'Active','parentId'=>$id])->get();
if($data){
    return response()->json(['code'=>'200','success'=>true,'message'=>'successfully','data'=>$data]);
}else{
    return response()->json(['code'=>'403','error'=>false,'message'=>'Error Please Try Again']);
}

}

public function getActivityOutcomeApi(Request $request){

$activityId = $request->get('activityId');
if($activityId == ''){
    return response()->json(['code'=>'403','error'=>false,'message'=>'Select Activity Type First']);
}else{

    $data = ActivityOutcome::WHERE(['activityId'=>$activityId])->get();
    if(count($data) > 0){
        return response()->json(['code'=>'200','success'=>true,'message'=>'data fetch','data'=>$data]);
    }else{
        return response()->json(['code'=>'403','error'=>false,'message'=>'data not fetch']);
    }

}
}


public function listMyLeadsShareLeads(Request $req){

$rules=array(
    'leadid'=>'required',
    'fromagent'=>'required',
    'toagent'=>'required',
    'shareNote'=>'required',
    'sharePercentage'=>'required',
);

$leadid = $req->post('leadid');
$fromagent = $req->post('fromagent');
$toagent = $req->post('toagent');
$shareNote = $req->post('shareNote');
$statusType = "share";
$sharePercentage = $req->post('sharePercentage');
$sharestatus = "pending";
$company_id = $req->post('company_id');

$validator = Validator::make($req->all(),$rules);
if($validator->fails()){
    return $validator->errors();
}else{


$checkRequest = TranasferShareLeads::where(['leadid'=>$leadid,'fagent'=>$fromagent,'companyId'=>$company_id])->get();
if(count($checkRequest) > 0){
    $agentData = AgentEmployee::where(['id'=>$checkRequest[0]->tagent])->get();
}

if(count($checkRequest) > 0){
    $msg="Lead Already Share to ".$agentData[0]->name;
}else{

    $ShareData = new TranasferShareLeads();

    $ShareData->leadid = $leadid;
    $ShareData->fagent = $fagent;
    $ShareData->tagent = $tagent;
    $ShareData->transferNote = $shareNote;
    $ShareData->statusType = $statusType;
    $ShareData->publishStatus = $sharestatus;
    $ShareData->sharePercentage = $sharePercentage;
    $ShareData->companyId = $company_id;
    $data = $ShareData->save();

    if(count($data) > 0){
        return response()->json(['code'=>'200','success'=>true,'message'=>'Lead Share Successfully!','data'=>$msg]);
    }else{
        return response()->json(['code'=>'403','error'=>false,'message'=>'Error Please Try Again!','data'=>'']);
    }


}


}


}

public function listMyLeadsNotinterested(Request $req){

$leadid = $req->post('leadid');
$client_opt = $req->post('client_opt');
$OtherNote = $req->post('OtherNote');
$agentid = $req->post('agentid');
$customerId = $req->post('customerId');
$company_id = $req->post('company_id');

$rules=array(
    'agentid'=>'required',
    'leadid'=>'required',
    'client_opt'=>'required',
);

$validator = Validator::make($req->all(),$rules);
if($validator->fails()){
    return $validator->errors();
}else{
    
    if($leadid == 0){
        
        $modelMy = ListMyLead::where('id','=',$customerId)->where(['companyId'=>$company_id])->first();
        $modelMy->ReasonStatus = 'Yes';
        $modelMy->update();
        
    }else{
        $model = ListAssignLeads::where('leadid','=',$leadid)->where(['companyId'=>$company_id])->first();
        $model->shiftStatus = '2';
        $model->update();
        
        $modelMy = ListMyLead::where('leadid','=',$leadid)->where(['companyId'=>$company_id])->first();
        $modelMy->ReasonStatus = 'Yes';
        $modelMy->update();
    }
    
    
   

    $ReasonQ = new ReasonNotInterested();
    $ReasonQ->leadid = $leadid;
    $ReasonQ->agentid = $agentid;
    $ReasonQ->customerId = $customerId;
    
    $ReasonQ->reasonTitle = $client_opt;
    $ReasonQ->budgetPrice = null;
    $ReasonQ->otherReason = $OtherNote;
    $ReasonQ->companyId = $company_id;

   $data = $ReasonQ->save();
    
    if($data){
        return response()->json(['code'=>'200','success'=>true,'message'=>'Not Interested Successfully']);
    }else{
        return response()->json(['code'=>'403','error'=>false,'message'=>'Error Please Try Again']);
    }

}


}

}
