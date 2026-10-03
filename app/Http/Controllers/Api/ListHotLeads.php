<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ListAssignLeads;
use App\Models\ListHotLead;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ListHotLeads extends Controller
{
public function listHotLeads(Request $request){

$orderbY = $request->orderby;
$orderType = $request->ordertype;
$perPage = $request->perpage;
$agentid = $request->agentid;
$teamObj = $request->teamObj ?? "";
$browserName = $request->browserName ?? "";
$SalesConsul = $request->SalesConsul ?? "";

if(is_null($orderbY)){ $orderbY = "id"; }else{
    $orderbY = $orderbY;
}

if(is_null($orderType)){ $ordertype = 'desc'; }else{
    $ordertype = $orderType;
}

if(is_null($perPage)){
            
     $listHotLeadCount = ListHotLead::with(['ReminderData','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'hot','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->count();
    
    $PerPage = $listHotLeadCount;
    $totalRec = $listHotLeadCount;
    
}else{
    
    if($perPage){ $PerPage = $perPage; }else{
        $PerPage = 5;
    }
    
}

$listHotLeadCount = ListHotLead::with(['ReminderData','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'hot','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->count();

if(is_null($agentid) && empty($teamObj) && empty($SalesConsul)){
            
    $listAssign = ListHotLead::with(['ReminderData','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'hot','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->paginate($PerPage);
        
    $listHotLeadCOunt = ListHotLead::with(['ReminderData','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'hot','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();
        
}else if($agentid == 'all' && empty($teamObj) && empty($SalesConsul)){
            
    $listAssign = ListHotLead::with(['ReminderData','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'hot','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->paginate($PerPage);
        
    $listHotLeadCOunt = ListHotLead::with(['ReminderData','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'hot','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();
        
}else if($agentid == 'all' && !empty($teamObj) && empty($SalesConsul)){
        
        $myArray2 = explode(',', $teamObj);
        $myArray = str_replace(array('[',']'),'',$myArray2);    
            
    $listAssign = ListHotLead::with(['ReminderData','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'hot','customer_leads_tbls.ReasonStatus'=>'No'])->whereIn('customer_leads_tbls.agentid',$myArray)->orderBy($orderbY,$ordertype)->paginate($PerPage);
        
    $listHotLeadCOunt = ListHotLead::with(['ReminderData','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'hot','customer_leads_tbls.ReasonStatus'=>'No'])->whereIn('customer_leads_tbls.agentid',$myArray)->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();
        
}else if($agentid == 'all' && !empty($teamObj) && !empty($SalesConsul)){
        
        $myArray2 = explode(',', $teamObj);
        $myArray = str_replace(array('[',']'),'',$myArray2);    
            
    $listAssign = ListHotLead::with(['ReminderData','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'hot','customer_leads_tbls.ReasonStatus'=>'No'])->where(['customer_leads_tbls.agentid'=>$SalesConsul])->orderBy($orderbY,$ordertype)->paginate($PerPage);
        
    $listHotLeadCOunt = ListHotLead::with(['ReminderData','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.LeadStatus'=>'hot','customer_leads_tbls.ReasonStatus'=>'No'])->where(['customer_leads_tbls.agentid'=>$SalesConsul])->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();
        
}else{

    $listAssign = ListHotLead::with(['ReminderData','getActivities','getEmployeeDetails' => function ($query){
        $query->orderBy('id', 'desc')->get();
        } , 'ProjectData'])->where(['customer_leads_tbls.agentid'=>$agentid,'customer_leads_tbls.LeadStatus'=>'hot','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->paginate($PerPage);
    
     $listHotLeadCOunt = ListHotLead::with(['ReminderData','getActivities','getEmployeeDetails' => function ($query){
    $query->orderBy('id', 'desc')->get();
    } , 'ProjectData'])->where(['customer_leads_tbls.agentid'=>$agentid,'customer_leads_tbls.LeadStatus'=>'hot','customer_leads_tbls.ReasonStatus'=>'No'])->orderBy($orderbY,$ordertype)->paginate($PerPage)->total();
    
}

$countListassign = count($listAssign);



if(count($listAssign) > 0){
    
    $TOtalPerPage = ceil($listHotLeadCOunt/$PerPage);
    
    $data = $this->AssignrenderList($listAssign,$browserName);
    
    return response()->json(['code'=>'200','success'=>true,'message'=>'Record Found','data'=>$data,'orderBy'=>$ordertype,'orderType'=>$orderbY,'perPage'=>$PerPage,'pageNo'=>$request->page,'totalRecords'=>$listHotLeadCOunt,'TotalPerPage'=>$TOtalPerPage,'total'=>$listHotLeadCOunt]);
}else{
    return response()->json(['code'=>'403','error'=>false,'message'=>'Record Not Found','data'=>'','orderBy'=>'','orderType'=>'','perPage'=>'','pageNo'=>'','totalRecords'=>0]);
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
    "project_data"=>$value->ProjectData,
    "get_activities"=>$value->getActivities,
    "clientActivityDesc"=>$value->clientActivityDesc,
    "iosClientPhone"=>$iosClientPhone,
    "PublishDate"=>date("d F Y",strtotime($value->created_at)),
    "PublishTime"=>date("H:i",strtotime($value->created_at)),
    "timeSince"=>$timeSince,
    // "value"=>$value,
    
    ]);
}

return $data;
}


}
