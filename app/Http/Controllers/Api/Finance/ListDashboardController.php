<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\BanklistRequest;
use App\Models\DocumentType;
use App\Models\ListAssignLeads;
use App\Models\ListMyLead;
use App\Models\ListNotInterested;
use App\Models\AssignReminder;
use App\Models\CustomerActivity;
use App\Models\TranasferShareLeads;
use App\Models\Zongcalling;
use App\Models\LeadImpression;
use App\Models\LeadImportDatabase;
use App\Models\HR\empTeam;
use App\Models\Assignreminderlist;
use App\Models\Leadsourcelist;
use App\Models\TaskManagement\Task;
use App\Models\TaskManagement\TaskChecklist;
use App\Models\TaskManagement\TaskAttachment;
use App\Models\TaskManagement\TaskHistory;
use App\Models\TaskManagement\TaskComment;
use App\Models\TaskManagement\Priority;
use App\Models\TaskManagement\TaskStatus;
use App\Models\HR\Noticeboard;
use App\Models\Finance\AssetType;
use App\Models\Finance\Chartlevel1;
use App\Models\Finance\Chartlevel2;

use App\Models\Finance\Dimensionlevel1;
use App\Models\Finance\Dimensionlevel2;


use App\Models\Inventory\Inventorysold;
use App\Models\ProjectAdd;


use App\Models\HR\Employee;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Arr;

use Validator;
use Mail;
use DB;

class ListDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $agentId = $request->agentId;
        $teamObj = $request->teamObj;
        $company_id = $request->company_id ?? "";
        

        if ($agentId == 'all' && empty($teamObj)) {
            
                $myEmpDataIm =  Employee::where(['status'=>'active','company_id'=>$company_id])->where('incommingPhone','!=','')->where('outgoingPhone','!=','')->pluck('outgoingPhone')->join(',');
                // $expEmp = explode(",",$myEmpDataIm);
                // $myEmpDataIxsm = implode("','",$expEmp);
                
                $teamObject = explode(',', $myEmpDataIm);
                $myEmpDataIxsm = str_replace(array('[',']'),'',$teamObject);
               
                $assignTotal = ListAssignLeads::where(['shiftStatus'=>'0','companyId'=>$company_id])->count();
                $assignToday = ListAssignLeads::where(['shiftStatus'=>'0','companyId'=>$company_id])->where(DB::raw('DATE(publishAgentDate)'), date('Y-m-d'))->count();
                
                $notIntersAll = ListNotInterested::all()->unique('leadid')->count();
                $notIntersToday = ListNotInterested::where(DB::raw('DATE(created_at)'), date('Y-m-d'))->get()->unique('leadid')->count();
                
                $MyLeadCountAll = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'No'])->get()->unique('leadid')->count();
                $MyLeadCountToday = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'No'])->where(DB::raw('DATE(created_at)'), date('Y-m-d'))->count();
                
                 $MyLeadCountClientAll = ListMyLead::where(['DormentStatus'=>'No'])->get()->count();
                $MyLeadCountClientToday = ListMyLead::where(['DormentStatus'=>'No'])->where(DB::raw('DATE(created_at)'), date('Y-m-d'))->count();
                
                $HotCountAll = ListMyLead::where(['LeadStatus'=>'hot','ReasonStatus'=>'No'])->count();
                $HotCountToday = ListMyLead::where(['LeadStatus'=>'hot','ReasonStatus'=>'No'])->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->count();

                $untabbedLeadsAll=ListAssignLeads::whereDoesntHave('LeadImpression')->where(['shiftStatus'=>'0'])->count();
                $untabbedLeadsToday=ListAssignLeads::whereDoesntHave('LeadImpression')->where(['shiftStatus'=>'0'])->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->count();
                
                $DormentLeadCountAll = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'Yes'])->count();
                $DormentLeadCountToday = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'Yes'])->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->count();
                
                $ReminderCountAll = AssignReminder::count();
                $ReminderCountToday = AssignReminder::where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->count();
                
                $cusACtivityCountAll = CustomerActivity::count();
                $cusActivityCountToday = CustomerActivity::where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->count();
                
                $TransferLeadsCountAll = TranasferShareLeads::where(['statusType'=>'transfer'])->count();
                $TodayTransferLeadsCount = TranasferShareLeads::where(['statusType'=>'transfer'])->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->count();
                
                $TotalDealsDOne = Inventorysold::where(['status'=>'approved','companyId'=>$company_id])->get()->unique('inventory_id')->count();
                $TodayDealsDOne = Inventorysold::where(['status'=>'approved','companyId'=>$company_id])->where(DB::raw('DATE(created_at)'), date('Y-m-d'))->get()->unique('inventory_id')->count();
                
                $CallingCountTotal = Zongcalling::whereIn('ext',$myEmpDataIxsm)->count();
                $CallingCountToday = Zongcalling::whereIn('ext',$myEmpDataIxsm)->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->count();
                
                $TotalOutgoingCallData = Zongcalling::where(['Type'=>'Outgoing Calls'])->whereIn('ext',$myEmpDataIxsm)->count();
                $TodayOutgoingCallData = Zongcalling::where(['Type'=>'Outgoing Calls'])->whereIn('ext',$myEmpDataIxsm)->where(DB::raw('DATE(datetime)'), date('Y-m-d'))->count();
                
                $TotalIncommingCallData = Zongcalling::where(['Type'=>'Incomming Calls'])->whereIn('ext',$myEmpDataIxsm)->count();
                $TodayIncommingCallData = Zongcalling::where(['Type'=>'Incomming Calls'])->whereIn('ext',$myEmpDataIxsm)->where(DB::raw('DATE(datetime)'), date('Y-m-d'))->count();
                
                $TotalConnectedCallData = Zongcalling::where(['status'=>'Connected'])->whereIn('ext',$myEmpDataIxsm)->count();
                $TodayConnectedCallData = Zongcalling::where(['status'=>'Connected'])->whereIn('ext',$myEmpDataIxsm)->where(DB::raw('DATE(datetime)'), date('Y-m-d'))->count();
                
                $TotalMissedCallData = Zongcalling::where(['status'=>'Missed'])->whereIn('ext',$myEmpDataIxsm)->count();
                $TodayMissedCallData = Zongcalling::where(['status'=>'Missed'])->whereIn('ext',$myEmpDataIxsm)->where(DB::raw('DATE(datetime)'), date('Y-m-d'))->count();
                
                $TotalAnsweredCallData = Zongcalling::where(['status'=>'Answered'])->whereIn('ext',$myEmpDataIxsm)->count();
                $TodayAnsweredCallData = Zongcalling::where(['status'=>'Answered'])->whereIn('ext',$myEmpDataIxsm)->where(DB::raw('DATE(datetime)'), date('Y-m-d'))->count();
                
                $ZOngCallingOutgoing = Zongcalling::where(['Type'=>'Outgoing Calls'])->whereIn('ext',$myEmpDataIxsm)->sum('duration');
                $ZOngCallingOutgoingToday = Zongcalling::where(['Type'=>'Outgoing Calls'])->whereIn('ext',$myEmpDataIxsm)->where(DB::raw('DATE(datetime)'), date('Y-m-d'))->sum('duration');
                 
                $ZOngCallingIncomming = Zongcalling::where(['Type'=>'Incomming Calls'])->whereIn('ext',$myEmpDataIxsm)->whereIn('status',['Missed','Answered'])->sum('duration');
                $ZOngCallingIncommingToday = Zongcalling::where(['Type'=>'Incomming Calls'])->whereIn('ext',$myEmpDataIxsm)->whereIn('status',['Missed','Answered'])->where(DB::raw('DATE(datetime)'), date('Y-m-d'))->sum('duration');
                
                
                $PhoneCountToday = LeadImpression::where(['impType'=>'phone','companyId'=>$company_id])->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->get()->unique('leadid')->count();
                $whatsappCountToday = LeadImpression::where(['impType'=>'whatsapp','companyId'=>$company_id])->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->get()->unique('leadid')->count();
                $smsCountToday = LeadImpression::where(['impType'=>'sms','companyId'=>$company_id])->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->get()->unique('leadid')->count();
                $emailCountToday = LeadImpression::where(['impType'=>'email','companyId'=>$company_id])->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->get()->unique('leadid')->count();
                
                $ReminderTodayData = AssignReminder::with(['getleads','getleads_home','getEmployeeDetails' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(DB::raw('DATE(reminderSet)'), date('Y-m-d'))->orderBy('reminderSet', 'desc')->groupBy('leadid')->get();
                
                $reminData = $this->renderListReminders($ReminderTodayData);
                
                $currentAssignLeads = ListAssignLeads::with(['getleads','getvirginleads','getreminder' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['shiftStatus'=>'0'])->where(DB::raw('DATE(publishAgentDate)'), date('Y-m-d'))->orderBy('id', 'desc')->skip(0)->take(5)->get();
                
                
                 $empAll = Employee::where(['status'=>'active','department_id'=>1,'company_id'=>$company_id])->get();
                 
                 $myEmpData =  Employee::where(['status'=>'active','department_id'=>1,'company_id'=>$company_id])->get();
                
                  $dList = $this->renderListEmp($myEmpData);
                 
                 $empAllS = Employee::where(['status'=>'active','company_id'=>$company_id])->get();
                 
                 $EmployeeCreator = Employee::where(['status'=>'active','Distributor'=>'yes'])->get();
                 $EmployeeSupervisor = Employee::where(['status'=>'active','DistributorSecond'=>'yes'])->get();
                 
                 $getAllProjects = LeadImportDatabase::select('clientProject', DB::raw('count(clientProject) as total_count'))->groupBy('clientProject')->get();
                 
                 $empTeam = empTeam::with(['getEmployeesDetails' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->get();
                
                $Assignreminderlist = Assignreminderlist::where(['status'=>'1'])->get();
                
                $Leadsourcelist = Leadsourcelist::all();
                
                $zongCallingIncommingDataWeekly = Zongcalling::whereDate('datetime',Carbon::now())->groupBy(DB::raw("DATE_FORMAT(datetime, '%Y-%m-%d %H:%i')"))->get();
                
                $timeData=[];
                
                $getAllDuplicates = LeadImportDatabase::select('leadType', DB::raw('count(leadType) as total_count'))->groupBy('leadType')->get();
                
                $TaskToday = Task::where(DB::raw('DATE(created_at)'), date('Y-m-d'))->count();
                $TaskStatus  = TaskStatus::all();
                
                $openTaskToday = Task::where(['taskStatus'=>1])->where(DB::raw('DATE(created_at)'), date('Y-m-d'))->count();
                $PendingTaskToday = Task::where(['taskStatus'=>0])->where(DB::raw('DATE(created_at)'), date('Y-m-d'))->count();
                $OngoingTaskToday = Task::where(['taskStatus'=>2])->where(DB::raw('DATE(created_at)'), date('Y-m-d'))->count();
                $CompleteTaskToday = Task::where(['taskStatus'=>3])->where(DB::raw('DATE(created_at)'), date('Y-m-d'))->count();
                
                $noticeData = Noticeboard::with(['getEmployee' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->latest()->first();
            
            
            $getMonth = [];
            foreach (range(1, 12) as $m) {
                $getMonth[] = date('Y - F', mktime(0, 0, 0, $m, 1));
            }
                
            $AssetType = AssetType::all();
            $Chartlevel1 = Chartlevel1::all();
            $Chartlevel2 = Chartlevel2::all();
            
            $Dimensionlevel1 =Dimensionlevel1::all();
            $Dimensionlevel2 = Dimensionlevel2::all();
            
            $ProjectAdd = ProjectAdd::all();
                
            // SUCCESS
            if ($assignTotal) {
              
                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Dashboard List Found',
                    'myEmpDataIm'=>$myEmpDataIm,
                    'ProjectAdd'=>$ProjectAdd,
                    'AssetType'=>$AssetType,
                    'Chartlevel2'=>$Chartlevel2,
                    'Chartlevel1'=>$Chartlevel1,
                    'Dimensionlevel1'=>$Dimensionlevel1,
                    'Dimensionlevel2'=>$Dimensionlevel2,
                    'getMonth'=>$getMonth,
                    'noticeData'=>$noticeData,
                    'openTaskToday'=>$openTaskToday,
                    'PendingTaskToday'=>$PendingTaskToday,
                    'OngoingTaskToday'=>$OngoingTaskToday,
                    'CompleteTaskToday'=>$CompleteTaskToday,
                    'assignTotal' => number_format($assignTotal),
                    'assignToday' => $assignToday,
                    'TotalNotinter'=>number_format($notIntersAll),
                    'TodayNotinter'=>$notIntersToday,
                    'TotalMyLead'=>number_format($MyLeadCountAll),
                    'TodayMyLead'=>$MyLeadCountToday,
                    'TotalHotleads'=>$HotCountAll,
                    'TodayHotleads'=>$HotCountToday,
                    'untabbedLeadsAll'=>number_format($untabbedLeadsAll),
                    'untabbedLeadsToday'=>$untabbedLeadsToday,
                    'TotalDormentLeads'=>number_format($DormentLeadCountAll),
                    'TodayDormentLeads'=>$DormentLeadCountToday,
                    'ReminderCountAll'=>number_format($ReminderCountAll),
                    'ReminderCountToday'=>$ReminderCountToday,
                    'cusACtivityCountAll'=>number_format($cusACtivityCountAll),
                    'cusActivityCountToday'=>$cusActivityCountToday,
                    'TotalTransferLeads'=>number_format($TransferLeadsCountAll),
                    'TodayTransferLeads'=>$TodayTransferLeadsCount,
                    'TotalDealsDOne'=>number_format($TotalDealsDOne),
                    'TodayDealsDOne'=>$TodayDealsDOne,
                    'CallingCountTotal'=>number_format($CallingCountTotal),
                    'CallingCountToday'=>$CallingCountToday,
                    'TotalOutgoingCallData'=>number_format($TotalOutgoingCallData),
                    'TodayOutgoingCallData'=>$TodayOutgoingCallData,
                    'TotalIncommingCallData'=>number_format($TotalIncommingCallData),
                    'TodayIncommingCallData'=>$TodayIncommingCallData,
                    'TotalConnectedCallData'=>number_format($TotalConnectedCallData),
                    'TodayConnectedCallData'=>$TodayConnectedCallData,
                    'TotalMissedCallData'=>number_format($TotalMissedCallData),
                    'TodayMissedCallData'=>$TodayMissedCallData,
                    'TotalAnsweredCallData'=>number_format($TotalAnsweredCallData),
                    'TodayAnsweredCallData'=>$TodayAnsweredCallData,
                    'ZOngCallingOutgoing'=>$this->FloorAmount($ZOngCallingOutgoing),
                    'ZOngCallingOutgoingToday'=>$this->FloorAmount($ZOngCallingOutgoingToday),
                    'ZOngCallingIncomming'=>$this->FloorAmount($ZOngCallingIncomming),
                    'ZOngCallingIncommingToday'=>$this->FloorAmount($ZOngCallingIncommingToday),
                    'PhoneCountToday'=>$PhoneCountToday,
                    'whatsappCountToday'=>$whatsappCountToday,
                    'smsCountToday'=>$smsCountToday,
                    'emailCountToday'=>$emailCountToday,
                    // 'ubtabbedGraphDashboard'=>$untabbedLeadsAllGraph,
                    // 'GraphCallingZOng'=>$GraphCallingZOng,
                    'TodosTodayData'=>$reminData,
                    'AssignLeadsDashboard'=>$currentAssignLeads,
                    'empAll'=>$empAll,
                    'empAllS'=>$empAllS,
                    'getAllProjects'=>$getAllProjects,
                    'empTeam'=>$empTeam,
                    'Assignreminderlist'=>$Assignreminderlist,
                    'EmployeeCreator'=>$EmployeeCreator,
                    'EmployeeSupervisor'=>$EmployeeSupervisor,
                    'Leadsourcelist'=>$Leadsourcelist,
                    'myEmpData'=>$dList,
                    'timeData'=>$timeData,
                    'dataHours'=>'',
                    'getAllDuplicates'=>$getAllDuplicates,
                    'Task'=>$TaskToday,
                    'TaskStatus'=>$TaskStatus,
                    'clientDatabaseTotal'=>$MyLeadCountClientAll,
                    'clientDatabaseToday'=>$MyLeadCountClientToday,
                ]);
            }

            // FAIL
            else {
                return app(CommonController::class)->getEmptyListResponse();
            }
            
        }else if ($agentId == 'all' && !empty($teamObj)) {
                
                $teamObject = explode(',', $teamObj);
                $teamMemberId = str_replace(array('[',']'),'',$teamObject);
                
                $MyLeadCountClientAll=0;
                $MyLeadCountClientToday=0;
                

                $assignTotal = ListAssignLeads::where(['shiftStatus'=>'0'])->whereIn('agentid',$teamMemberId)->count();
                $assignToday = ListAssignLeads::where(['shiftStatus'=>'0'])->whereIn('agentid',$teamMemberId)->where(DB::raw('DATE(publishAgentDate)'), date('Y-m-d'))->count();
                
                $notIntersAll = ListNotInterested::whereIn('agentid',$teamMemberId)->get()->unique('leadid')->count();
                $notIntersToday = ListNotInterested::whereIn('agentid',$teamMemberId)->where(DB::raw('DATE(created_at)'), date('Y-m-d'))->get()->unique('leadid')->count();
                
                $MyLeadCountAll = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'No'])->whereIn('agentid',$teamMemberId)->get()->unique('leadid')->count();
                $MyLeadCountToday = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'No'])->whereIn('agentid',$teamMemberId)->where(DB::raw('DATE(created_at)'), date('Y-m-d'))->count();
                
                
                
                $HotCountAll = ListMyLead::where(['LeadStatus'=>'hot','ReasonStatus'=>'No'])->whereIn('agentid',$teamMemberId)->count();
                $HotCountToday = ListMyLead::where(['LeadStatus'=>'hot','ReasonStatus'=>'No'])->whereIn('agentid',$teamMemberId)->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->count();

                $untabbedLeadsAll=ListAssignLeads::whereDoesntHave('LeadImpression')->where(['shiftStatus'=>'0'])->whereIn('agentid',$teamMemberId)->count();
                $untabbedLeadsToday=ListAssignLeads::whereDoesntHave('LeadImpression')->where(['shiftStatus'=>'0'])->whereIn('agentid',$teamMemberId)->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->count();
                
                $DormentLeadCountAll = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'Yes'])->whereIn('agentid',$teamMemberId)->count();
                $DormentLeadCountToday = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'Yes'])->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->whereIn('agentid',$teamMemberId)->count();
                
                $ReminderCountAll = AssignReminder::whereIn('agentid',$teamMemberId)->count();
                $ReminderCountToday = AssignReminder::where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->whereIn('agentid',$teamMemberId)->count();
                
                $cusACtivityCountAll = CustomerActivity::whereIn('agentid',$teamMemberId)->count();
                $cusActivityCountToday = CustomerActivity::where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->whereIn('agentid',$teamMemberId)->count();
                
                $TransferLeadsCountAll = TranasferShareLeads::where(['statusType'=>'transfer'])->whereIn('fagent',$teamMemberId)->count();
                $TodayTransferLeadsCount = TranasferShareLeads::where(['statusType'=>'transfer'])->whereIn('fagent',$teamMemberId)->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->count();
                
                $TotalDealsDOne = Inventorysold::where(['status'=>'approved'])->whereIn('employee_id',$teamMemberId)->get()->unique('inventory_id')->count();
                $TodayDealsDOne = Inventorysold::where(['status'=>'approved'])->whereIn('employee_id',$teamMemberId)->where(DB::raw('DATE(created_at)'), date('Y-m-d'))->get()->unique('inventory_id')->count();
                
                $getOutgoingEmp = Employee::whereIn('id',$teamMemberId)->where(['company_id'=>$company_id])->get();
                
                $incomming = [];
                $outgoing = [];
                foreach($getOutgoingEmp as $intes){
                    if($intes->incommingPhone == '' || $intes->incommingPhone == null){
                    }else{
                        $incomming[]=$intes->incommingPhone;
                    }
                    if($intes->outgoingPhone == '' || $intes->outgoingPhone == null){
                    }else{
                        $outgoing[]=$intes->outgoingPhone;
                    }
                }
                
                $inOutCombine = Arr::collapse([$incomming,$outgoing]);
              
                
                $CallingCountTotal = Zongcalling::whereIn('ext',$inOutCombine)->count();
                $CallingCountToday = Zongcalling::whereIn('ext',$inOutCombine)->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->count();
                
                $TotalOutgoingCallData = Zongcalling::whereIn('ext',$outgoing)->where(['Type'=>'Outgoing Calls'])->count();
                $TodayOutgoingCallData = Zongcalling::whereIn('ext',$outgoing)->where(['Type'=>'Outgoing Calls'])->where(DB::raw('DATE(datetime)'), date('Y-m-d'))->count();
                
                $TotalIncommingCallData = Zongcalling::whereIn('ext',$incomming)->where(['Type'=>'Incomming Calls'])->count();
                $TodayIncommingCallData = Zongcalling::whereIn('ext',$incomming)->where(['Type'=>'Incomming Calls'])->where(DB::raw('DATE(datetime)'), date('Y-m-d'))->count();
                
                $TotalConnectedCallData = Zongcalling::whereIn('ext',$outgoing)->where(['status'=>'Connected'])->count();
                $TodayConnectedCallData = Zongcalling::whereIn('ext',$outgoing)->where(['status'=>'Connected'])->where(DB::raw('DATE(datetime)'), date('Y-m-d'))->count();
                
                $TotalMissedCallData = Zongcalling::whereIn('ext',$incomming)->where(['status'=>'Missed'])->count();
                $TodayMissedCallData = Zongcalling::whereIn('ext',$incomming)->where(['status'=>'Missed'])->where(DB::raw('DATE(datetime)'), date('Y-m-d'))->count();
                
                $TotalAnsweredCallData = Zongcalling::whereIn('ext',$incomming)->where(['status'=>'Answered'])->count();
                $TodayAnsweredCallData = Zongcalling::whereIn('ext',$incomming)->where(['status'=>'Answered'])->where(DB::raw('DATE(datetime)'), date('Y-m-d'))->count();
                
                $ZOngCallingOutgoing = Zongcalling::whereIn('ext',$outgoing)->where(['Type'=>'Outgoing Calls'])->sum('duration');
                $ZOngCallingOutgoingToday = Zongcalling::whereIn('ext',$outgoing)->where(['Type'=>'Outgoing Calls'])->where(DB::raw('DATE(datetime)'), date('Y-m-d'))->sum('duration');
                 
                $ZOngCallingIncomming = Zongcalling::whereIn('ext',$incomming)->where(['Type'=>'Incomming Calls'])->whereIn('status',['Missed','Answered'])->sum('duration');
                $ZOngCallingIncommingToday = Zongcalling::whereIn('ext',$incomming)->where(['Type'=>'Incomming Calls'])->whereIn('status',['Missed','Answered'])->where(DB::raw('DATE(datetime)'), date('Y-m-d'))->sum('duration');
                
                
                $PhoneCountToday = LeadImpression::where(['impType'=>'phone'])->whereIn('agentid',$teamMemberId)->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->get()->unique('leadid')->count();
                $whatsappCountToday = LeadImpression::where(['impType'=>'whatsapp'])->whereIn('agentid',$teamMemberId)->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->get()->unique('leadid')->count();
                $smsCountToday = LeadImpression::where(['impType'=>'sms'])->whereIn('agentid',$teamMemberId)->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->get()->unique('leadid')->count();
                $emailCountToday = LeadImpression::where(['impType'=>'email'])->whereIn('agentid',$teamMemberId)->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->get()->unique('leadid')->count();
                
                // $untabbedLeadsAllGraph=ListAssignLeads::whereDoesntHave('LeadImpression')->where(['shiftStatus'=>'0'])->whereIn('agentid',$teamMemberId)->join('leadsimportdatabase', 'leadsimportdatabase.id', '=', 'leadsassigndatabase.leadid')->select('clientProject', DB::raw('count(clientProject) as total_count'))->groupBy('clientProject')->get();
                
                // $GraphCallingZOng = []; 
                //     for ($i=0; $i <7 ; $i++) {
            
                //         $zongCallingIncommingDataWeekly = Zongcalling::where(['Type'=>'Incomming Calls'])->whereIn('ext',$incomming)->whereDate('datetime',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                //         $zongCallingOutgoingDataWeekly = Zongcalling::where(['Type'=>'Outgoing Calls'])->whereIn('ext',$outgoing)->whereDate('datetime',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                
                //         $GraphCallingZOng[] = array(
                //          'format' => Carbon::now()->startOfWeek()->addDay($i)->format('l'),
                //          'datetime' => Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'),
                //          'incommingweekly'=>$zongCallingIncommingDataWeekly,
                //          'outgoingweekly'=>$zongCallingOutgoingDataWeekly,
                //     );
                // }
                
                $ReminderTodayData = AssignReminder::with(['getleads','getleads_home' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->whereIn('agentid',$teamMemberId)->where(DB::raw('DATE(reminderSet)'), date('Y-m-d'))->groupBy('leadid')->get();
                
                $reminData = $this->renderListReminders($ReminderTodayData);
                
                $currentAssignLeads = ListAssignLeads::with(['getleads','getvirginleads','getreminder' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['shiftStatus'=>'0'])->whereIn('agentid',$teamMemberId)->where(DB::raw('DATE(publishAgentDate)'), date('Y-m-d'))->orderBy('id', 'desc')->skip(0)->take(5)->get();
                
                
                $empAll = Employee::where(['status'=>'active','department_id'=>1,'company_id'=>$company_id])->get();
                
                $myEmpData =  Employee::where(['status'=>'active','department_id'=>1])->whereIn('id',$teamMemberId)->get();
                
                $dList = $this->renderListEmp($myEmpData);
                
                $empAllS = Employee::where(['status'=>'active','company_id'=>$company_id])->get();
                
                $getAllProjects = LeadImportDatabase::select('clientProject', DB::raw('count(clientProject) as total_count'))->groupBy('clientProject')->get();
                
                $empTeam = empTeam::with(['getEmployeesDetails' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->whereIn('teamLeadId',$teamMemberId)->get();
                
                 $Assignreminderlist = Assignreminderlist::where(['status'=>'1'])->get();
                
                $EmployeeCreator = Employee::where(['status'=>'active','Distributor'=>'yes'])->get();
                $EmployeeSupervisor = Employee::where(['status'=>'active','Distributor'=>'yes','DistributorSecond'=>'yes'])->get();
                
                $Leadsourcelist = Leadsourcelist::all();
                
                $getAllDuplicates = LeadImportDatabase::select('leadType', DB::raw('count(leadType) as total_count'))->groupBy('leadType')->get();
                
                $TaskToday = Task::whereIn('agentId',$teamMemberId)->where(DB::raw('DATE(created_at)'), date('Y-m-d'))->count();
                
                $TaskStatus  = TaskStatus::all();
                
                $openTaskToday = Task::whereIn('agentId',$teamMemberId)->where(['taskStatus'=>1])->where(DB::raw('DATE(created_at)'), date('Y-m-d'))->count();
                $PendingTaskToday = Task::whereIn('agentId',$teamMemberId)->where(['taskStatus'=>0])->where(DB::raw('DATE(created_at)'), date('Y-m-d'))->count();
                $OngoingTaskToday = Task::whereIn('agentId',$teamMemberId)->where(['taskStatus'=>2])->where(DB::raw('DATE(created_at)'), date('Y-m-d'))->count();
                $CompleteTaskToday = Task::whereIn('agentId',$teamMemberId)->where(['taskStatus'=>3])->where(DB::raw('DATE(created_at)'), date('Y-m-d'))->count();
                
                $noticeData = Noticeboard::with(['getEmployee' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->latest()->first();
            
             $getMonth = [];
            foreach (range(1, 12) as $m) {
                $getMonth[] = date('m - F', mktime(0, 0, 0, $m, 1));
            }
            
            $AssetType = AssetType::all();
            $Chartlevel1 = Chartlevel1::all();
                $Chartlevel2 = Chartlevel2::all();
                
                $Dimensionlevel1 =Dimensionlevel1::all();
$Dimensionlevel2 = Dimensionlevel2::all();
 $ProjectAdd = ProjectAdd::all();
                
            // SUCCESS
            if ($assignTotal) {
              
                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Dashboard List Found',
                    'ProjectAdd'=>$ProjectAdd,
                    'AssetType'=>$AssetType,
                    'Chartlevel2'=>$Chartlevel2,
                    'Dimensionlevel1'=>$Dimensionlevel1,
                    'Dimensionlevel2'=>$Dimensionlevel2,
                    'getMonth'=>$getMonth,
                    'Chartlevel1'=>$Chartlevel1,
                    'openTaskToday'=>$openTaskToday,
                    'noticeData'=>$noticeData,
                    'PendingTaskToday'=>$PendingTaskToday,
                    'OngoingTaskToday'=>$OngoingTaskToday,
                    'CompleteTaskToday'=>$CompleteTaskToday,
                    'assignTotal' => number_format($assignTotal),
                    'assignToday' => $assignToday,
                    'TotalNotinter'=>number_format($notIntersAll),
                    'TodayNotinter'=>$notIntersToday,
                    'TotalMyLead'=>number_format($MyLeadCountAll),
                    'TodayMyLead'=>$MyLeadCountToday,
                    'TotalHotleads'=>$HotCountAll,
                    'TodayHotleads'=>$HotCountToday,
                    'untabbedLeadsAll'=>number_format($untabbedLeadsAll),
                    'untabbedLeadsToday'=>$untabbedLeadsToday,
                    'TotalDormentLeads'=>number_format($DormentLeadCountAll),
                    'TodayDormentLeads'=>$DormentLeadCountToday,
                    'ReminderCountAll'=>number_format($ReminderCountAll),
                    'ReminderCountToday'=>$ReminderCountToday,
                    'cusACtivityCountAll'=>number_format($cusACtivityCountAll),
                    'cusActivityCountToday'=>$cusActivityCountToday,
                    'TotalTransferLeads'=>number_format($TransferLeadsCountAll),
                    'TodayTransferLeads'=>$TodayTransferLeadsCount,
                    'TotalDealsDOne'=>number_format($TotalDealsDOne),
                    'TodayDealsDOne'=>$TodayDealsDOne,
                    'CallingCountTotal'=>number_format($CallingCountTotal),
                    'CallingCountToday'=>$CallingCountToday,
                    'TotalOutgoingCallData'=>number_format($TotalOutgoingCallData),
                    'TodayOutgoingCallData'=>$TodayOutgoingCallData,
                    'TotalIncommingCallData'=>number_format($TotalIncommingCallData),
                    'TodayIncommingCallData'=>$TodayIncommingCallData,
                    'TotalConnectedCallData'=>number_format($TotalConnectedCallData),
                    'TodayConnectedCallData'=>$TodayConnectedCallData,
                    'TotalMissedCallData'=>number_format($TotalMissedCallData),
                    'TodayMissedCallData'=>$TodayMissedCallData,
                    'TotalAnsweredCallData'=>number_format($TotalAnsweredCallData),
                    'TodayAnsweredCallData'=>$TodayAnsweredCallData,
                    'ZOngCallingOutgoing'=>$this->FloorAmount($ZOngCallingOutgoing),
                    'ZOngCallingOutgoingToday'=>$this->FloorAmount($ZOngCallingOutgoingToday),
                    'ZOngCallingIncomming'=>$this->FloorAmount($ZOngCallingIncomming),
                    'ZOngCallingIncommingToday'=>$this->FloorAmount($ZOngCallingIncommingToday),
                    'PhoneCountToday'=>$PhoneCountToday,
                    'whatsappCountToday'=>$whatsappCountToday,
                    'smsCountToday'=>$smsCountToday,
                    'emailCountToday'=>$emailCountToday,
                    // 'ubtabbedGraphDashboard'=>$untabbedLeadsAllGraph,
                    // 'GraphCallingZOng'=>$GraphCallingZOng,
                    'TodosTodayData'=>$reminData,
                    'AssignLeadsDashboard'=>$currentAssignLeads,
                    'empAll'=>$empAll,
                    'empAllS'=>$empAllS,
                    'getAllProjects'=>$getAllProjects,
                    'empTeam'=>$empTeam,
                    'Assignreminderlist'=>$Assignreminderlist,
                    'EmployeeCreator'=>$EmployeeCreator,
'EmployeeSupervisor'=>$EmployeeSupervisor,
'Leadsourcelist'=>$Leadsourcelist,
'myEmpData'=>$dList,
'timeData'=>'',
'getAllDuplicates'=>$getAllDuplicates,
'Task'=>$TaskToday,
'TaskStatus'=>$TaskStatus,
'clientDatabaseTotal'=>0,
                    'clientDatabaseToday'=>0,
                ]);
            }

            // FAIL
            else {
                return app(CommonController::class)->getEmptyListResponse();
            }
            
        }

        // AGENT ID NOT SELECTED
        else {
            
           
            
             $MyLeadCountClientAll=0;
                $MyLeadCountClientToday=0;
            
            $assignTotal = ListAssignLeads::where(['shiftStatus'=>'0','agentid'=>$agentId,'companyId'=>$company_id])->count();
            $assignToday = ListAssignLeads::where(['shiftStatus'=>'0','agentid'=>$agentId,'companyId'=>$company_id])->where(DB::raw('DATE(publishAgentDate)'), date('Y-m-d'))->count();
            
            $notIntersAll = ListNotInterested::where(['agentid'=>$agentId,'companyId'=>$company_id])->get()->unique('leadid')->count();
            $notIntersToday = ListNotInterested::where(['agentid'=>$agentId,'companyId'=>$company_id])->where(DB::raw('DATE(created_at)'), date('Y-m-d'))->get()->unique('leadid')->count();
            
            $MyLeadCountAll = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'No','agentid'=>$agentId,'companyId'=>$company_id])->get()->count();
            $MyLeadCountToday = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'No','agentid'=>$agentId,'companyId'=>$company_id])->where(DB::raw('DATE(created_at)'), date('Y-m-d'))->count();
            
            $HotCountAll = ListMyLead::where(['LeadStatus'=>'hot','ReasonStatus'=>'No','agentid'=>$agentId,'companyId'=>$company_id])->count();
            $HotCountToday = ListMyLead::where(['LeadStatus'=>'hot','ReasonStatus'=>'No','agentid'=>$agentId,'companyId'=>$company_id])->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->count();
            
            $untabbedLeadsAll=ListAssignLeads::whereDoesntHave('LeadImpression')->where(['shiftStatus'=>'0','agentid'=>$agentId,'companyId'=>$company_id])->count();
            $untabbedLeadsToday=ListAssignLeads::whereDoesntHave('LeadImpression')->where(['shiftStatus'=>'0','agentid'=>$agentId,'companyId'=>$company_id])->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->count();
            
            $DormentLeadCountAll = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'Yes','agentid'=>$agentId,'companyId'=>$company_id])->count();
            $DormentLeadCountToday = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'Yes','agentid'=>$agentId,'companyId'=>$company_id])->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->count();
            
            $ReminderCountAll = AssignReminder::where(['agentid'=>$agentId,'companyId'=>$company_id])->count();
            $ReminderCountToday = AssignReminder::where(['agentid'=>$agentId,'companyId'=>$company_id])->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->count();
            
            $cusACtivityCountAll = CustomerActivity::where(['agentid'=>$agentId,'companyId'=>$company_id])->count();
            $cusActivityCountToday = CustomerActivity::where(['agentid'=>$agentId,'companyId'=>$company_id])->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->count();
            
            $TransferLeadsCountAll = TranasferShareLeads::where(['fagent'=>$agentId,'statusType'=>'transfer','companyId'=>$company_id])->count();
            $TodayTransferLeadsCount = TranasferShareLeads::where(['fagent'=>$agentId,'statusType'=>'transfer','companyId'=>$company_id])->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->count();
            
            $TotalDealsDOne = Inventorysold::where(['employee_id'=>$agentId,'status'=>'approved','companyId'=>$company_id])->get()->unique('inventory_id')->count();
            $TodayDealsDOne = Inventorysold::where(['employee_id'=>$agentId,'status'=>'approved','companyId'=>$company_id])->where(DB::raw('DATE(created_at)'), date('Y-m-d'))->get()->unique('inventory_id')->count();
            
            $getOutgoingEmp = Employee::where(['id'=>$agentId,'company_id'=>$company_id])->first();
            
            $CallingCountTotal = Zongcalling::whereIn('ext',[$getOutgoingEmp->incommingPhone,$getOutgoingEmp->outgoingPhone])->count();
            $CallingCountToday = Zongcalling::whereIn('ext',[$getOutgoingEmp->incommingPhone,$getOutgoingEmp->outgoingPhone])->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->count();
            
            $TotalOutgoingCallData = Zongcalling::where(['Type'=>'Outgoing Calls','ext'=>$getOutgoingEmp->outgoingPhone])->count();
            $TodayOutgoingCallData = Zongcalling::where(['Type'=>'Outgoing Calls','ext'=>$getOutgoingEmp->outgoingPhone])->where(DB::raw('DATE(datetime)'), date('Y-m-d'))->count();
            
            $TotalIncommingCallData = Zongcalling::where(['Type'=>'Incomming Calls','ext'=>$getOutgoingEmp->incommingPhone])->count();
            $TodayIncommingCallData = Zongcalling::where(['Type'=>'Incomming Calls','ext'=>$getOutgoingEmp->incommingPhone])->where(DB::raw('DATE(datetime)'), date('Y-m-d'))->count();
            
            $TotalConnectedCallData = Zongcalling::where(['status'=>'Connected','ext'=>$getOutgoingEmp->outgoingPhone])->count();
            $TodayConnectedCallData = Zongcalling::where(['status'=>'Connected','ext'=>$getOutgoingEmp->outgoingPhone])->where(DB::raw('DATE(datetime)'), date('Y-m-d'))->count();
            
            $TotalMissedCallData = Zongcalling::where(['status'=>'Missed','ext'=>$getOutgoingEmp->incommingPhone])->count();
            $TodayMissedCallData = Zongcalling::where(['status'=>'Missed','ext'=>$getOutgoingEmp->incommingPhone])->where(DB::raw('DATE(datetime)'), date('Y-m-d'))->count();
            
            $TotalAnsweredCallData = Zongcalling::where(['status'=>'Answered','ext'=>$getOutgoingEmp->incommingPhone])->count();
            $TodayAnsweredCallData = Zongcalling::where(['status'=>'Answered','ext'=>$getOutgoingEmp->incommingPhone])->where(DB::raw('DATE(datetime)'), date('Y-m-d'))->count();
            
            $ZOngCallingOutgoing = Zongcalling::where(['Type'=>'Outgoing Calls','ext'=>$getOutgoingEmp->outgoingPhone])->sum('duration');
            $ZOngCallingOutgoingToday = Zongcalling::where(['Type'=>'Outgoing Calls','ext'=>$getOutgoingEmp->outgoingPhone])->where(DB::raw('DATE(datetime)'), date('Y-m-d'))->sum('duration');
             
            $ZOngCallingIncomming = Zongcalling::where(['Type'=>'Incomming Calls','ext'=>$getOutgoingEmp->incommingPhone])->whereIn('status',['Missed','Answered'])->sum('duration');
            $ZOngCallingIncommingToday = Zongcalling::where(['Type'=>'Incomming Calls','ext'=>$getOutgoingEmp->incommingPhone])->whereIn('status',['Missed','Answered'])->where(DB::raw('DATE(datetime)'), date('Y-m-d'))->sum('duration');
            
            
            $PhoneCountToday = LeadImpression::where(['agentid'=>$agentId,'impType'=>'phone','companyId'=>$company_id])->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->get()->unique('leadid')->count();
            $whatsappCountToday = LeadImpression::where(['agentid'=>$agentId,'impType'=>'whatsapp','companyId'=>$company_id])->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->get()->unique('leadid')->count();
            $smsCountToday = LeadImpression::where(['agentid'=>$agentId,'impType'=>'sms','companyId'=>$company_id])->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->get()->unique('leadid')->count();
            $emailCountToday = LeadImpression::where(['agentid'=>$agentId,'impType'=>'email','companyId'=>$company_id])->where(DB::raw('DATE(updated_at)'), date('Y-m-d'))->get()->unique('leadid')->count();
            
            // $untabbedLeadsAllGraph=ListAssignLeads::whereDoesntHave('LeadImpression')->where(['agentid'=>$agentId,'shiftStatus'=>'0'])->join('leadsimportdatabase', 'leadsimportdatabase.id', '=', 'leadsassigndatabase.leadid')->select('clientProject', DB::raw('count(clientProject) as total_count'))->groupBy('clientProject')->get();
            
            // $GraphCallingZOng = []; 
            //     for ($i=0; $i <7 ; $i++) {
            
            //         $zongCallingIncommingDataWeekly = Zongcalling::where(['Type'=>'Incomming Calls','ext'=>$getOutgoingEmp->incommingPhone])->whereDate('datetime',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
            //         $zongCallingOutgoingDataWeekly = Zongcalling::where(['Type'=>'Outgoing Calls','ext'=>$getOutgoingEmp->outgoingPhone])->whereDate('datetime',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
            
            //         $GraphCallingZOng[] = array(
            //          'format' => Carbon::now()->startOfWeek()->addDay($i)->format('l'),
            //          'datetime' => Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'),
            //          'incommingweekly'=>$zongCallingIncommingDataWeekly,
            //          'outgoingweekly'=>$zongCallingOutgoingDataWeekly,
            //     );
            // }
            
            $ReminderTodayData = AssignReminder::with(['getleads','getleads_home' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['agentid'=>$agentId])->where(DB::raw('DATE(reminderSet)'), date('Y-m-d'))->groupBy('leadid')->get();
            
            $reminData = $this->renderListReminders($ReminderTodayData);
            
            $currentAssignLeads = ListAssignLeads::with(['getleads','getvirginleads','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['agentid'=>$agentId,'shiftStatus'=>'0'])->where(DB::raw('DATE(publishAgentDate)'), date('Y-m-d'))->orderBy('id', 'desc')->skip(0)->take(5)->get();
            
            $empAll = Employee::where(['status'=>'active','department_id'=>1,'company_id'=>$company_id])->get();
            $empAllS = Employee::where(['status'=>'active','company_id'=>$company_id])->get();
            
             $getAllProjects = LeadImportDatabase::select('clientProject', DB::raw('count(clientProject) as total_count'))->groupBy('clientProject')->get();
            
            $empTeam = empTeam::all();
            
            $myEmpData =  Employee::where(['status'=>'active','department_id'=>1,'id'=>$agentId])->get();
                
             $dList = $this->renderListEmp($myEmpData);
            
            $Assignreminderlist = Assignreminderlist::where(['status'=>'1'])->get();
            
            $EmployeeCreator = Employee::where(['status'=>'active','Distributor'=>'yes'])->get();
$EmployeeSupervisor = Employee::where(['status'=>'active','Distributor'=>'yes','DistributorSecond'=>'yes'])->get();
            
            $Leadsourcelist = Leadsourcelist::all();
            
            $getAllDuplicates = LeadImportDatabase::select('leadType', DB::raw('count(leadType) as total_count'))->groupBy('leadType')->get();
            // SUCCESS
            
            $TaskToday = Task::where(['agentId'=>$agentId])->where(DB::raw('DATE(created_at)'), date('Y-m-d'))->count();
           
            $TaskStatus  = TaskStatus::all();
            
            $openTaskToday = Task::where(['agentId'=>$agentId])->where(['taskStatus'=>1])->where(DB::raw('DATE(created_at)'), date('Y-m-d'))->count();
            $PendingTaskToday = Task::where(['agentId'=>$agentId])->where(['taskStatus'=>0])->where(DB::raw('DATE(created_at)'), date('Y-m-d'))->count();
            $OngoingTaskToday = Task::where(['agentId'=>$agentId])->where(['taskStatus'=>2])->where(DB::raw('DATE(created_at)'), date('Y-m-d'))->count();
            $CompleteTaskToday = Task::where(['agentId'=>$agentId])->where(['taskStatus'=>3])->where(DB::raw('DATE(created_at)'), date('Y-m-d'))->count();
            
            $noticeData = Noticeboard::with(['getEmployee' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->latest()->first();
            
             $getMonth = [];
            foreach (range(1, 12) as $m) {
                $getMonth[] = date('Y - F', mktime(0, 0, 0, $m, 1));
            }
            $AssetType = AssetType::all();
            $Chartlevel1 = Chartlevel1::all();
             $Chartlevel2 = Chartlevel2::all();
             
             $Dimensionlevel1 =Dimensionlevel1::all();
$Dimensionlevel2 = Dimensionlevel2::all();
 $ProjectAdd = ProjectAdd::all();
            
            return response()->json([
                'code' => '200',
                'success' => true,
                'message' => 'Dashboard List Found',
                'ProjectAdd'=>$ProjectAdd,
                'AssetType'=>$AssetType,
                'Chartlevel2'=>$Chartlevel2,
                'Chartlevel1'=>$Chartlevel1,
                'Dimensionlevel1'=>$Dimensionlevel1,
'Dimensionlevel2'=>$Dimensionlevel2,
                'openTaskToday'=>$openTaskToday,
                'getMonth'=>$getMonth,
                'noticeData'=>$noticeData,
                'PendingTaskToday'=>$PendingTaskToday,
                'OngoingTaskToday'=>$OngoingTaskToday,
                'CompleteTaskToday'=>$CompleteTaskToday,
                'assignTotal' => number_format($assignTotal),
                'assignToday' => $assignToday,
                'TotalNotinter'=>number_format($notIntersAll),
                'TodayNotinter'=>$notIntersToday,
                'TotalMyLead'=>number_format($MyLeadCountAll),
                'TodayMyLead'=>$MyLeadCountToday,
                'TotalHotleads'=>$HotCountAll,
                'TodayHotleads'=>$HotCountToday,
                'untabbedLeadsAll'=>number_format($untabbedLeadsAll),
                'untabbedLeadsToday'=>$untabbedLeadsToday,
                'TotalDormentLeads'=>number_format($DormentLeadCountAll),
                'TodayDormentLeads'=>$DormentLeadCountToday,
                'ReminderCountAll'=>number_format($ReminderCountAll),
                'ReminderCountToday'=>$ReminderCountToday,
                'cusACtivityCountAll'=>number_format($cusACtivityCountAll),
                'cusActivityCountToday'=>$cusActivityCountToday,
                'TotalTransferLeads'=>number_format($TransferLeadsCountAll),
                'TodayTransferLeads'=>$TodayTransferLeadsCount,
                'TotalDealsDOne'=>number_format($TotalDealsDOne),
                'TodayDealsDOne'=>$TodayDealsDOne,
                'CallingCountTotal'=>number_format($CallingCountTotal),
                'CallingCountToday'=>$CallingCountToday,
                'TotalOutgoingCallData'=>number_format($TotalOutgoingCallData),
                'TodayOutgoingCallData'=>$TodayOutgoingCallData,
                'TotalIncommingCallData'=>number_format($TotalIncommingCallData),
                'TodayIncommingCallData'=>$TodayIncommingCallData,
                'TotalConnectedCallData'=>number_format($TotalConnectedCallData),
                'TodayConnectedCallData'=>$TodayConnectedCallData,
                'TotalMissedCallData'=>number_format($TotalMissedCallData),
                'TodayMissedCallData'=>$TodayMissedCallData,
                'TotalAnsweredCallData'=>number_format($TotalAnsweredCallData),
                'TodayAnsweredCallData'=>$TodayAnsweredCallData,
                'ZOngCallingOutgoing'=>$this->FloorAmount($ZOngCallingOutgoing),
                'ZOngCallingOutgoingToday'=>$this->FloorAmount($ZOngCallingOutgoingToday),
                'ZOngCallingIncomming'=>$this->FloorAmount($ZOngCallingIncomming),
                'ZOngCallingIncommingToday'=>$this->FloorAmount($ZOngCallingIncommingToday),
                'PhoneCountToday'=>$PhoneCountToday,
                'whatsappCountToday'=>$whatsappCountToday,
                'smsCountToday'=>$smsCountToday,
                'emailCountToday'=>$emailCountToday,
                // 'ubtabbedGraphDashboard'=>$untabbedLeadsAllGraph,
                // 'GraphCallingZOng'=>$GraphCallingZOng,
                'TodosTodayData'=>$reminData,
                'AssignLeadsDashboard'=>$currentAssignLeads,
                'empAll'=>$empAll,
                'empAllS'=>$empAllS,
                'getAllProjects'=>$getAllProjects,
                'empTeam'=>$empTeam,
                'Assignreminderlist'=>$Assignreminderlist,
                'EmployeeCreator'=>$EmployeeCreator,
'EmployeeSupervisor'=>$EmployeeSupervisor,
'Leadsourcelist'=>$Leadsourcelist,
'myEmpData'=>$dList,
'timeData'=>'',
'getAllDuplicates'=>$getAllDuplicates,
'Task'=>$TaskToday,
'TaskStatus'=>$TaskStatus,
'clientDatabaseTotal'=>0,
                    'clientDatabaseToday'=>0,
            ]);
          
            
        }
    }
    
    
    public function renderListReminders(Object $apiResponse)
    {
        $data = [];

        foreach ($apiResponse as $key => $value) {
            
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                // 'value'=>$value,
                "clientProject"=>$value->getleads == '' || $value->getleads == null ? '' : $value->getleads->clientProject,
                "clientName"=>$value->getleads == '' || $value->getleads == null ? '' : $value->getleads->clientName,
                "clientNameShort"=>$value->getleads == '' || $value->getleads == null ? '' : Str::limit($value->getleads->clientName,10),
                "clientPhone"=>$value->getleads == '' || $value->getleads == null ? '' : $value->getleads->clientPhone,
                "leadid"=>$value->leadid,
                "customerId"=>$value->customerId,
                "agentid"=>$value->agentid,
                "reminderSet"=>date("d M Y",strtotime($value->reminderSet)),
                "reminderSetTime"=>date("h:i:s",strtotime($value->reminderSet)),
                "priority"=>$value->priority,
                "reminderinfo"=>$value->reminderinfo,
                "reminderinfoShort"=>Str::limit($value->reminderinfo, 20),
                 "emp_name"=>$value->getEmployeeDetails == '' || $value->getEmployeeDetails == null ? '' : $value->getEmployeeDetails->emp_name,
            ]);
        }

        return $data;
    }
    
    
    public function renderListEmp(Object $apiResponse)
    {
        $data = [];
        $myData = date("Y-m-d"); 

        foreach ($apiResponse as $key => $value) {
            
            $AttemptedCalls = Zongcalling::whereIn('ext',[$value->outgoingPhone])->where(DB::raw('DATE(datetime)'), $myData)->count();
            
             $connectedCalls = Zongcalling::where(['status'=>'Connected'])->whereIn('ext',[$value->outgoingPhone])->where('duration', '>=', 30)->where('duration', '<', 90)->where(DB::raw('DATE(datetime)'), $myData)->count();
            $VerifiedCalls = Zongcalling::whereIn('ext',[$value->outgoingPhone])->where('duration', '>=', 90)->where(DB::raw('DATE(datetime)'), $myData)->count();
            
            $mettingAlligned = CustomerActivity::where(['agentid'=>$value->id,'typeId'=>'24'])->where(DB::raw('DATE(created_at)'), $myData)->count();
            
            $mettingDone = CustomerActivity::where(['agentid'=>$value->id,'typeId'=>'18'])->where(DB::raw('DATE(created_at)'), $myData)->count();
            
            $SelfLead = ListMyLead::where(['agentid'=>$value->id,'leadid'=>'0'])->where(DB::raw('DATE(created_at)'), $myData)->count();
            
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "emp_name"=>$value->emp_name,
                'AttemptedCalls'=>$AttemptedCalls,
                'connectedCalls'=>$connectedCalls,
                'VerifiedCalls'=>$VerifiedCalls,
                'mettingAlligned'=>$mettingAlligned,
                'mettingDone'=>$mettingDone,
                'SelfLeadsdata'=>$SelfLead,
            ]);
        }

        return $data;
    }

    // PREPARE LIST
    public function renderList(Object $apiResponse ,$StartDates,$EndDate)
    {
        $data = [];

        foreach ($apiResponse as $key => $value) {
            
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
            ]);
        }

        return $data;
    }
    
    public function FloorAmount($DurationSum)
    {
        $init = $DurationSum;
        $hours = floor($init / 3600);
        $minutes = floor(($init / 60) % 60);
        $seconds = $init % 60;
        
        return "$hours:$minutes:$seconds";
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BanklistRequest $request)
    {
        
        if($request->hasFile("Attachement")){
            $image = $request->file("Attachement");
            $ext = $image->extension();
            $image_name=time().'.'.$ext;
            $image->storeAs('/public/bankLogo/',$image_name);
            $FileData = "public/storage/bankLogo/".$image_name;
            
        }else{
            $FileData="";
        }
        
        
        $response = Banklist::create([
            'title' =>  $request->title,
            'branch' =>  $request->branchtitle,
            'accountno' =>  $request->accountno,
            'iban' =>  $request->iban,
            'bankname' =>  $request->bankname,
            'branchcode' =>  $request->branchcode,
            'City' =>  $request->City,
            'SwiftCode' =>  $request->SwiftCode,
            'NiftCode' =>  $request->NiftCode,
            'bankimage' =>  $FileData,
        ]);
        
        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $response,
            'code'=>'200'
        ], 200);
        
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Banklist $banklist)
    {
        $response = Banklist::where(['id'=>$banklist->id])->get();
            
        if($response){
            return response()->json([
                'message' => "Data retrieved successfully!",
                'data' => $response,
                'code'=>'200'
            ], 200);
        
        }else{
           
            return response()->json([
                'message' => "Data Not Found!",
                'code'=>'203'
            ], 203);
        
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(BanklistRequest $request, Banklist $banklist,$id="")
    {

        $checkFiles = Banklist::findOrFail($banklist->id);
        
        if($request->hasFile("Attachement")){
            $image = $request->file("Attachement");
            $ext = $image->extension();
            $image_name=time().'.'.$ext;
            $image->storeAs('/public/bankLogo/',$image_name);
            $image_names = "public/storage/bankLogo/".$image_name;
        }else{
            $image_names=$checkFiles->bankimage;
        }

        $banklist->update([
            'title' =>  $request->title,
            'branch' =>  $request->branchtitle,
            'accountno' =>  $request->accountno,
            'iban' =>  $request->iban,
            'bankname' =>  $request->bankname,
            'branchcode' =>  $request->branchcode,
            'bankimage' =>  $image_names,
            'City' =>  $request->City,
            'SwiftCode' =>  $request->SwiftCode,
            'NiftCode' =>  $request->NiftCode,
        ]);
        
        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $checkFiles,
            'code'=>'200'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(BanklistRequest $request, Banklist $banklist)
    {
        $banklist->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code'=>'200'
        ], 200);
    }
    
    
    
    
}