<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ListAssignLeads;
use App\Models\ListMyLead;
use App\Models\ListNotInterested;
use App\Models\CustomerActivity;
use App\Models\AssignReminder;
use App\Models\LeadImpression;
use App\Models\TranasferShareLeads;
use App\Models\HR\Employee;
use App\Models\Zongcalling;
use App\Models\LeadImportDatabase;
use App\Models\Videorequest;
use App\Models\DocumentType;
use App\Models\apartmentType;
use App\Models\natureProject;
use App\Models\propertyType;
use App\Models\Issuingauthority;
use App\Models\Purposeprop;
use App\Models\Finance\stockNature;
use App\Models\Finance\stockType;
use App\Models\Finance\Stockrecby;
use App\Models\Finance\Stockdeliveredby;
use App\Models\AffiliateProgram\AffiliateRegisterModule;
use App\Models\Finance\Inventoriesfilters;

use App\Models\Inventory\Project;
use App\Models\Inventory\Inventorysold;
use App\Models\Finance\AssetType;
use App\Models\Finance\AssetLife;

use Illuminate\Support\Arr;

use Carbon\Carbon;
use DB;
use Validator;


use Illuminate\Http\Request;

class EmployeeLogin extends Controller
{
public function DashboardSalesCount(Request $request){

ini_set('memory_limit','-1');

$agentid = $request->agentId;
$teamObj = $request->teamObj;

if(is_null($agentid) && is_null($teamObj)){
                
                
    $totalOutgoingAll = 0;
    $totaConnectedAll = 0;
    $totaFollowupAll = 0;
      $totaPersonalCallAll = 0;
         $totaIncommingCallAll = 0;
    $totaIncommingAnsweredCallAll = 0;
     $totaIncommingMissedCallAll = 0;
      $totaCallbackCallAll = 0;
      $totaCallbackNotCallAll = 0;
      $totaYoutubeCallAll = 0;
      $totaSocialMediaCallAll = 0;
      $totalTalktimeDuration=0;
      $totalTalktimeIncDuration = 0;
    //Assign Leads Count
    $AssignLeadsTotal = ListAssignLeads::join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsassigndatabase.shiftStatus'=>'0'])->select('leadsassigndatabase.leadid','leadsimportdatabase.id','leadsassigndatabase.agentid')->count();
    
    $AssignLeadsToday = ListAssignLeads::join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsassigndatabase.shiftStatus'=>'0'])->whereDate('leadsassigndatabase.publishAgentDate', date('Y-m-d'))->select('leadsassigndatabase.leadid','leadsimportdatabase.id','leadsassigndatabase.agentid')->count();

    //My Leads Count
    $MyLeadCountAll = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'No'])->count();
    
