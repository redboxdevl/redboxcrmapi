<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HR\Employee;
use App\Models\ListAssignLeads;
use App\Models\ListMyLead;
use App\Models\CustomerActivity;
use App\Models\ActivityTypes;
use App\Models\ListNotInterested;
use App\Models\Zongcalling;
use App\Models\Inventory\Inventorysold;
use App\Models\Inventory\Inventoryhold;
use App\Models\LeadImportDatabase;
use App\Models\LeadImpression;
use App\Models\AffiliateProgram\AffiliateRegisterModule;

use Carbon\Carbon;
use DB;
use Validator;
use DateTime;

use Illuminate\Http\Request;

class KpiReportController extends Controller
{
    
    
    public function index(Request $request)
    {
        
        $agentId = $request->agentId;
        $ReportType = $request->ReportType ?? "";
        $EmpData = $request->EmpData ?? "";
        $dateWise = $request->dateWise ?? "";
        $StartDates = $request->StartDates ?? "";
        $EndDate = $request->EndDate ?? "";
        $SalesConsul = $request->SalesConsul ?? "";
        $NoOfDays = $request->NoOfDays ?? "";
        $companyId = $request->companyId ?? "";

        if (!empty($agentId) && $agentId !='all' && empty($EmpData) && empty($SalesConsul)) {
           
           if($agentId == 38){
               $response = Employee::with(['getOutgoingCalls' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['department_id'=>'1','status'=>'active','company_id'=>$companyId])->get();
           }else{
               $response = Employee::with(['getOutgoingCalls' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['department_id'=>'1','status'=>'active','company_id'=>$companyId])->where(['id'=>$agentId])->get();
           }
            
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response,$ReportType,$StartDates,$EndDate,$NoOfDays);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'KPI Employee List Found',
                    'data' => $data,
                  
                ]);
            }

            // FAIL
            else {
                return app(CommonController::class)->getEmptyListResponse();
            }
        }else if($agentId == 'all' && empty($EmpData) && empty($SalesConsul)){
            
            $response = Employee::where(['department_id'=>'1','status'=>'active','company_id'=>$companyId])->get();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response,$ReportType,$StartDates,$EndDate,$NoOfDays);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'KPI Employee List Found',
                    'data' => $data,
                  
                ]);
            }

            // FAIL
            else {
                return app(CommonController::class)->getEmptyListResponse();
            }
            
        }
        else if($agentId == 'all' && !empty($EmpData) && empty($SalesConsul)){
            
            $myArray2 = explode(',', $EmpData);
            $myArray = str_replace(array('[',']'),'',$myArray2);
            
            $response = Employee::where(['department_id'=>'1','status'=>'active','company_id'=>$companyId])->whereIn('id',$myArray)->get();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response,$ReportType,$StartDates,$EndDate,$NoOfDays);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'KPI Employee List Found',
                    'data' => $data,
                  
                ]);
            }

            // FAIL
            else {
                return app(CommonController::class)->getEmptyListResponse();
            }
            
        }
        else if($agentId == 'all' && !empty($EmpData) && !empty($SalesConsul)){
            
            $myArray2 = explode(',', $EmpData);
            $myArray = str_replace(array('[',']'),'',$myArray2);
            
            $response = Employee::where(['department_id'=>'1','status'=>'active','company_id'=>$companyId])->whereIn('id',$myArray)->where(['id'=>$SalesConsul])->get();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response,$ReportType,$StartDates,$EndDate,$NoOfDays);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'KPI Employee List Found',
                    'data' => $data,
                  
                ]);
            }

            // FAIL
            else {
                return app(CommonController::class)->getEmptyListResponse();
            }
            
        }else if($agentId == 'all' && empty($EmpData) && !empty($SalesConsul)){
            
            $myArray2 = explode(',', $EmpData);
            $myArray = str_replace(array('[',']'),'',$myArray2);
            
            $response = Employee::where(['department_id'=>'1','status'=>'active','company_id'=>$companyId])->where(['id'=>$SalesConsul])->get();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response,$ReportType,$StartDates,$EndDate,$NoOfDays);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'KPI Employee List Found',
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

public function FloorAmount($DurationSum)
{
    $init = $DurationSum;
    $hours = floor($init / 3600);
    $minutes = floor(($init / 60) % 60);
    $seconds = $init % 60;
    
    return "$hours:$minutes:$seconds";
}

public function renderList(Object $apiResponse,$ReportType,$StartDates,$EndDate,$NoOfDays)
{
$data = [];

$totalAssignLeads = [];
$totalTlw = [];
$totalAttemptedCalls = [];
$totalCCSEC = [];
$totalCC90SEC = [];
$totalMettingAlign = [];
$totalMettingDOneC = [];
$totalTalkTime = [];
$totalSelfLeads = [];
$totalNotiNterstedLeads = [];
$totalAffiliateReg = [];

$datetime1 = new DateTime($StartDates);
$datetime2 = new DateTime($EndDate);
$interval = $datetime1->diff($datetime2);
$days = $interval->format('%a');//now do whatever you like with $days

foreach ($apiResponse as $key => $value) {
$conxs="";
 
    if($ReportType == 'daily' || $ReportType == '' || $StartDates == '' || $EndDate == ''){
        
       $myData = date("Y-m-d"); 
        
        if($StartDates == '' || $EndDate == ''){
            $connectedCalls = Zongcalling::whereIn('status',['Connected','Answered'])->whereIn('ext',[$value->outgoingPhone,$value->incommingPhone])->where('duration', '>=', 30)->where('duration', '<', 90)->where(DB::raw('DATE(datetime)'), $myData)->count();
            $VerifiedCalls = Zongcalling::whereIn('ext',[$value->outgoingPhone,$value->incommingPhone])->where('duration', '>=', 90)->where(DB::raw('DATE(datetime)'), $myData)->count();
            
             $mettingDone = CustomerActivity::where(['agentid'=>$value->id,'typeId'=>'18'])->where(DB::raw('DATE(created_at)'), $myData)->count();
        
        $mettingAlligned = CustomerActivity::where(['agentid'=>$value->id,'typeId'=>'24'])->where(DB::raw('DATE(created_at)'), $myData)->count();
        
        $SelfLead = ListMyLead::where(['agentid'=>$value->id,'leadid'=>'0'])->where(DB::raw('DATE(created_at)'), $myData)->count();
        
         $getOutgoingDuration = DB::table('zong_calling_data')->where(['ext'=>$value->outgoingPhone,'Type'=>'Outgoing Calls'])->where(DB::raw('DATE(datetime)'), $myData)->sum('duration');
         
         $getIncommingDuration = DB::table('zong_calling_data')->where(['ext'=>$value->incommingPhone,'Type'=>'Incomming Calls'])->where(DB::raw('DATE(datetime)'), $myData)->sum('duration');
         
         
         $AttemptedCalls = Zongcalling::whereIn('ext',[$value->outgoingPhone])->where(DB::raw('DATE(datetime)'), $myData)->get()->unique('phone')->count();
        $AttemptedCallstest=Zongcalling::whereIn('ext',[$value->outgoingPhone])->where(DB::raw('DATE(datetime)'), $myData)->get()->unique('phone')->count();
        
        // $totalLeadWork = ListAssignLeads::where(['agentid'=>$value->id])->where(DB::raw('DATE(updated_at)'), date("Y-m-d"))->count();
            $totalLeadWork = ListAssignLeads::with(['getImpressionLeads' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ,])->where(['agentid'=>$value->id])->where(DB::raw('DATE(created_at)'), $myData)->get();
            
            $newArr = [];
            foreach($totalLeadWork as $itend){
            if($itend->getImpressionLeads == '' || $itend->getImpressionLeads == null){
            }else{
            $newArr[]=$itend->getImpressionLeads;
            
            }
            
            }
           
            
            //$dataZOng = Zongcalling::whereIn('ext',[$value->outgoingPhone,$value->incommingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($StartDates,$EndDate))->get()->chunk(3);
            
            $todayAssignLeads = ListAssignLeads::where(['agentid'=>$value->id])->where(DB::raw('DATE(created_at)'), $myData)->count();
            
        
        $allListed = ListAssignLeads::where(['agentid'=>$value->id])->where(DB::raw('DATE(updated_at)'), $myData)->get();
        
         $LeadImpression = CustomerActivity::where(['agentid'=>$value->id])->where(DB::raw('DATE(created_at)'), $myData)->get()->unique('customerId')->count();
         
         $ListNotInterested = ListNotInterested::where(['agentid'=>$value->id])->where(DB::raw('DATE(created_at)'), $myData)->get()->unique('leadid')->count();
         
         
         
         $totalAssignLeads[]=$todayAssignLeads;
        $totalTlw[]=$LeadImpression;
        $totalCCSEC[]=$connectedCalls;
        $totalAttemptedCalls[]=$AttemptedCalls;
        $totalCC90SEC[]=$VerifiedCalls;
        $totalMettingAlign[]=$mettingAlligned;
        $totalMettingDOneC[]=$mettingDone;
        $totalTalkTime[]=$getOutgoingDuration;
         $totalTalkTimeInc[]=$getIncommingDuration;
        $totalSelfLeads[]=$SelfLead;
        $totalNotiNterstedLeads[]=$ListNotInterested;
        $totalAffiliateReg[]=0;
            
        }else{
            $connectedCalls = Zongcalling::whereIn('status',['Connected','Answered'])->whereIn('ext',[$value->outgoingPhone,$value->incommingPhone])->where('duration', '>=', 30)->where('duration', '<', 90)->whereBetween(DB::raw('DATE(datetime)'), [$StartDates,$EndDate])->count();
            $VerifiedCalls = Zongcalling::whereIn('ext',[$value->outgoingPhone,$value->incommingPhone])->where('duration', '>=', 90)->whereBetween(DB::raw('DATE(datetime)'), [$StartDates,$EndDate])->count();
            
             $mettingDone = CustomerActivity::where(['agentid'=>$value->id,'typeId'=>'18'])->whereBetween(DB::raw('DATE(created_at)'), [$StartDates,$EndDate])->count();
        
        $mettingAlligned = CustomerActivity::where(['agentid'=>$value->id,'typeId'=>'24'])->whereBetween(DB::raw('DATE(created_at)'), [$StartDates,$EndDate])->count();
        
        $SelfLead = ListMyLead::where(['agentid'=>$value->id,'leadid'=>'0'])->whereBetween(DB::raw('DATE(created_at)'), [$StartDates,$EndDate])->count();
        
        
         $getOutgoingDuration = DB::table('zong_calling_data')->where(['ext'=>$value->outgoingPhone,'Type'=>'Outgoing Calls'])->whereBetween(DB::raw('DATE(datetime)'), [$StartDates,$EndDate])->sum('duration');
         
         $getIncommingDuration = DB::table('zong_calling_data')->where(['ext'=>$value->incommingPhone,'Type'=>'Incomming Calls'])->whereBetween(DB::raw('DATE(datetime)'), [$StartDates,$EndDate])->sum('duration');
         
         
         $AttemptedCalls = Zongcalling::whereIn('ext',[$value->outgoingPhone])->whereBetween(DB::raw('DATE(datetime)'), [$StartDates,$EndDate])->get()->unique('phone')->count();
         
          $AttemptedCallstest = Zongcalling::whereIn('ext',[$value->outgoingPhone])->whereBetween(DB::raw('DATE(datetime)'), [$StartDates,$EndDate])->get()->unique('phone')->count();
        
        // $totalLeadWork = ListAssignLeads::where(['agentid'=>$value->id])->where(DB::raw('DATE(updated_at)'), date("Y-m-d"))->count();
            $totalLeadWork = ListAssignLeads::with(['getImpressionLeads' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ,])->where(['agentid'=>$value->id])->whereBetween(DB::raw('DATE(created_at)'), [$StartDates,$EndDate])->get();
            
            $newArr = [];
            foreach($totalLeadWork as $itend){
            if($itend->getImpressionLeads == '' || $itend->getImpressionLeads == null){
            }else{
            $newArr[]=$itend->getImpressionLeads;
          
            }
            
            }
            
            
            //$dataZOng = Zongcalling::whereIn('ext',[$value->outgoingPhone,$value->incommingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($StartDates,$EndDate))->get()->chunk(3);
            
            $todayAssignLeads = ListAssignLeads::where(['agentid'=>$value->id])->whereBetween(DB::raw('DATE(created_at)'), [$StartDates,$EndDate])->count();
            
           
        
        $allListed = ListAssignLeads::where(['agentid'=>$value->id])->whereBetween(DB::raw('DATE(updated_at)'), [$StartDates,$EndDate])->get();
        
         $LeadImpression = CustomerActivity::where(['agentid'=>$value->id])->whereBetween(DB::raw('DATE(created_at)'), [$StartDates,$EndDate])->get()->unique('customerId')->count();
         
         $ListNotInterested = ListNotInterested::where(['agentid'=>$value->id])->whereBetween(DB::raw('DATE(created_at)'), [$StartDates,$EndDate])->get()->unique('leadid')->count();
         
         
          $totalAssignLeads[]=$todayAssignLeads;
        $totalTlw[]=$LeadImpression;
        $totalCCSEC[]=$connectedCalls;
        $totalAttemptedCalls[]=$AttemptedCalls;
        $totalCC90SEC[]=$VerifiedCalls;
        $totalMettingAlign[]=$mettingAlligned;
        $totalMettingDOneC[]=$mettingDone;
        $totalTalkTime[]=$getOutgoingDuration;
         $totalTalkTimeInc[]=$getIncommingDuration;
        $totalSelfLeads[]=$SelfLead;
        $totalNotiNterstedLeads[]=$ListNotInterested;
        $totalAffiliateReg[]=0;
            
        }
        
        
        
        
       
        
        //$getOutgoingDuration = Zongcalling::where(['ext'=>$value->outgoingPhone,'Type'=>'Outgoing Calls'])->where(DB::raw('DATE(datetime)'), date("Y-m-d"))->get();
        
        
        // $getOutgoingDuration = Zongcalling::where(['ext'=>$value->outgoingPhone,'Type'=>'Outgoing Calls'])->where(DB::raw('DATE(datetime)'), date("Y-m-d"))->sum('duration');
        
        
         
          if($days != '' && $days !=0){
            $conxs = "1";
            $dayAtt = 100 * $days;
            $dayAtts = 50 * $days;
            $dayAtts3 = 30 * $days;
            $dayAtts4 = 5 * $days;
            $dayAtts5 = 2 * $days;
            
             $AttemptedPercentage = $AttemptedCalls / $dayAtt * 100;
            $connectedCallsPercentage = $connectedCalls / $dayAtts * 100;
            $VerifiedCallsPercentage = $VerifiedCalls / $dayAtts3 * 100;
            $mettingAllignedPercentage = $mettingAlligned / $dayAtts4 * 100;
            $mettingDonePercentage = $mettingDone / $dayAtts5 * 100;
            $SelfLeadsPercentage = $SelfLead / 10 * 100;
        }else{
            $conxs = "2";
            $AttemptedPercentage = ($AttemptedCalls / 100) * 100;
            $connectedCallsPercentage = ($connectedCalls / 50) * 100;
            $VerifiedCallsPercentage = ($VerifiedCalls / 30) * 100;
            $mettingAllignedPercentage = ($mettingAlligned / 5) * 100;
            $mettingDonePercentage = ($mettingDone / 2) * 100;
            $SelfLeadsPercentage = ($SelfLead / 10) * 100;
           
        }
         
        //  $AttemptedPercentage = $AttemptedCalls / 100 * 100;
         
        //  $connectedCallsPercentage = $connectedCalls / 50 * 100;
         
        //  $VerifiedCallsPercentage = $VerifiedCalls / 30 * 100;
         
        //  $mettingAllignedPercentage = $mettingAlligned / 5 * 100;
         
        //  $mettingDonePercentage = $mettingDone / 2 * 100;
         
        //   $SelfLeadsPercentage = ($SelfLead / 10) * 100;
         
         $atVsMaPercentage = $AttemptedCalls * $mettingAllignedPercentage / 100;
         
        //   $totalAssignLeads[]=$todayAssignLeads;
        // $totalTlw[]=$LeadImpression;
        // $totalCCSEC[]=$connectedCalls;
        // $totalAttemptedCalls[]=$AttemptedCalls;
        // $totalCC90SEC[]=$VerifiedCalls;
        //  $totalMettingAlign[]=$mettingAlligned;
        // $totalMettingDOneC[]=$mettingDone;
        //  $totalTalkTime[]=0;
        //  $totalSelfLeads[]=0;
    
    }else{
        
        
        $connectedCalls = Zongcalling::whereIn('status',['Connected','Answered'])->whereIn('ext',[$value->outgoingPhone,$value->incommingPhone])->where('duration', '>=', 30)->where('duration', '<', 90)->where(DB::raw('MONTH(created_at)'), date("m"))->count();
        
        $VerifiedCalls = Zongcalling::whereIn('ext',[$value->outgoingPhone,$value->incommingPhone])->where('duration', '>=', 90)->where(DB::raw('MONTH(created_at)'), date("m"))->count();
        
        $mettingDone = CustomerActivity::where(['agentid'=>$value->id,'typeId'=>'18'])->where(DB::raw('MONTH(created_at)'), date("m"))->count();
        
        $mettingAlligned = CustomerActivity::where(['agentid'=>$value->id,'typeId'=>'24'])->where(DB::raw('MONTH(created_at)'), date("m"))->count();
        
        $SelfLead = ListMyLead::where(['agentid'=>$value->id,'leadid'=>'0'])->where(DB::raw('MONTH(created_at)'), date("m"))->count();
        
        // $TalkTime =  Zongcalling::whereIn('ext',[$value->outgoingPhone,$value->incommingPhone])->where(['Type'=>'Outgoing Calls'])->where(DB::raw('MONTH(datetime)'), date("m"))->get();
        
        // $getOutgoingDuration = Zongcalling::where(['ext'=>$value->outgoingPhone,'Type'=>'Outgoing Calls'])->where(DB::raw('MONTH(datetime)'), date("m"))->get();
        
    //   $getOutgoingDuration = Zongcalling::where(['ext'=>$value->outgoingPhone,'Type'=>'Outgoing Calls'])->where(DB::raw('DATE(datetime)'), date("Y-m-d"))->selectRaw('count(duration) as duration')
    //     // ->groupBy('duration')->get();
        
        $getOutgoingDuration = DB::table('zong_calling_data')->where(['ext'=>$value->outgoingPhone,'Type'=>'Outgoing Calls'])->where(DB::raw('MONTH(datetime)'), date("m"))->sum('duration');
        
        $getIncommingDuration = DB::table('zong_calling_data')->where(['ext'=>$value->incommingPhone,'Type'=>'Incomming Calls'])->where(DB::raw('MONTH(datetime)'), date("m"))->sum('duration');
        
        $AttemptedCalls = Zongcalling::where(['ext'=>$value->outgoingPhone,'Type'=>'Outgoing Calls'])->where(DB::raw('MONTH(datetime)'), date("m"))->get()->unique('phone')->count();
        
        // $totalLeadWork = ListAssignLeads::where(['agentid'=>$value->id])->where(DB::raw('MONTH(updated_at)'), date("m"))->count();
        
            $totalLeadWork = ListAssignLeads::with(['getImpressionLeads' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ,])->where(['agentid'=>$value->id])->where(DB::raw('MONTH(created_at)'), date("m"))->get();
            
            $newArr = [];
            foreach($totalLeadWork as $itend){
            if($itend->getImpressionLeads == '' || $itend->getImpressionLeads == null){
            }else{
            $newArr[]=$itend->getImpressionLeads;
           
            }
            
            }
            
             
            //$dataZOng = Zongcalling::whereIn('ext',[$value->outgoingPhone,$value->incommingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($StartDates,$EndDate))->get()->chunk(3);
            
            $todayAssignLeads = ListAssignLeads::where(['agentid'=>$value->id])->where(DB::raw('MONTH(created_at)'), date("m"))->count();
            
            
            $LeadImpression = CustomerActivity::where(['agentid'=>$value->id])->where(DB::raw('MONTH(created_at)'), date("m"))->get()->unique('customerId')->count();
            
            $ListNotInterested = ListNotInterested::where(['agentid'=>$value->id])->where(DB::raw('MONTH(created_at)'), date("m"))->get()->unique('leadid')->count();
            
             $totalAssignLeads[]=$todayAssignLeads;
        $totalTlw[]=$LeadImpression;
        $totalCCSEC[]=$connectedCalls;
        $totalAttemptedCalls[]=$AttemptedCalls;
        $totalCC90SEC[]=$VerifiedCalls;
         $totalMettingAlign[]=$mettingAlligned;
        $totalMettingDOneC[]=$mettingDone;
         $totalTalkTime[]=$getOutgoingDuration;
         $totalTalkTimeInc[]=$getIncommingDuration;
         $totalSelfLeads[]=$SelfLead;
          $totalNotiNterstedLeads[]=$ListNotInterested;
          $totalAffiliateReg[]=0;
          
        
        // $allListed = ListAssignLeads::where(['agentid'=>$value->id])->where(DB::raw('MONTH(updated_at)'), date("m"))->get();
        
        // foreach($allListed as $getItem){
        //   if($getItem['shiftStatus'] == '2'){
        //       $notIntersData = ListNotInterested::where(['leadid'=>$getItem['leadid']])->get();
        //       $myLeads="";
        //       $assignLeads="";
        //   }else if($getItem['shiftStatus'] == '1'){
        //       $notIntersData="";
        //       $myLeads = ListMyLead::where(['leadid'=>$getItem['leadid']])->get();
        //       $assignLeads="";
        //   }else{
        //       $notIntersData="";
        //       $myLeads="";
        //       $assignLeads = ListAssignLeads::where(['leadid'=>$getItem['leadid']])->get();
        //   }
        // }
        
        
        
        if($days != '' || $days !=0){
            $conxs = "1";
            $dayAtt = 100 * $days;
            $dayAtts = 50 * $days;
            $dayAtts3 = 30 * $days;
            $dayAtts4 = 5 * $days;
            $dayAtts5 = 2 * $days;
            
            $AttemptedPercentage = $AttemptedCalls / $dayAtt * 100;
            $connectedCallsPercentage = $connectedCalls / $dayAtts * 100;
            $VerifiedCallsPercentage = $VerifiedCalls / $dayAtts3 * 100;
            $mettingAllignedPercentage = $mettingAlligned / $dayAtts4 * 100;
            $mettingDonePercentage = $mettingDone / $dayAtts5 * 100;
            $SelfLeadsPercentage = $SelfLead / 10 * 100;
            
        }else{
            $conxs = "2";
            $AttemptedPercentage = ($AttemptedCalls / 100) * 100;
            $connectedCallsPercentage = ($connectedCalls / 50) * 100;
            $VerifiedCallsPercentage = ($VerifiedCalls / 30) * 100;
            $mettingAllignedPercentage = ($mettingAlligned / 5) * 100;
            $mettingDonePercentage = ($mettingDone / 2) * 100;
            $SelfLeadsPercentage = ($SelfLead / 10) * 100;
           
        }
        
       
        
        $atVsMaPercentage = $AttemptedCalls * $mettingAllignedPercentage / 100;

    }
    
    $TalDaa = [];
    $count=0;
    
    $kpiComTotal = $AttemptedPercentage + $connectedCallsPercentage + $VerifiedCallsPercentage + $mettingAllignedPercentage + $mettingDonePercentage;
    
    
    array_push($data, (object) [
        "id" => $value->id,
        "created_at" => $value->created_at,
        "updated_at" => $value->updated_at,
        "deleted_at" => $value->deleted_at,
        "emp_name"=>$value->emp_name,
        'connectedCalls'=>$connectedCalls,
        "VerifiedCalls"=>$VerifiedCalls,
        "mettingDone"=>$mettingDone,
        "SelfLead"=>$SelfLead,
        "TalkTime"=>$this->FloorAmount($getOutgoingDuration),
        "incommingTalkTime"=>$this->FloorAmount($getIncommingDuration),
        "mettingAlligned"=>$mettingAlligned,
        "AttemptedCall"=>$AttemptedCalls,
        "totalLeadWork"=>$LeadImpression,
        'ListNotInterested'=>$ListNotInterested,
        "todayAssignLeads"=>$todayAssignLeads,
        "AttemptedPercentage"=>round($AttemptedPercentage),
        "connectedCallsPercentage"=>round($connectedCallsPercentage),
        "VerifiedCallsPercentage"=>round($VerifiedCallsPercentage),
        "mettingAllignedPercentage"=>round($mettingAllignedPercentage),
        "mettingDonePercentage"=>round($mettingDonePercentage),
        "atVsMaPercentage"=>round($atVsMaPercentage),
        "NoOfDays"=>$NoOfDays,
        "TotalKpiCompletion" => round($kpiComTotal / 5),
        'AttemptedCallstest'=>$AttemptedCallstest,
        "AttemptedPercentage"=>$AttemptedPercentage,
        "kpiComTotal"=>$kpiComTotal / 5,
       
      
    ]);
}

return [
    'records'=>$data,
    'totalAssignLeads'=>array_sum($totalAssignLeads),
    "tlwTotal"=>array_sum($totalTlw),
    "attemptedCallTotal" => array_sum($totalAttemptedCalls),
    "CCSecTotal"=>array_sum($totalCCSEC),
    "CC90SecTotal"=>array_sum($totalCC90SEC),
    "MettingAlignTotal"=>array_sum($totalMettingAlign),
    "MettingDoneTotal"=>array_sum($totalMettingDOneC),
    "TotalTalkTime"=>$this->FloorAmount(array_sum($totalTalkTime)),
    "totalTalkTimeInc"=>$this->FloorAmount(array_sum($totalTalkTimeInc)),
    "TotalLeadSelf"=>array_sum($totalSelfLeads),
    "totalNotiNterstedLeads"=>array_sum($totalNotiNterstedLeads),
    "days"=>$days,
];
}


}