    $MyLeadCountToday = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'No'])->whereDate('created_at', date('Y-m-d'))->count();
    
    //Dorment Leads Count
    $DormentLeadCountAll = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'Yes'])->count();
    $DormentLeadCountToday = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'Yes'])->whereDate('created_at', date('Y-m-d'))->count();

    //Hot Leads Count
    $HotCountAll = ListMyLead::where(['LeadStatus'=>'hot','ReasonStatus'=>'No'])->count();
    $HotCountToday = ListMyLead::where(['LeadStatus'=>'hot','ReasonStatus'=>'No'])->whereDate('updated_at', date('Y-m-d'))->count();

    //Not Interested COunt
    $NotinterestedCountAll = ListNotInterested::where(['agentid'=>$agentid])->count();
    $NotinterestedCountToday = ListNotInterested::where(['agentid'=>$agentid])->whereDate('updated_at', date('Y-m-d'))->count();

    //Customer Activity Count
    $cusACtivityCountAll = CustomerActivity::count();
    $cusActivityCountToday = CustomerActivity::whereDate('updated_at', date('Y-m-d'))->count();

    //Reminder Count
    $ReminderCountAll = AssignReminder::count();
    $ReminderCountToday = AssignReminder::whereDate('updated_at', date('Y-m-d'))->count();

    $PhoneCountToday = LeadImpression::where(['impType'=>'phone'])->whereDate('updated_at', date('Y-m-d'))->count();
    $whatsappCountToday = LeadImpression::where(['impType'=>'whatsapp'])->whereDate('updated_at', date('Y-m-d'))->count();
    $smsCountToday = LeadImpression::where(['impType'=>'sms'])->whereDate('updated_at', date('Y-m-d'))->count();
    $emailCountToday = LeadImpression::where(['impType'=>'email'])->whereDate('updated_at', date('Y-m-d'))->count();

    $untabbedLeadsAll=ListAssignLeads::whereDoesntHave('LeadImpression')->where(['shiftStatus'=>'0'])->count();
    $untabbedLeadsToday=ListAssignLeads::whereDoesntHave('LeadImpression')->where(['shiftStatus'=>'0'])->whereDate('updated_at', date('Y-m-d'))->count();

    $MyLeadMaleCountAll = ListMyLead::where(['Gender'=>'male'])->count();
    $MyLeadFeMaleCountAll = ListMyLead::where(['Gender'=>'female'])->count();
    
    $ReminderTodayData = AssignReminder::with(['getleads','getleads_home' => function ($query){
        $query->orderBy('id', 'desc')->get();
    } ,])->whereDate('reminderSet', date('Y-m-d'))->get();
    
    $TransferLeadsCountAll = TranasferShareLeads::where(['statusType'=>'transfer'])->count();
    
    $TodayTransferLeadsCount = TranasferShareLeads::where(['statusType'=>'transfer'])->whereDate('updated_at', date('Y-m-d'))->count();
    
    
    $listAssignDashboard = ListAssignLeads::with(['getleads','getvirginleads','getreminder' => function ($query){
            $query->orderBy('id', 'desc')->get();
        } ,])->whereDate('publishAgentDate', date('Y-m-d'))->where(['shiftStatus'=>'0'])->orderBy('id', 'desc')->skip(0)->take(5)->get();
        
        
    $untabbedLeadsAllGraph=ListAssignLeads::whereDoesntHave('LeadImpression')->where(['shiftStatus'=>'0'])->join('leadsimportdatabase', 'leadsimportdatabase.id', '=', 'leadsassigndatabase.leadid')->select('clientProject', DB::raw('count(clientProject) as total_count'))->groupBy('clientProject')->get();
    
   
    $empData = Employee::where(['id'=>$agentid])->first();
    
    $empAll = Employee::all();
    
    $zongDumy = array(array("Type"=>"Incomming Calls","total_count"=>0),array("Type"=>"Outgoing Calls","total_count"=>0));
    
   
 
    if(empty($empData->child_num)){
        $zongCallingData=$zongDumy;
    }else{
         $zongCallingData = Zongcalling::where(['ext'=>$empData->child_num])->select('Type', DB::raw('count(Type) as total_count'))->groupBy('Type')->get();
    }
    
    
    $CallingCountToday = Zongcalling::whereDate('updated_at', date('Y-m-d'))->count();
    $CallingCountTotal = Zongcalling::count();
    
    $TotalOutgoingCallData = Zongcalling::where(['Type'=>'Outgoing Calls'])->select('Type', DB::raw('count(Type) as total_count'))->groupBy('Type')->count();
    
    $TodayOutgoingCallData = Zongcalling::where(['Type'=>'Outgoing Calls'])->whereDate('datetime', date('Y-m-d'))->select('Type', DB::raw('count(Type) as total_count'))->groupBy('Type')->count();
    
    $TotalIncommingCallData = Zongcalling::where(['Type'=>'Incomming Calls'])->select('Type', DB::raw('count(Type) as total_count'))->groupBy('Type')->count();
    
    $TodayIncommingCallData = Zongcalling::where(['Type'=>'Incomming Calls'])->whereDate('datetime', date('Y-m-d'))->select('Type', DB::raw('count(Type) as total_count'))->groupBy('Type')->count();
    
    $TotalConnectedCallData = Zongcalling::where(['status'=>'Connected'])->select('status', DB::raw('count(status) as total_count'))->groupBy('status')->count();
    $TodayConnectedCallData = Zongcalling::where(['status'=>'Connected'])->whereDate('datetime', date('Y-m-d'))->select('status', DB::raw('count(status) as total_count'))->groupBy('status')->count();
    
    $TotalMissedCallData = Zongcalling::where(['status'=>'Missed'])->select('status', DB::raw('count(status) as total_count'))->groupBy('status')->count();
    $TodayMissedCallData = Zongcalling::where(['status'=>'Missed'])->whereDate('datetime', date('Y-m-d'))->select('status', DB::raw('count(status) as total_count'))->groupBy('status')->count();
    
    $TotalAnsweredCallData = Zongcalling::where(['status'=>'Answered'])->select('status', DB::raw('count(status) as total_count'))->groupBy('status')->count();
    $TodayAnsweredCallData = Zongcalling::where(['status'=>'Answered'])->whereDate('datetime', date('Y-m-d'))->select('status', DB::raw('count(status) as total_count'))->groupBy('status')->count();
    
    $totalVideos = Videorequest::count();
    $todayVideos = Videorequest::whereDate('created_at', date('Y-m-d'))->count();
    $pendingVideos = Videorequest::where(['status'=>'Pending'])->count();
    $ApprovedVideos = Videorequest::where(['status'=>'Approved'])->count();
    
    $ProjectWiseDataGraph = Videorequest::select('project_id', DB::raw('count(project_id) as total_count'))->groupBy('project_id')->get();
    
     $FaceWiseDataGraph = Videorequest::select('emp_id', DB::raw('count(emp_id) as total_count'))->groupBy('emp_id')->get();
     
     $ZOngCallingOutgoing = Zongcalling::where(['Type'=>'Outgoing Calls'])->sum('duration');
     $ZOngCallingIncomming = Zongcalling::where(['Type'=>'Incomming Calls'])->whereIn('status',['Missed','Answered'])->sum('duration');
      $ZOngCallingOutgoingToday = Zongcalling::where(['Type'=>'Outgoing Calls'])->whereDate('datetime', date('Y-m-d'))->sum('duration');
     $ZOngCallingIncommingToday = Zongcalling::where(['Type'=>'Incomming Calls'])->whereIn('status',['Missed','Answered'])->whereDate('datetime', date('Y-m-d'))->sum('duration');
     
     
     $week = []; 
    for ($i=0; $i <7 ; $i++) {
        
        $zongCallingIncommingDataWeekly = Zongcalling::where(['Type'=>'Incomming Calls'])->whereDate('datetime',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
        
        $zongCallingOutgoingDataWeekly = Zongcalling::where(['Type'=>'Outgoing Calls'])->whereDate('datetime',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
     
        $week[] = array(
         'format' => Carbon::now()->startOfWeek()->addDay($i)->format('l'),
         'datetime' => Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'),
         'incommingweekly'=>$zongCallingIncommingDataWeekly,
         'outgoingweekly'=>$zongCallingOutgoingDataWeekly,
        );
    }
    
    $callstatusreporting = Employee::where(['department_id'=>'1' ,'status'=>'active'])->get();
    
    $StatusRep = [];
    foreach($callstatusreporting as $empDatax){
        
         $ZOngCallingOutgoingTotal = Zongcalling::where(['Type'=>'Outgoing Calls','ext'=>$empDatax->child_num])->whereDate('datetime', date('Y-m-d'))->count();
         
          $ZOngCallingConnectedTotal = Zongcalling::where(['status'=>'Connected','ext'=>$empDatax->child_num])->whereDate('datetime', date('Y-m-d'))->count();
          
          $ZOngCallingAnsweredTotal = Zongcalling::where(['status'=>'Answered','ext'=>$empDatax->child_num])->whereDate('datetime', date('Y-m-d'))->count();
          
           $ZOngCallingMissedTotal = Zongcalling::where(['status'=>'Missed Call','ext'=>$empDatax->child_num])->whereDate('datetime', date('Y-m-d'))->count();
           
           $ZOngCallingCallbackYesTotal = Zongcalling::where(['CallBack'=>'yes','ext'=>$empDatax->child_num])->whereDate('datetime', date('Y-m-d'))->count();
           
           $ZOngCallingNotCallbackYesTotal = Zongcalling::where(['CallBack'=>'no','ext'=>$empDatax->child_num])->whereDate('datetime', date('Y-m-d'))->count();
           
            $ZOngCallingIncommingTotalToday = Zongcalling::where(['Type'=>'Incomming Calls','status'=>'Answered','ext'=>$empDatax->child_num])->whereDate('datetime', date('Y-m-d'))->sum('duration');
            
            $ZOngCallingOutgoingTotalToday = Zongcalling::where(['Type'=>'Outgoing Calls','ext'=>$empDatax->child_num])->whereDate('datetime', date('Y-m-d'))->sum('duration');
            
             $TOtalIncommTalk = $this->FloorAmount($ZOngCallingIncommingTotalToday);
              $TOtalOutgoingTalk = $this->FloorAmount($ZOngCallingOutgoingTotalToday);
              
              $totalTalktimeDuration+=$TOtalOutgoingTalk;
              $totalTalktimeIncDuration+=$ZOngCallingIncommingTotalToday;
              
              $ZOngCallingIncommingTotal = Zongcalling::where(['Type'=>'Incomming Calls','status'=>'Answered','ext'=>$empDatax->child_num])->whereDate('datetime', date('Y-m-d'))->sum('duration');
             
          $ZOngCallingYoutubeTotal = Zongcalling::where(['Source'=>'youtube','ext'=>$empDatax->child_num])->whereDate('datetime', date('Y-m-d'))->count();
          
          $ZOngCallingSocialMediaTotal = Zongcalling::where(['Source'=>'social media','ext'=>$empDatax->child_num])->whereDate('datetime', date('Y-m-d'))->count();
          
          $ZOngCallingPersonalTotal = Zongcalling::where(['Source'=>'personal-call','ext'=>$empDatax->child_num])->count();
        
        $StatusRep[]=array(
          'empName' => $empDatax->emp_name,
          'totalOutgoing' => $ZOngCallingOutgoingTotal,
          'totalConnected' => $ZOngCallingConnectedTotal,
          'followupcalls' => 0,
          'personalcalls' => $ZOngCallingPersonalTotal,
          'totalanswered' => $ZOngCallingAnsweredTotal,
          'totalmissed' => $ZOngCallingMissedTotal,
          'totalcallback' => $ZOngCallingCallbackYesTotal,
          'totalnotcallback' => $ZOngCallingNotCallbackYesTotal,
          'incTaLKTime' => $TOtalIncommTalk,
          'TotalYoutube' => $ZOngCallingYoutubeTotal,
          'TotalSocialMedia' => $ZOngCallingSocialMediaTotal,
          'TotalOutgoingTalktime' => $TOtalOutgoingTalk,
          'TotalIncommingData' => $ZOngCallingIncommingTotal,
        );
        
    }
    
    $DocumentType = DocumentType::where(['status'=>'1'])->orderBy('document_title', 'ASC')->get();
    $propertyType = propertyType::all();
    $Issuingauthority = Issuingauthority::where(['status'=>'1'])->get();
    $Purposeprop = Purposeprop::where(['status'=>'1'])->get();
    $stockNature = stockNature::where(['status'=>'1'])->get();
    $stockType = stockType::where(['status'=>'1'])->get();
    $Stockrecby = Stockrecby::where(['status'=>'1'])->get();
    $Stockdeliveredby = Stockdeliveredby::where(['status'=>'1'])->get();
    
    $AffiliateRegisterModule = AffiliateRegisterModule::all();
    
    $Inventoriesfilters = Inventoriesfilters::where(['status'=>'active'])->get();
    
    $TotalDealsDOne = Inventorysold::where(['status'=>'approved'])->count();
    $TodayDealsDOne = Inventorysold::where(['status'=>'approved'])->whereDate('created_at', date('Y-m-d'))->count();
    $apartmentType = natureProject::all();
    $AssetTypeData = AssetType::where(['status'=>'1'])->get();
    $AssetLifeData = AssetLife::where(['status'=>'1'])->get();
  
    
}else if($agentid == 'all' && !empty($teamObj)){
    
    $myArray2 = explode(',', $teamObj);
    $myArray = str_replace(array('[',']'),'',$myArray2);


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
    
   

    //Assign Leads Count
    $AssignLeadsTotal = ListAssignLeads::join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->join('employees','employees.id','=','leadsassigndatabase.agentid')->where(['leadsassigndatabase.shiftStatus'=>'0','employees.status'=>'active'])->where('leadsimportdatabase.clientProject','!=','')->select('leadsassigndatabase.leadid','leadsimportdatabase.id','leadsassigndatabase.agentid','employees.status')->whereIn('leadsassigndatabase.agentid',$myArray)->count();
    
    $AssignLeadsToday = ListAssignLeads::join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsassigndatabase.shiftStatus'=>'0'])->whereDate('leadsassigndatabase.publishAgentDate', date('Y-m-d'))->select('leadsassigndatabase.leadid','leadsimportdatabase.id','leadsassigndatabase.agentid')->whereIn('leadsassigndatabase.agentid',$myArray)->count();
    
    //My Leads Count
    $MyLeadCountAll = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'No'])->whereIn('agentid',$myArray)->count();
    $MyLeadCountToday = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'No'])->whereIn('agentid',$myArray)->whereDate('created_at', date('Y-m-d'))->count();
    
    //Dorment Leads Count
    $DormentLeadCountAll = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'Yes'])->whereIn('agentid',$myArray)->count();
    $DormentLeadCountToday = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'Yes'])->whereIn('agentid',$myArray)->whereDate('created_at', date('Y-m-d'))->count();

    //Hot Leads Count
    $HotCountAll = ListMyLead::where(['LeadStatus'=>'hot','ReasonStatus'=>'No'])->whereIn('agentid',$myArray)->count();
    $HotCountToday = ListMyLead::where(['LeadStatus'=>'hot','ReasonStatus'=>'No'])->whereIn('agentid',$myArray)->whereDate('updated_at', date('Y-m-d'))->count();

    //Not Interested COunt
    $NotinterestedCountAll = ListNotInterested::whereIn('agentid',$myArray)->count();
    $NotinterestedCountToday = ListNotInterested::whereDate('updated_at', date('Y-m-d'))->whereIn('agentid',$myArray)->count();

    //Customer Activity Count
    $cusACtivityCountAll = CustomerActivity::whereIn('agentid',$myArray)->count();
    $cusActivityCountToday = CustomerActivity::whereDate('updated_at', date('Y-m-d'))->whereIn('agentid',$myArray)->count();

    //Reminder Count
    $ReminderCountAll = AssignReminder::whereIn('agentid',$myArray)->count();
    $ReminderCountToday = AssignReminder::whereDate('updated_at', date('Y-m-d'))->whereIn('agentid',$myArray)->count();

    $PhoneCountToday = LeadImpression::where(['impType'=>'phone'])->whereDate('updated_at', date('Y-m-d'))->whereIn('agentid',$myArray)->count();
    $whatsappCountToday = LeadImpression::where(['impType'=>'whatsapp'])->whereDate('updated_at', date('Y-m-d'))->whereIn('agentid',$myArray)->count();
    $smsCountToday = LeadImpression::where(['impType'=>'sms'])->whereDate('updated_at', date('Y-m-d'))->whereIn('agentid',$myArray)->count();
    $emailCountToday = LeadImpression::where(['impType'=>'email'])->whereDate('updated_at', date('Y-m-d'))->whereIn('agentid',$myArray)->count();

    $untabbedLeadsAll=ListAssignLeads::whereDoesntHave('LeadImpression')->where(['shiftStatus'=>'0'])->whereIn('agentid',$myArray)->count();
    $untabbedLeadsToday=ListAssignLeads::whereDoesntHave('LeadImpression')->where(['shiftStatus'=>'0'])->whereIn('agentid',$myArray)->whereDate('updated_at', date('Y-m-d'))->count();

    $MyLeadMaleCountAll = ListMyLead::where(['Gender'=>'male'])->whereIn('agentid',$myArray)->count();
    $MyLeadFeMaleCountAll = ListMyLead::where(['Gender'=>'female'])->whereIn('agentid',$myArray)->count();
    
    $ReminderTodayData = AssignReminder::with(['getleads','getleads_home' => function ($query){
        $query->orderBy('id', 'desc')->get();
    } ,])->whereDate('reminderSet', date('Y-m-d'))->whereIn('agentid',$myArray)->get();
    
    $TransferLeadsCountAll = TranasferShareLeads::where(['statusType'=>'transfer'])->whereIn('fagent',$myArray)->count();
    
    $TodayTransferLeadsCount = TranasferShareLeads::where(['statusType'=>'transfer'])->whereIn('fagent',$myArray)->whereDate('updated_at', date('Y-m-d'))->count();
    
    
    $listAssignDashboard = ListAssignLeads::with(['getleads','getvirginleads','getreminder' => function ($query){
            $query->orderBy('id', 'desc')->get();
        } ,])->where(['shiftStatus'=>'0'])->whereIn('agentid',$myArray)->orderBy('id', 'desc')->skip(0)->take(5)->get();
        
        
    $untabbedLeadsAllGraph=ListAssignLeads::whereDoesntHave('LeadImpression')->where(['shiftStatus'=>'0'])->join('leadsimportdatabase', 'leadsimportdatabase.id', '=', 'leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->select('clientProject', DB::raw('count(clientProject) as total_count'))->groupBy('clientProject')->get();
    
   
    $empData = Employee::where(['id'=>$agentid])->first();
    
    $zongDumy = array(array("Type"=>"Incomming Calls","total_count"=>0),array("Type"=>"Outgoing Calls","total_count"=>0));
    
    
    
    $week = []; 
    for ($i=0; $i <7 ; $i++) {
        
        $zongCallingIncommingDataWeekly = Zongcalling::where(['Type'=>'Incomming Calls'])->whereDate('datetime',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
        
        $zongCallingOutgoingDataWeekly = Zongcalling::where(['Type'=>'Outgoing Calls'])->whereDate('datetime',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
     
        $week[] = array(
         'format' => Carbon::now()->startOfWeek()->addDay($i)->format('l'),
         'datetime' => Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'),
         'incommingweekly'=>$zongCallingIncommingDataWeekly,
         'outgoingweekly'=>$zongCallingOutgoingDataWeekly,
        );
    }
    
   
 
    if(empty($empData)){
        $zongCallingData=$zongDumy;
    }else{
         $zongCallingData = Zongcalling::where(['ext'=>$empData->child_num])->select('Type', DB::raw('count(Type) as total_count'))->groupBy('Type')->get();
    }
    
    
    $CallingCountToday = Zongcalling::whereDate('updated_at', date('Y-m-d'))->whereIn('ext',$mergerInOut)->count();
    $CallingCountTotal = Zongcalling::whereIn('ext',$mergerInOut)->count();
    
    $TotalOutgoingCallData = Zongcalling::where(['Type'=>'Outgoing Calls'])->whereIn('ext',$mergerInOut)->select('Type', DB::raw('count(Type) as total_count'))->groupBy('Type')->count();
    
    $TodayOutgoingCallData = Zongcalling::where(['Type'=>'Outgoing Calls'])->whereDate('datetime', date('Y-m-d'))->whereIn('ext',$mergerInOut)->select('Type', DB::raw('count(Type) as total_count'))->groupBy('Type')->count();
    
    $TotalIncommingCallData = Zongcalling::where(['Type'=>'Incomming Calls'])->whereIn('ext',$mergerInOut)->select('Type', DB::raw('count(Type) as total_count'))->groupBy('Type')->count();
    
    $TodayIncommingCallData = Zongcalling::where(['Type'=>'Incomming Calls'])->whereIn('ext',$mergerInOut)->whereDate('datetime', date('Y-m-d'))->select('Type', DB::raw('count(Type) as total_count'))->groupBy('Type')->count();
    
    $TotalConnectedCallData = Zongcalling::where(['status'=>'Connected'])->whereIn('ext',$mergerInOut)->select('status', DB::raw('count(status) as total_count'))->groupBy('status')->count();
    $TodayConnectedCallData = Zongcalling::where(['status'=>'Connected'])->whereIn('ext',$mergerInOut)->whereDate('datetime', date('Y-m-d'))->select('status', DB::raw('count(status) as total_count'))->groupBy('status')->count();
    
    $TotalMissedCallData = Zongcalling::where(['status'=>'Missed'])->whereIn('ext',$mergerInOut)->select('status', DB::raw('count(status) as total_count'))->groupBy('status')->count();
    $TodayMissedCallData = Zongcalling::where(['status'=>'Missed'])->whereIn('ext',$mergerInOut)->whereDate('datetime', date('Y-m-d'))->select('status', DB::raw('count(status) as total_count'))->groupBy('status')->count();
    
    $TotalAnsweredCallData = Zongcalling::where(['status'=>'Answered'])->whereIn('ext',$mergerInOut)->select('status', DB::raw('count(status) as total_count'))->groupBy('status')->count();
    $TodayAnsweredCallData = Zongcalling::where(['status'=>'Answered'])->whereIn('ext',$mergerInOut)->whereDate('datetime', date('Y-m-d'))->select('status', DB::raw('count(status) as total_count'))->groupBy('status')->count();
    
    
    $totalVideos = Videorequest::count();
    $todayVideos = Videorequest::whereDate('created_at', date('Y-m-d'))->count();
    $pendingVideos = Videorequest::where(['status'=>'Pending'])->count();
    $ApprovedVideos = Videorequest::where(['status'=>'Approved'])->count();
    
    $ProjectWiseDataGraph = Videorequest::select('project_id', DB::raw('count(project_id) as total_count'))->groupBy('project_id')->get();
    
    $FaceWiseDataGraph = Videorequest::select('emp_id', DB::raw('count(emp_id) as total_count'))->groupBy('emp_id')->get();
    
    $empAll = Employee::all();
    
    $ZOngCallingOutgoing = Zongcalling::where(['Type'=>'Outgoing Calls'])->whereIn('ext',$mergerInOut)->sum('duration');
    
    $ZOngCallingIncomming = Zongcalling::where(['Type'=>'Incomming Calls'])->whereIn('ext',$mergerInOut)->whereIn('status',['Missed','Answered'])->sum('duration');
     $ZOngCallingOutgoingToday = Zongcalling::where(['Type'=>'Outgoing Calls'])->whereIn('ext',$mergerInOut)->whereDate('datetime', date('Y-m-d'))->sum('duration');
     $ZOngCallingIncommingToday = Zongcalling::where(['Type'=>'Incomming Calls'])->whereIn('ext',$mergerInOut)->whereIn('status',['Missed','Answered'])->whereDate('datetime', date('Y-m-d'))->sum('duration');
    
    $callstatusreporting = Employee::where(['department_id'=>'1' ,'status'=>'active'])->whereIn('id',$myArray)->get();
    
    $StatusRep = [];
    
    $q = $request->q ?? date('Y-m-d');
    $start =$request->start ?? date('Y-m-d');
    $end =$request->end ?? date('Y-m-d');
    
    $totalOutgoingAll = 0;
    $totaConnectedAll = 0;
    $totaFollowupAll = 0;
    $totaPersonalCallAll = 0;
    $totaIncommingCallAll = 0;
    $totaIncommingAnsweredCallAll = 0;
     $totaIncommingMissedCallAll = 0;
      $totaCallbackCallAll = 0;
      $totaCallbackNotCallAll = 0;
      $totaYoutubeCallAll = 0;
      $totaSocialMediaCallAll = 0;
      
      $totalTalktimeDuration = 0;
      $totalTalktimeIncDuration = 0;
    
    foreach($callstatusreporting as $empDatax){
          
            $ZOngCallingOutgoingTotal = Zongcalling::where(['Type'=>'Outgoing Calls','ext'=>$empDatax->outgoingPhone])->whereIn('ext',$mergerInOut)->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->groupBy('phone')->get()->count();
         
            $totalOutgoingAll +=$ZOngCallingOutgoingTotal;
         
          $ZOngCallingConnectedTotal = Zongcalling::where(['status'=>'Connected','ext'=>$empDatax->outgoingPhone])->whereIn('ext',$mergerInOut)->select('phone', DB::raw('count(phone)'))->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->groupBy('phone')->get()->count();
          
          $totaConnectedAll += $ZOngCallingConnectedTotal;
        
          
          $ZOngCallingAnsweredTotal = Zongcalling::where(['status'=>'Answered','ext'=>$empDatax->incommingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->select('phone', DB::raw('count(phone)'))->groupBy('phone')->get()->count();
          
          $totaIncommingAnsweredCallAll += $ZOngCallingAnsweredTotal;
          
           $ZOngCallingMissedTotal = Zongcalling::where(['status'=>'Missed','ext'=>$empDatax->incommingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->select('phone', DB::raw('count(phone)'))->groupBy('phone')->get()->count();
           
           $totaIncommingMissedCallAll +=$ZOngCallingMissedTotal;
           
           $ZOngCallingCallbackYesTotal = Zongcalling::where(['CallBack'=>'yes'])->whereIn('ext',[$empDatax->incommingPhone,$empDatax->outgoingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->count();
           
           $totaCallbackCallAll+=$ZOngCallingCallbackYesTotal;
           
           $ZOngCallingNotCallbackYesTotal = Zongcalling::where(['CallBack'=>'no'])->whereIn('ext',[$empDatax->incommingPhone,$empDatax->outgoingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->count();
           
           $totaCallbackNotCallAll+=$ZOngCallingNotCallbackYesTotal;
           
            $ZOngCallingIncommingTotalToday = Zongcalling::where(['Type'=>'Incomming Calls','status'=>'Answered','ext'=>$empDatax->incommingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->sum('duration');
            
            $ZOngCallingOutgoingTotalToday = Zongcalling::where(['Type'=>'Outgoing Calls','ext'=>$empDatax->outgoingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->sum('duration');
            // dd($ZOngCallingOutgoingTotalToday);
             $TOtalIncommTalk = $this->FloorAmount($ZOngCallingIncommingTotalToday);
              $TOtalOutgoingTalk = $this->FloorAmount($ZOngCallingOutgoingTotalToday);
              
              $totalTalktimeDuration+=$ZOngCallingOutgoingTotalToday;
              
              $totalTalktimeIncDuration+=$ZOngCallingIncommingTotalToday;
              
              
              
              $ZOngCallingIncommingTotal = Zongcalling::where(['Type'=>'Incomming Calls','ext'=>$empDatax->incommingPhone])->whereIn('status',['Answered','Missed'])->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->select('phone', DB::raw('count(phone)'))->groupBy('phone')->get()->count();
              
              $totaIncommingCallAll +=$ZOngCallingIncommingTotal;
              
             
          $ZOngCallingYoutubeTotal = Zongcalling::where(['Source'=>'youtube'])->whereIn('ext',[$empDatax->incommingPhone,$empDatax->outgoingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->count();
          
            $totaYoutubeCallAll+=$ZOngCallingYoutubeTotal;
          
          $ZOngCallingSocialMediaTotal = Zongcalling::where(['Source'=>'social media'])->whereIn('ext',[$empDatax->incommingPhone,$empDatax->outgoingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->select('phone', DB::raw('count(phone)'))->groupBy('phone')->get()->count();
          
        $totaSocialMediaCallAll+=$ZOngCallingSocialMediaTotal;
          
           $ZOngCallingPersonalTotal = Zongcalling::where(['Source'=>'personal-call'])->whereIn('ext',[$empDatax->incommingPhone,$empDatax->outgoingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->count();
           
           $totaPersonalCallAll += $ZOngCallingPersonalTotal;
           
             $ZOngCallingFollowupsTotal = Zongcalling::where(['Source'=>'follow-up'])->whereIn('ext',[$empDatax->incommingPhone,$empDatax->outgoingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->count();
        
            $totaFollowupAll += $ZOngCallingFollowupsTotal;
        
        $StatusRep[]=array(
          'empName' => $empDatax->emp_name,
          'totalOutgoing' => $ZOngCallingOutgoingTotal,
          'totalConnected' => $ZOngCallingConnectedTotal,
          'followupcalls' => $ZOngCallingFollowupsTotal,
          'personalcalls' =>$ZOngCallingPersonalTotal,
          'totalanswered' => $ZOngCallingAnsweredTotal,
          'totalmissed' => $ZOngCallingIncommingTotal - $ZOngCallingAnsweredTotal,
          'totalcallback' => $ZOngCallingCallbackYesTotal,
          'totalnotcallback' => $ZOngCallingNotCallbackYesTotal,
          'incTaLKTime' => $TOtalIncommTalk,
          'TotalYoutube' => $ZOngCallingYoutubeTotal,
          'TotalSocialMedia' => $ZOngCallingSocialMediaTotal,
          'TotalOutgoingTalktime' => $TOtalOutgoingTalk,
          'TotalIncommingData' => $ZOngCallingIncommingTotal,
        );
        
    }
    
    $DocumentType = DocumentType::where(['status'=>'1'])->orderBy('document_title', 'ASC')->get();
    $propertyType = propertyType::where(['approved'=>'yes'])->orderBy('type_name', 'ASC')->get();
    $Issuingauthority = Issuingauthority::where(['status'=>'1'])->orderBy('issuingTitle', 'ASC')->get();
    $Purposeprop = Purposeprop::where(['status'=>'1'])->orderBy('purposetitle', 'ASC')->get();
    
    $stockNature = stockNature::where(['status'=>'1'])->get();
    $stockType = stockType::where(['status'=>'1'])->get();
    $Stockrecby = Stockrecby::where(['status'=>'1'])->get();
    $Stockdeliveredby = Stockdeliveredby::where(['status'=>'1'])->get();
    
    $AffiliateRegisterModule = AffiliateRegisterModule::all();
    $Inventoriesfilters = Inventoriesfilters::where(['status'=>'active'])->get();
    
    $TotalDealsDOne = Inventorysold::where(['status'=>'approved'])->whereIn('employee_id',$myArray)->count();
    $TodayDealsDOne = Inventorysold::where(['status'=>'approved'])->whereIn('employee_id',$myArray)->whereDate('created_at', date('Y-m-d'))->count();
    $apartmentType = natureProject::all();
    
    $AssetTypeData = AssetType::where(['status'=>'1'])->get();
    $AssetLifeData = AssetLife::where(['status'=>'1'])->get();
    
}else if($agentid == 'all' && empty($teamObj)){
    
    
    
    //Assign Leads Count
    $AssignLeadsTotal = ListAssignLeads::join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->join('employees','employees.id','=','leadsassigndatabase.agentid')->where(['leadsassigndatabase.shiftStatus'=>'0','employees.status'=>'active'])->where('leadsimportdatabase.clientProject','!=','')->select('leadsassigndatabase.leadid','leadsimportdatabase.id','leadsassigndatabase.agentid','employees.status')->count();
    
    $AssignLeadsToday = ListAssignLeads::join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsassigndatabase.shiftStatus'=>'0'])->whereDate('leadsassigndatabase.publishAgentDate', date('Y-m-d'))->select('leadsassigndatabase.leadid','leadsimportdatabase.id','leadsassigndatabase.agentid')->count();

    //My Leads Count
    $MyLeadCountAll = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'No'])->count();
    
    $MyLeadCountToday = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'No'])->whereDate('created_at', date('Y-m-d'))->count();
    
    //Dorment Leads Count
    $DormentLeadCountAll = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'Yes'])->count();
    $DormentLeadCountToday = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'Yes'])->whereDate('created_at', date('Y-m-d'))->count();

    //Hot Leads Count
    $HotCountAll = ListMyLead::where(['LeadStatus'=>'hot','ReasonStatus'=>'No'])->count();
    $HotCountToday = ListMyLead::where(['LeadStatus'=>'hot','ReasonStatus'=>'No'])->whereDate('updated_at', date('Y-m-d'))->count();

    //Not Interested COunt
    $NotinterestedCountAll = ListNotInterested::count();
    $NotinterestedCountToday = ListNotInterested::whereDate('updated_at', date('Y-m-d'))->count();

    //Customer Activity Count
    $cusACtivityCountAll = CustomerActivity::count();
    $cusActivityCountToday = CustomerActivity::whereDate('updated_at', date('Y-m-d'))->count();

    //Reminder Count
    $ReminderCountAll = AssignReminder::count();
    $ReminderCountToday = AssignReminder::whereDate('updated_at', date('Y-m-d'))->count();

    $PhoneCountToday = LeadImpression::where(['impType'=>'phone'])->whereDate('updated_at', date('Y-m-d'))->count();
    $whatsappCountToday = LeadImpression::where(['impType'=>'whatsapp'])->whereDate('updated_at', date('Y-m-d'))->count();
    $smsCountToday = LeadImpression::where(['impType'=>'sms'])->whereDate('updated_at', date('Y-m-d'))->count();
    $emailCountToday = LeadImpression::where(['impType'=>'email'])->whereDate('updated_at', date('Y-m-d'))->count();

    $untabbedLeadsAll=ListAssignLeads::whereDoesntHave('LeadImpression')->where(['shiftStatus'=>'0'])->count();
    $untabbedLeadsToday=ListAssignLeads::whereDoesntHave('LeadImpression')->where(['shiftStatus'=>'0'])->whereDate('updated_at', date('Y-m-d'))->count();

    $MyLeadMaleCountAll = ListMyLead::where(['Gender'=>'male'])->count();
    $MyLeadFeMaleCountAll = ListMyLead::where(['Gender'=>'female'])->count();
    
    $ReminderTodayData = AssignReminder::with(['getleads','getleads_home' => function ($query){
        $query->orderBy('id', 'desc')->get();
    } ,])->whereDate('reminderSet', date('Y-m-d'))->get();
    
    $TransferLeadsCountAll = TranasferShareLeads::where(['statusType'=>'transfer'])->count();
    
    $TodayTransferLeadsCount = TranasferShareLeads::where(['statusType'=>'transfer'])->whereDate('updated_at', date('Y-m-d'))->count();
    
    
    $listAssignDashboard = ListAssignLeads::with(['getleads','getvirginleads','getreminder' => function ($query){
            $query->orderBy('id', 'desc')->get();
        } ,])->where(['shiftStatus'=>'0'])->orderBy('id', 'desc')->skip(0)->take(5)->get();
        
        
    $untabbedLeadsAllGraph=ListAssignLeads::whereDoesntHave('LeadImpression')->where(['shiftStatus'=>'0'])->join('leadsimportdatabase', 'leadsimportdatabase.id', '=', 'leadsassigndatabase.leadid')->select('clientProject', DB::raw('count(clientProject) as total_count'))->groupBy('clientProject')->get();
    
   
    $empData = Employee::where(['id'=>$agentid])->first();
    
    $zongDumy = array(array("Type"=>"Incomming Calls","total_count"=>0),array("Type"=>"Outgoing Calls","total_count"=>0));
    
    $week = []; 
    for ($i=0; $i <7 ; $i++) {
        
        $zongCallingIncommingDataWeekly = Zongcalling::where(['Type'=>'Incomming Calls'])->whereDate('datetime',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
        
        $zongCallingOutgoingDataWeekly = Zongcalling::where(['Type'=>'Outgoing Calls'])->whereDate('datetime',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
     
        $week[] = array(
         'format' => Carbon::now()->startOfWeek()->addDay($i)->format('l'),
         'datetime' => Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'),
         'incommingweekly'=>$zongCallingIncommingDataWeekly,
         'outgoingweekly'=>$zongCallingOutgoingDataWeekly,
        );
    }
    
   
 
    if(empty($empData->child_num)){
        $zongCallingData=$zongDumy;
    }else{
         $zongCallingData = Zongcalling::where(['ext'=>$empData->child_num])->select('Type', DB::raw('count(Type) as total_count'))->groupBy('Type')->get();
    }
    
    
    $CallingCountToday = Zongcalling::whereDate('updated_at', date('Y-m-d'))->count();
    $CallingCountTotal = Zongcalling::count();
    
    $TotalOutgoingCallData = Zongcalling::where(['Type'=>'Outgoing Calls'])->select('Type', DB::raw('count(Type) as total_count'))->groupBy('Type')->count();
    
    $TodayOutgoingCallData = Zongcalling::where(['Type'=>'Outgoing Calls'])->whereDate('datetime', date('Y-m-d'))->select('Type', DB::raw('count(Type) as total_count'))->groupBy('Type')->count();
    
    $TotalIncommingCallData = Zongcalling::where(['Type'=>'Incomming Calls'])->select('Type', DB::raw('count(Type) as total_count'))->groupBy('Type')->count();
    
    $TodayIncommingCallData = Zongcalling::where(['Type'=>'Incomming Calls'])->whereDate('datetime', date('Y-m-d'))->select('Type', DB::raw('count(Type) as total_count'))->groupBy('Type')->count();
    
    $TotalConnectedCallData = Zongcalling::where(['status'=>'Connected'])->select('status', DB::raw('count(status) as total_count'))->groupBy('status')->count();
    $TodayConnectedCallData = Zongcalling::where(['status'=>'Connected'])->whereDate('datetime', date('Y-m-d'))->select('status', DB::raw('count(status) as total_count'))->groupBy('status')->count();
    
    $TotalMissedCallData = Zongcalling::where(['status'=>'Missed'])->select('status', DB::raw('count(status) as total_count'))->groupBy('status')->count();
    $TodayMissedCallData = Zongcalling::where(['status'=>'Missed'])->whereDate('datetime', date('Y-m-d'))->select('status', DB::raw('count(status) as total_count'))->groupBy('status')->count();
    
    $TotalAnsweredCallData = Zongcalling::where(['status'=>'Answered'])->select('status', DB::raw('count(status) as total_count'))->groupBy('status')->count();
    $TodayAnsweredCallData = Zongcalling::where(['status'=>'Answered'])->whereDate('datetime', date('Y-m-d'))->select('status', DB::raw('count(status) as total_count'))->groupBy('status')->count();
    
    
    $totalVideos = Videorequest::count();
    $todayVideos = Videorequest::whereDate('created_at', date('Y-m-d'))->count();
    $pendingVideos = Videorequest::where(['status'=>'Pending'])->count();
    $ApprovedVideos = Videorequest::where(['status'=>'Approved'])->count();
    
    $ProjectWiseDataGraph = Videorequest::select('project_id', DB::raw('count(project_id) as total_count'))->groupBy('project_id')->get();
    
    $FaceWiseDataGraph = Videorequest::select('emp_id', DB::raw('count(emp_id) as total_count'))->groupBy('emp_id')->get();
    
    $empAll = Employee::all();
    
    $ZOngCallingOutgoing = Zongcalling::where(['Type'=>'Outgoing Calls'])->sum('duration');
    
    $ZOngCallingIncomming = Zongcalling::where(['Type'=>'Incomming Calls'])->whereIn('status',['Missed','Answered'])->sum('duration');
     $ZOngCallingOutgoingToday = Zongcalling::where(['Type'=>'Outgoing Calls'])->whereDate('datetime', date('Y-m-d'))->sum('duration');
     $ZOngCallingIncommingToday = Zongcalling::where(['Type'=>'Incomming Calls'])->whereIn('status',['Missed','Answered'])->whereDate('datetime', date('Y-m-d'))->sum('duration');
    
    $callstatusreporting = Employee::where(['department_id'=>'1' ,'status'=>'active'])->get();
    
    $StatusRep = [];
    
    $q = $request->q ?? date('Y-m-d');
    $start =$request->start ?? date('Y-m-d');
    $end =$request->end ?? date('Y-m-d');
    
    $totalOutgoingAll = 0;
    $totaConnectedAll = 0;
    $totaFollowupAll = 0;
    $totaPersonalCallAll = 0;
    $totaIncommingCallAll = 0;
    $totaIncommingAnsweredCallAll = 0;
     $totaIncommingMissedCallAll = 0;
      $totaCallbackCallAll = 0;
      $totaCallbackNotCallAll = 0;
      $totaYoutubeCallAll = 0;
      $totaSocialMediaCallAll = 0;
      $totalTalktimeDuration = 0;
      $totalTalktimeIncDuration = 0;
    
    foreach($callstatusreporting as $empDatax){
          
            $ZOngCallingOutgoingTotal = Zongcalling::where(['Type'=>'Outgoing Calls','ext'=>$empDatax->outgoingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->groupBy('phone')->get()->count();
         
            $totalOutgoingAll +=$ZOngCallingOutgoingTotal;
         
          $ZOngCallingConnectedTotal = Zongcalling::where(['status'=>'Connected','ext'=>$empDatax->outgoingPhone])->select('phone', DB::raw('count(phone)'))->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->groupBy('phone')->get()->count();
          
          $totaConnectedAll += $ZOngCallingConnectedTotal;
        
          
          $ZOngCallingAnsweredTotal = Zongcalling::where(['status'=>'Answered','ext'=>$empDatax->incommingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->select('phone', DB::raw('count(phone)'))->groupBy('phone')->get()->count();
          
          $totaIncommingAnsweredCallAll += $ZOngCallingAnsweredTotal;
          
           $ZOngCallingMissedTotal = Zongcalling::where(['status'=>'Missed','ext'=>$empDatax->incommingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->select('phone', DB::raw('count(phone)'))->groupBy('phone')->get()->count();
           
           $totaIncommingMissedCallAll +=$ZOngCallingMissedTotal;
           
          $ZOngCallingCallbackYesTotal = Zongcalling::where(['CallBack'=>'yes'])->whereIn('ext',[$empDatax->incommingPhone,$empDatax->outgoingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->count();
            
          
           
           $totaCallbackCallAll+=$ZOngCallingCallbackYesTotal;
           
           $ZOngCallingNotCallbackYesTotal = Zongcalling::where(['CallBack'=>'no'])->whereIn('ext',[$empDatax->incommingPhone,$empDatax->outgoingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->count();
           
           $totaCallbackNotCallAll+=$ZOngCallingNotCallbackYesTotal;
           
            $ZOngCallingIncommingTotalToday = Zongcalling::where(['Type'=>'Incomming Calls','status'=>'Answered','ext'=>$empDatax->incommingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->sum('duration');
            
            $ZOngCallingOutgoingTotalToday = Zongcalling::where(['Type'=>'Outgoing Calls','ext'=>$empDatax->outgoingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->sum('duration');
            
             $TOtalIncommTalk = $this->FloorAmount($ZOngCallingIncommingTotalToday);
              $TOtalOutgoingTalk = $this->FloorAmount($ZOngCallingOutgoingTotalToday);
              
              $totalTalktimeDuration+=$ZOngCallingOutgoingTotalToday;
              
              $totalTalktimeIncDuration+=$ZOngCallingIncommingTotalToday;
              
              $ZOngCallingIncommingTotal = Zongcalling::where(['Type'=>'Incomming Calls','ext'=>$empDatax->incommingPhone])->whereIn('status',['Answered','Missed'])->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->select('phone', DB::raw('count(phone)'))->groupBy('phone')->get()->count();
              
              $totaIncommingCallAll +=$ZOngCallingIncommingTotal;
              
             
          $ZOngCallingYoutubeTotal = Zongcalling::where(['Source'=>'youtube'])->whereIn('ext',[$empDatax->incommingPhone,$empDatax->outgoingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->count();
          
            $totaYoutubeCallAll+=$ZOngCallingYoutubeTotal;
          
          $ZOngCallingSocialMediaTotal = Zongcalling::where(['Source'=>'social media'])->whereIn('ext',[$empDatax->incommingPhone,$empDatax->outgoingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->select('phone', DB::raw('count(phone)'))->groupBy('phone')->get()->count();
          
        $totaSocialMediaCallAll+=$ZOngCallingSocialMediaTotal;
          
           $ZOngCallingPersonalTotal = Zongcalling::where(['Source'=>'personal-call'])->whereIn('ext',[$empDatax->incommingPhone,$empDatax->outgoingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->count();
           
           $totaPersonalCallAll += $ZOngCallingPersonalTotal;
           
             $ZOngCallingFollowupsTotal = Zongcalling::where(['Source'=>'follow-up'])->whereIn('ext',[$empDatax->incommingPhone,$empDatax->outgoingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($start,$end))->count();
        
            $totaFollowupAll += $ZOngCallingFollowupsTotal;
        
        $StatusRep[]=array(
          'empName' => $empDatax->emp_name,
          'totalOutgoing' => $ZOngCallingOutgoingTotal,
          'totalConnected' => $ZOngCallingConnectedTotal,
          'followupcalls' => $ZOngCallingFollowupsTotal,
          'personalcalls' =>$ZOngCallingPersonalTotal,
          'totalanswered' => $ZOngCallingAnsweredTotal,
          'totalmissed' => $ZOngCallingIncommingTotal - $ZOngCallingAnsweredTotal,
          'totalcallback' => $ZOngCallingCallbackYesTotal,
          'totalnotcallback' => $ZOngCallingNotCallbackYesTotal,
          'incTaLKTime' => $TOtalIncommTalk,
          'TotalYoutube' => $ZOngCallingYoutubeTotal,
          'TotalSocialMedia' => $ZOngCallingSocialMediaTotal,
          'TotalOutgoingTalktime' => $TOtalOutgoingTalk,
          'TotalIncommingData' => $ZOngCallingIncommingTotal,
        );
        
    }
    
    $DocumentType = DocumentType::where(['status'=>'1'])->orderBy('document_title', 'ASC')->get();
    $propertyType = propertyType::where(['approved'=>'yes'])->orderBy('type_name', 'ASC')->get();
    $Issuingauthority = Issuingauthority::where(['status'=>'1'])->orderBy('issuingTitle', 'ASC')->get();
    $Purposeprop = Purposeprop::where(['status'=>'1'])->orderBy('purposetitle', 'ASC')->get();
    
    $stockNature = stockNature::where(['status'=>'1'])->get();
    $stockType = stockType::where(['status'=>'1'])->get();
    $Stockrecby = Stockrecby::where(['status'=>'1'])->get();
    $Stockdeliveredby = Stockdeliveredby::where(['status'=>'1'])->get();
    
    $AffiliateRegisterModule = AffiliateRegisterModule::all();
    $Inventoriesfilters = Inventoriesfilters::where(['status'=>'active'])->get();
    
    $TotalDealsDOne = Inventorysold::where(['status'=>'approved'])->count();
    $TodayDealsDOne = Inventorysold::where(['status'=>'approved'])->whereDate('created_at', date('Y-m-d'))->count();
    $apartmentType = natureProject::all();
    
    $AssetTypeData = AssetType::where(['status'=>'1'])->get();
    $AssetLifeData = AssetLife::where(['status'=>'1'])->get();
    
}else{
    
    
    $totalOutgoingAll = 0;
    $totaConnectedAll = 0;
    $totaFollowupAll = 0;
      $totaPersonalCallAll = 0;
          $totaIncommingCallAll = 0;
    $totaIncommingAnsweredCallAll = 0;
     $totaIncommingMissedCallAll = 0;
      $totaCallbackCallAll = 0;
      $totaCallbackNotCallAll = 0;
      $totaYoutubeCallAll = 0;
      $totaSocialMediaCallAll = 0;
      $totalTalktimeDuration=0;
      $totalTalktimeIncDuration = 0;
    //Assign Leads Count
    $AssignLeadsTotal = ListAssignLeads::join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsassigndatabase.agentid'=>$agentid])->select('leadsassigndatabase.leadid','leadsimportdatabase.id','leadsassigndatabase.agentid')->count();

    $AssignLeadsToday = ListAssignLeads::join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsassigndatabase.agentid'=>$agentid,'leadsassigndatabase.shiftStatus'=>'0'])->whereDate('leadsassigndatabase.publishAgentDate', date('Y-m-d'))->select('leadsassigndatabase.leadid','leadsimportdatabase.id','leadsassigndatabase.agentid')->count();
    
    //My Leads Count
    $MyLeadCountAll = ListMyLead::where(['agentid'=>$agentid,'LeadStatus'=>'My Lead','DormentStatus'=>'No' , 'ReasonStatus' => 'No'])->count();
    
    $MyLeadCountToday = ListMyLead::where(['agentid'=>$agentid,'LeadStatus'=>'My Lead','DormentStatus'=>'No' , 'ReasonStatus' => 'No'])->whereDate('created_at', date('Y-m-d'))->count();
    
    //Dorment Leads Count
    $DormentLeadCountAll = ListMyLead::where(['agentid'=>$agentid,'LeadStatus'=>'My Lead','DormentStatus'=>'Yes'])->count();
    $DormentLeadCountToday = ListMyLead::where(['agentid'=>$agentid,'LeadStatus'=>'My Lead','DormentStatus'=>'Yes'])->whereDate('created_at', date('Y-m-d'))->count();

    //Hot Leads Count
    $HotCountAll = ListMyLead::where(['agentid'=>$agentid,'LeadStatus'=>'hot','ReasonStatus'=>'No' , 'ReasonStatus' => 'No'])->count();
    $HotCountToday = ListMyLead::where(['agentid'=>$agentid,'LeadStatus'=>'hot','ReasonStatus'=>'No' , 'ReasonStatus' => 'No'])->whereDate('updated_at', date('Y-m-d'))->count();

    //Not Interested COunt
    $NotinterestedCountAll = ListNotInterested::where(['agentid'=>$agentid])->count();
    $NotinterestedCountToday = ListNotInterested::where(['agentid'=>$agentid])->whereDate('updated_at', date('Y-m-d'))->count();

    //Customer Activity Count
    $cusACtivityCountAll = CustomerActivity::where(['agentid'=>$agentid])->count();
    $cusActivityCountToday = CustomerActivity::where(['agentid'=>$agentid])->whereDate('updated_at', date('Y-m-d'))->count();

    //Reminder Count
    $ReminderCountAll = AssignReminder::where(['agentid'=>$agentid])->count();
    $ReminderCountToday = AssignReminder::where(['agentid'=>$agentid])->whereDate('updated_at', date('Y-m-d'))->count();

    $PhoneCountToday = LeadImpression::where(['agentid'=>$agentid,'impType'=>'phone'])->whereDate('updated_at', date('Y-m-d'))->count();
    $whatsappCountToday = LeadImpression::where(['agentid'=>$agentid,'impType'=>'whatsapp'])->whereDate('updated_at', date('Y-m-d'))->count();
    $smsCountToday = LeadImpression::where(['agentid'=>$agentid,'impType'=>'sms'])->whereDate('updated_at', date('Y-m-d'))->count();
    $emailCountToday = LeadImpression::where(['agentid'=>$agentid,'impType'=>'email'])->whereDate('updated_at', date('Y-m-d'))->count();

    $untabbedLeadsAll=ListAssignLeads::whereDoesntHave('LeadImpression')->where(['shiftStatus'=>'0','agentid'=>$agentid])->count();
    $untabbedLeadsToday=ListAssignLeads::whereDoesntHave('LeadImpression')->where(['shiftStatus'=>'0','agentid'=>$agentid])->whereDate('updated_at', date('Y-m-d'))->count();

    $MyLeadMaleCountAll = ListMyLead::where(['agentid'=>$agentid,'Gender'=>'male'])->count();
    
    $MyLeadFeMaleCountAll = ListMyLead::where(['agentid'=>$agentid,'Gender'=>'female'])->count();
    
    
     $ReminderTodayData = AssignReminder::with(['getleads','getleads_home' => function ($query){
        $query->orderBy('id', 'desc')->get();
    } ,])->where(['agentid'=>$agentid])->whereDate('reminderSet', date('Y-m-d'))->get();
    
    $TransferLeadsCountAll = TranasferShareLeads::where(['fagent'=>$agentid,'statusType'=>'transfer'])->count();
    
    $TodayTransferLeadsCount = TranasferShareLeads::where(['fagent'=>$agentid,'statusType'=>'transfer'])->whereDate('updated_at', date('Y-m-d'))->count();
    
    
    $listAssignDashboard = ListAssignLeads::with(['getleads','getvirginleads','getreminder' => function ($query){
            $query->orderBy('id', 'desc')->get();
        } ,])->where(['agentid'=>$agentid,'shiftStatus'=>'0'])->orderBy('id', 'desc')->skip(0)->take(5)->get();
     
    
    $untabbedLeadsAllGraph=ListAssignLeads::whereDoesntHave('LeadImpression')->where(['agentid'=>$agentid,'shiftStatus'=>'0'])->join('leadsimportdatabase', 'leadsimportdatabase.id', '=', 'leadsassigndatabase.leadid')->select('clientProject', DB::raw('count(clientProject) as total_count'))->groupBy('clientProject')->get();
    
    
    $empData = Employee::where(['id'=>$agentid])->first();
    
    $zongDumy = array(array("Type"=>"Incomming Calls","total_count"=>0),array("Type"=>"Outgoing Calls","total_count"=>0));
    
   
 
    if(empty($empData->child_num)){
        $zongCallingData=$zongDumy;
    }else{
         $zongCallingData = Zongcalling::where(['ext'=>$empData->child_num])->select('Type', DB::raw('count(Type) as total_count'))->groupBy('Type')->get();
    }
    
    
    $CallingCountToday = Zongcalling::where(['ext'=>$empData->child_num])->whereDate('updated_at', date('Y-m-d'))->count();
    $CallingCountTotal = Zongcalling::where(['ext'=>$empData->child_num])->count();
    
    $TotalOutgoingCallData = Zongcalling::where(['Type'=>'Outgoing Calls','ext'=>$empData->outgoingPhone])->select('Type', DB::raw('count(Type) as total_count'))->groupBy('Type')->count();
    
    $TodayOutgoingCallData = Zongcalling::where(['Type'=>'Outgoing Calls','ext'=>$empData->outgoingPhone])->whereDate('datetime', date('Y-m-d'))->select('Type', DB::raw('count(Type) as total_count'))->groupBy('Type')->count();
    
    $TotalIncommingCallData = Zongcalling::where(['Type'=>'Incomming Calls','ext'=>$empData->incommingPhone])->select('Type', DB::raw('count(Type) as total_count'))->groupBy('Type')->count();
    
    $TodayIncommingCallData = Zongcalling::where(['Type'=>'Incomming Calls','ext'=>$empData->incommingPhone])->whereDate('datetime', date('Y-m-d'))->select('Type', DB::raw('count(Type) as total_count'))->groupBy('Type')->count();
    
    $TotalConnectedCallData = Zongcalling::where(['status'=>'Connected'])->whereIn('ext',[$empData->incommingPhone,$empData->outgoingPhone])->select('status', DB::raw('count(status) as total_count'))->groupBy('status')->count();
    $TodayConnectedCallData = Zongcalling::where(['status'=>'Connected'])->whereIn('ext',[$empData->incommingPhone,$empData->outgoingPhone])->whereDate('datetime', date('Y-m-d'))->select('status', DB::raw('count(status) as total_count'))->groupBy('status')->count();
    
    $TotalMissedCallData = Zongcalling::where(['status'=>'Missed'])->whereIn('ext',[$empData->incommingPhone,$empData->outgoingPhone])->select('status', DB::raw('count(status) as total_count'))->groupBy('status')->count();
    $TodayMissedCallData = Zongcalling::where(['status'=>'Missed'])->whereIn('ext',[$empData->incommingPhone,$empData->outgoingPhone])->whereDate('datetime', date('Y-m-d'))->select('status', DB::raw('count(status) as total_count'))->groupBy('status')->count();
    
    $TotalAnsweredCallData = Zongcalling::where(['status'=>'Answered'])->whereIn('ext',[$empData->incommingPhone,$empData->outgoingPhone])->select('status', DB::raw('count(status) as total_count'))->groupBy('status')->count();
    $TodayAnsweredCallData = Zongcalling::where(['status'=>'Answered'])->whereIn('ext',[$empData->incommingPhone,$empData->outgoingPhone])->whereDate('datetime', date('Y-m-d'))->select('status', DB::raw('count(status) as total_count'))->groupBy('status')->count();
   
    $totalVideos = Videorequest::where(['emp_id'=>$agentid])->count();
    $todayVideos = Videorequest::where(['emp_id'=>$agentid])->whereDate('created_at', date('Y-m-d'))->count();
    $pendingVideos = Videorequest::where(['status'=>'Pending','emp_id'=>$agentid])->count();
    $ApprovedVideos = Videorequest::where(['status'=>'Approved','emp_id'=>$agentid])->count();
    
    $ProjectWiseDataGraph = Videorequest::where(['emp_id'=>$agentid])->select('project_id', DB::raw('count(project_id) as total_count'))->groupBy('project_id')->get();
    
    $FaceWiseDataGraph = Videorequest::where(['emp_id'=>$agentid])->select('emp_id', DB::raw('count(emp_id) as total_count'))->groupBy('emp_id')->get();
    
     $empAll = Employee::all();
     
     $ZOngCallingOutgoing = Zongcalling::where(['Type'=>'Outgoing Calls','ext'=>$empData->child_num])->sum('duration');
     $ZOngCallingOutgoingToday = Zongcalling::where(['Type'=>'Outgoing Calls','ext'=>$empData->child_num])->whereDate('datetime', date('Y-m-d'))->sum('duration');
     $ZOngCallingIncommingToday = Zongcalling::where(['Type'=>'Incomming Calls','ext'=>$empData->phone_number])->whereIn('status',['Missed','Answered'])->whereDate('datetime', date('Y-m-d'))->sum('duration');
     
     $ZOngCallingIncomming = Zongcalling::where(['Type'=>'Incomming Calls','ext'=>$empData->phone_number])->whereIn('status',['Missed','Answered'])->sum('duration');
     
     
     $week = []; 
    for ($i=0; $i <7 ; $i++) {
        
        
        $zongCallingIncommingDataWeekly = Zongcalling::where(['Type'=>'Incomming Calls','ext'=>$empData->child_num])->whereDate('datetime',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
        
        $zongCallingOutgoingDataWeekly = Zongcalling::where(['Type'=>'Outgoing Calls','ext'=>$empData->child_num])->whereDate('datetime',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
     
        $week[] = array(
         'format' => Carbon::now()->startOfWeek()->addDay($i)->format('l'),
         'datetime' => Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'),
         'incommingweekly'=>$zongCallingIncommingDataWeekly,
         'outgoingweekly'=>$zongCallingOutgoingDataWeekly,
        );
    }
    
    
    $callstatusreporting = Employee::where(['department_id'=>'1' ,'status'=>'active'])->get();
    
    $StatusRep = [];
    foreach($callstatusreporting as $empDatax){
        
         $ZOngCallingOutgoingTotal = Zongcalling::where(['Type'=>'Outgoing Calls','ext'=>$empDatax->child_num])->whereDate('datetime', date('Y-m-d'))->count();
         
          $ZOngCallingConnectedTotal = Zongcalling::where(['status'=>'Connected','ext'=>$empDatax->child_num])->whereDate('datetime', date('Y-m-d'))->count();
          
          $ZOngCallingAnsweredTotal = Zongcalling::where(['status'=>'Answered','ext'=>$empDatax->child_num])->whereDate('datetime', date('Y-m-d'))->count();
          
           $ZOngCallingMissedTotal = Zongcalling::where(['status'=>'Missed Call','ext'=>$empDatax->child_num])->whereDate('datetime', date('Y-m-d'))->count();
           
           $ZOngCallingCallbackYesTotal = Zongcalling::where(['CallBack'=>'yes','ext'=>$empDatax->child_num])->whereDate('datetime', date('Y-m-d'))->count();
           
           $ZOngCallingNotCallbackYesTotal = Zongcalling::where(['CallBack'=>'no','ext'=>$empDatax->child_num])->whereDate('datetime', date('Y-m-d'))->count();
           
            $ZOngCallingIncommingTotalToday = Zongcalling::where(['Type'=>'Incomming Calls','status'=>'Answered','ext'=>$empDatax->child_num])->whereDate('datetime', date('Y-m-d'))->sum('duration');
            
            $ZOngCallingOutgoingTotalToday = Zongcalling::where(['Type'=>'Outgoing Calls','ext'=>$empDatax->child_num])->whereDate('datetime', date('Y-m-d'))->sum('duration');
            
             $TOtalIncommTalk = $this->FloorAmount($ZOngCallingIncommingTotalToday);
              $TOtalOutgoingTalk = $this->FloorAmount($ZOngCallingOutgoingTotalToday);
              
              $totalTalktimeDuration+=$ZOngCallingOutgoingTotalToday;
              $totalTalktimeIncDuration+=$ZOngCallingIncommingTotalToday;
              
              $ZOngCallingIncommingTotal = Zongcalling::where(['Type'=>'Incomming Calls','status'=>'Answered','ext'=>$empDatax->child_num])->whereDate('datetime', date('Y-m-d'))->sum('duration');
             
          $ZOngCallingYoutubeTotal = Zongcalling::where(['Source'=>'youtube','ext'=>$empDatax->child_num])->whereDate('datetime', date('Y-m-d'))->count();
          
          $ZOngCallingSocialMediaTotal = Zongcalling::where(['Source'=>'social media','ext'=>$empDatax->child_num])->whereDate('datetime', date('Y-m-d'))->count();
        
        $StatusRep[]=array(
          'empName' => $empDatax->emp_name,
          'totalOutgoing' => $ZOngCallingOutgoingTotal,
          'totalConnected' => $ZOngCallingConnectedTotal,
          'followupcalls' => 0,
          'personalcalls' => 0,
          'totalanswered' => $ZOngCallingAnsweredTotal,
          'totalmissed' => $ZOngCallingMissedTotal,
          'totalcallback' => $ZOngCallingCallbackYesTotal,
          'totalnotcallback' => $ZOngCallingNotCallbackYesTotal,
          'incTaLKTime' => $TOtalIncommTalk,
          'TotalYoutube' => $ZOngCallingYoutubeTotal,
          'TotalSocialMedia' => $ZOngCallingSocialMediaTotal,
          'TotalOutgoingTalktime' => $TOtalOutgoingTalk,
          'TotalIncommingData' => $ZOngCallingIncommingTotal,
        );
        
    }
     
    
    $DocumentType = DocumentType::where(['status'=>'1'])->orderBy('document_title', 'ASC')->get();
    $propertyType = propertyType::all();
    $Issuingauthority = Issuingauthority::where(['status'=>'1'])->get();
    $Purposeprop = Purposeprop::where(['status'=>'1'])->get();
    $stockNature = stockNature::where(['status'=>'1'])->get();
    $stockType = stockType::where(['status'=>'1'])->get();
    $Stockrecby = Stockrecby::where(['status'=>'1'])->get();
    $Stockdeliveredby = Stockdeliveredby::where(['status'=>'1'])->get();
    $AffiliateRegisterModule = AffiliateRegisterModule::all();
    $Inventoriesfilters = Inventoriesfilters::where(['status'=>'active'])->get();
    
    $AssetTypeData = AssetType::where(['status'=>'1'])->get();
    $AssetLifeData = AssetLife::where(['status'=>'1'])->get();
    
    $TotalDealsDOne = Inventorysold::where(['employee_id'=>$agentid,'status'=>'approved'])->count();
    $TodayDealsDOne = Inventorysold::where(['employee_id'=>$agentid,'status'=>'approved'])->whereDate('created_at', date('Y-m-d'))->count();
    $apartmentType = natureProject::all();
    
}


$getAllProjects = LeadImportDatabase::select('clientProject', DB::raw('count(clientProject) as total_count'))->groupBy('clientProject')->get();

$getAllNature = LeadImportDatabase::select('nature', DB::raw('count(nature) as total_count'))->groupBy('nature')->get();

$getAllDuplicates = LeadImportDatabase::select('leadType', DB::raw('count(leadType) as total_count'))->groupBy('leadType')->get();


$UntabbedGraphDumy = array(array("clientProject"=>"Empty","total_count"=>"0"));

$dataRx = $this->renderList($ReminderTodayData);

if(count($untabbedLeadsAllGraph) > 0){

}else{
    $untabbedLeadsAllGraph = $UntabbedGraphDumy;
}

$GraphProject = [];
foreach($ProjectWiseDataGraph as $ProjectDa){
    
    if($ProjectDa->project_id == 0){
        
    }else{
        
        $Project = Project::where(['id'=>$ProjectDa->project_id])->first();
        $GraphProject[]=array(
            'project_name' => $Project == null ? '' : $Project->project_name,
            'total_count' => $ProjectDa->total_count,
        );
        
    }
    
}

$FaceGraphPro = [];
foreach($FaceWiseDataGraph as $FaceProjectDa){
        
    $EmployeeData = Employee::where(['id'=>$FaceProjectDa->emp_id])->first();
    $FaceGraphPro[]=array(
        'emp_name' => $EmployeeData->emp_name,
        'profile_pic' => $EmployeeData->profile_pic,
        'total_count' => $FaceProjectDa->total_count,
    );
}

$OutgoingToday = $this->FloorAmount($ZOngCallingOutgoingToday);
$IncommingToday = $this->FloorAmount($ZOngCallingIncommingToday);
$OutgoingAll = $this->FloorAmount($ZOngCallingOutgoing);
$IncommingAll = $this->FloorAmount($ZOngCallingIncomming);


$data = array(
    'AssetLifeData'=>$AssetLifeData,
    'AssetTypeData'=>$AssetTypeData,
    'apartmentType'=>$apartmentType,
    'TotalDealsDOne'=>$TotalDealsDOne,
    'TodayDealsDOne'=>$TodayDealsDOne,
    'Inventoriesfilters'=>$Inventoriesfilters,
    'AffiliateRegisterModule'=>$AffiliateRegisterModule,
    'Stockdeliveredby'=>$Stockdeliveredby,
    'Stockrecby'=>$Stockrecby,
    'stockNature'=>$stockNature,
    'stockType'=>$stockType,
    'Purposeprop' => $Purposeprop,
    'Issuingauthority'=>$Issuingauthority,
    'propertyType'=>$propertyType,
    'DocumentType'=>$DocumentType,
    'StatusRep'=>$StatusRep,
    'ZOngCallingOutgoingToday' => $OutgoingToday,
    'ZOngCallingIncommingToday' => $IncommingToday,
    'ZOngCallingOutgoing' => $OutgoingAll,
    'ZOngCallingIncomming' => $IncommingAll,
    'empall'=>$empAll,
    'ProjectWiseDataGraph' => $GraphProject,
    'FaceWiseDataGraph' => $FaceGraphPro,
    'totalVideos' => $totalVideos,
    'todayVideos' => $todayVideos,
    'pendingVideos' => $pendingVideos,
    'ApprovedVideos' => $ApprovedVideos,
    'getAllProjects' => $getAllProjects,
    'getAllNature' => $getAllNature,
    'getAllDuplicates' => $getAllDuplicates,
    'TotalOutgoingCallData'=>$TotalOutgoingCallData,
    'TodayOutgoingCallData'=>$TodayOutgoingCallData,
    'TotalIncommingCallData'=>$TotalIncommingCallData,
    'TodayIncommingCallData'=>$TodayIncommingCallData,
    'TotalConnectedCallData'=>$TotalConnectedCallData,
    'TodayConnectedCallData'=>$TodayConnectedCallData,
    'TotalMissedCallData'=>$TotalMissedCallData,
    'TodayMissedCallData'=>$TodayMissedCallData,
    'TotalAnsweredCallData'=>$TotalAnsweredCallData,
    'TodayAnsweredCallData'=>$TodayAnsweredCallData,
    'totalASsign'=>$AssignLeadsTotal,
    'todayASsign'=>$AssignLeadsToday,
    'TotalMyLead'=>$MyLeadCountAll,
    'TodayMyLead'=>$MyLeadCountToday,
    'TotalHotleads'=>$HotCountAll,
    'TodayHotleads'=>$HotCountToday,
    'TotalNotinter'=>$NotinterestedCountAll,
    'TodayNotinter'=>$NotinterestedCountToday,
    'cusACtivityCountAll'=>$cusACtivityCountAll,
    'cusActivityCountToday'=>$cusActivityCountToday,
    'ReminderCountAll'=>$ReminderCountAll,
    'ReminderCountToday'=>$ReminderCountToday,
    'PhoneCountToday'=>$PhoneCountToday,
    'whatsappCountToday'=>$whatsappCountToday,
    'smsCountToday'=>$smsCountToday,
    'emailCountToday'=>$emailCountToday,
    'untabbedLeadsAll'=>$untabbedLeadsAll,
    'untabbedLeadsToday'=>$untabbedLeadsToday,
    'MyLeadMaleCountAll'=>$MyLeadMaleCountAll,
    'MyLeadFeMaleCountAll'=>$MyLeadFeMaleCountAll,
    'TodosTodayData'=>$dataRx,
    'TotalDormentLeads'=>$DormentLeadCountAll,
    'TodayDormentLeads'=>$DormentLeadCountToday,
    'TotalTransferLeads'=>$TransferLeadsCountAll,
    'TodayTransferLeads'=>$TodayTransferLeadsCount,
    'AssignLeadsDashboard'=>$listAssignDashboard,
    'ubtabbedGraphDashboard'=>$untabbedLeadsAllGraph,
    'zongCallingData'=>$zongCallingData,
    'GraphCallingZOng'=>$week,
    'CallingCountToday' => $CallingCountToday,
    'CallingCountTotal' => $CallingCountTotal,
    'totalOutgoingAll' => $totalOutgoingAll,
    'totaConnectedAll'=>$totaConnectedAll,
    'totaFollowupAll'=> $totaFollowupAll,
      'totaPersonalCallAll'=>$totaPersonalCallAll,
      'totaIncommingCallAll'=>$totaIncommingCallAll,
      'totaIncommingAnsweredCallAll'=>$totaIncommingAnsweredCallAll,
      'totaIncommingMissedCallAll'=>$totaIncommingMissedCallAll,
      'totaCallbackCallAll'=>$totaCallbackCallAll,
      'totaCallbackNotCallAll'=>$totaCallbackNotCallAll,
      'totaYoutubeCallAll'=>$totaYoutubeCallAll,
      'totaSocialMediaCallAll'=>$totaSocialMediaCallAll,
      'totalTalktimeDuration'=>$totalTalktimeDuration,
      'totalTalktimeIncDuration'=>$totalTalktimeIncDuration,
);


if($AssignLeadsTotal == ''){
    return response()->json(['code'=>'403','error'=>false,'message'=>'Record Not Found','data'=>$listAssign,'orderBy'=>'','orderType'=>'','perPage'=>'','pageNo'=>'']);
}else{
    return response()->json(['code'=>'200','success'=>true,'message'=>'Record Found','data'=>$data]);
   
}

}

public function FloorAmount($DurationSum)
{
    $init = $DurationSum;
    $hours = floor($init / 3600);
    $minutes = floor(($init / 60) % 60);
    $seconds = $init % 60;

return "$hours:$minutes:$seconds";
}


public function renderList(Object $apiResponse)
{
$data = [];

foreach ($apiResponse as $key => $value) {
    
    if($value->leadid == 0){
        
        $customerData = ListMyLead::where(['id'=>$value->customerId])->first();
        
    }else{
        
        $customerData = $value;
        
    }

    array_push($data, (object) [
        "id" => $value->id,
        "leadid"=>$value->leadid,
        "created_at" => $value->created_at,
        "updated_at" => $value->updated_at,
        "deleted_at" => $value->deleted_at,
        "reminderSet" => $value->reminderSet,
        "priority" => $value->priority,
        "reminderinfo" => $value->reminderinfo,
        "getleads_home"=>[
           'clientName' => $customerData->clientName == null ? $customerData->getleads_home->clientName : $customerData->clientName,  
           'clientStatus'=> $customerData->clientStatus,
           'id'=> $customerData->id,
           'countryid'=> $customerData->countryid,
           'cityid'=> $customerData->cityid,
           'projectid'=> $customerData->projectid,
           'natureid'=> $customerData->natureid,
           'actMediumId'=> $customerData->actMediumId,
           'Gender'=> $customerData->Gender,
           'email'=> $customerData->email,
           'mobileNo'=> $customerData->mobileNo,
           'passportCnic'=> $customerData->passportCnic,
           'prefLanguage'=> $customerData->prefLanguage,
           'Budget'=> $customerData->Budget,
           'timeFrame'=> $customerData->timeFrame,
           'RefferdBy'=> $customerData->RefferdBy,
           'clientInterst'=> $customerData->clientInterst,
           'clientActivityDesc'=> $customerData->clientActivityDesc,
         ],
       
    ]);
}

return $data;
}



public function GetNotificationFirebase(Request $request){

$rules=array(
    'Token'=>'required',
    'title'=>'required',
    'body'=>'required',
);

$validator = Validator::make($request->all(),$rules);
if($validator->fails()){
    return $validator->errors();
}else{
    
    $firebaseToken =[$request->post('Token')];
    
    $SERVER_API_KEY = 'AAAAlEKlRQs:APA91bHaCN2HRq2OXCAR-vJEGm9PWuuvGFbEuTAZlOxYu7ezu8LU-cxBvUwpM9wcfKsyV7r3Faiqag9_TqPqoUDm3cEXApB171B1EXy1WXohhkEGdlJSfp_uJTQnUijn2kR_G1BXwvCV';
    
    $data = [
    "registration_ids" => $firebaseToken,
    "notification" => [
        "title" => $request->post('title'),
        "body" => $request->post('body'),  
    ]
    ];
    $dataString = json_encode($data);
    
    $headers = [
    'Authorization: key=' . $SERVER_API_KEY,
    'Content-Type: application/json',
    ];
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
       
    $response = curl_exec($ch);
    
    return $response;
    
}



}

}
