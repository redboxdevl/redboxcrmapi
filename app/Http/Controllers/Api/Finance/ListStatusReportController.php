<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\BanklistRequest;
use App\Models\Finance\Banklist;
use App\Models\DocumentType;
use App\Models\Issuingauthority;
use App\Models\propertyType;
use App\Models\Purposeprop;
use App\Models\City;
use App\Models\HR\Employee;
use App\Models\ListAssignLeads;
use App\Models\ListMyLead;
use App\Models\Inventory\Inventorysold;
use App\Models\ListNotInterested;
use App\Models\Zongcalling;
use App\Models\LeadImpression;

use App\Models\LeadImportDatabase;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;
use DB;

class ListStatusReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orderBy = $request->orderBy ?? "id";
        $orderType = $request->orderType ?? "desc";
        $perPage = $request->perPage ?? "12";
        $page = $request->page ?? "1";
        $q = $request->q ?? "";
        $agentId = $request->agentId;
        $teamObj = $request->teamObj;
        $StartDates = $request->StartDates ?? date('Y-m-d');
        $EndDate = $request->EndDate ?? date('Y-m-d');
        

        if (is_null($agentId) && empty($teamObj)) {
            
            $response = Employee::where(['status'=>'active','department_id'=>'1'])->orderBy($orderBy, $orderType)->get();
            $count = Employee::where(['status'=>'active','department_id'=>'1'])->count();
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response,$StartDates,$EndDate);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'LIst Status Report List Found',
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
        }   else if ($agentId == 'all' && empty($teamObj)) {

            
            $response = Employee::where(['status'=>'active','department_id'=>'1'])->orderBy($orderBy, $orderType)->get();
            $count = Employee::where(['status'=>'active','department_id'=>'1'])->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response,$StartDates,$EndDate);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Leads Report List Found',
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
            
        }else if ($agentId == 'all' && !empty($teamObj)) {
            
            $myArray2 = explode(',', $teamObj);
            $myArray = str_replace(array('[',']'),'',$myArray2);
            
            $response = Employee::where(['status'=>'active','department_id'=>'1'])->whereIn('id',$myArray)->orderBy($orderBy, $orderType)->get();
                
            $count = Employee::where(['status'=>'active','department_id'=>'1'])->whereIn('id',$myArray)->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response,$StartDates,$EndDate);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Leads Report List Found',
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
            
        }

        // AGENT ID NOT SELECTED
        else {
            return app(CommonController::class)->getExceptionResponse("Agent Id is required");
        }
    }

    // PREPARE LIST
    public function renderList(Object $apiResponse ,$StartDates,$EndDate)
    {
        $data = [];
       $totalAllOutgoing = 0;
        foreach ($apiResponse as $key => $value) {
            
            $totalOutGoing = Zongcalling::where(['Type'=>'Outgoing Calls','ext'=>$value->outgoingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($StartDates,$EndDate))->get()->unique('phone')->count();
            $totalConnected = Zongcalling::where(['status'=>'Connected','ext'=>$value->outgoingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($StartDates,$EndDate))->get()->unique('phone')->count();
            $followupCalls = Zongcalling::where(['Source'=>'follow-up'])->whereIn('ext',[$value->incommingPhone,$value->outgoingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($StartDates,$EndDate))->get()->unique('phone')->count();
            $personalCalls = Zongcalling::where(['Source'=>'personal-call'])->whereIn('ext',[$value->incommingPhone,$value->outgoingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($StartDates,$EndDate))->get()->unique('phone')->count();
            $outgoingTawkTime = Zongcalling::where(['Type'=>'Outgoing Calls','ext'=>$value->outgoingPhone])->whereBetween(DB::raw('DATE(datetime)'), array($StartDates,$EndDate))->sum('duration');
            $todayAssignLeads = ListAssignLeads::where(['agentid'=>$value->id])->whereBetween(DB::raw('DATE(created_at)'), array($StartDates,$EndDate))->count();
            
            $totalIcomming = Zongcalling::where(['Type'=>'Incomming Calls','ext'=>$value->incommingPhone])->whereIn('status',['Answered','Missed'])->whereBetween(DB::raw('DATE(datetime)'), array($StartDates,$EndDate))->get()->unique('phone')->count();
            $totalIcommingAns = Zongcalling::where(['Type'=>'Incomming Calls','ext'=>$value->incommingPhone])->whereIn('status',['Answered'])->whereBetween(DB::raw('DATE(datetime)'), array($StartDates,$EndDate))->get()->unique('phone')->count();
            
            $incommingTawkTime = Zongcalling::where(['Type'=>'Incomming Calls','ext'=>$value->incommingPhone,'status'=>'Answered'])->whereBetween(DB::raw('DATE(datetime)'), array($StartDates,$EndDate))->sum('duration');
            
            $commentstatuscount =  Zongcalling::whereIn('ext',[$value->incommingPhone,$value->outgoingPhone])->whereIn('CommentStatus',['moderate','not interested','interested','hot client'])->whereBetween(DB::raw('DATE(datetime)'), array($StartDates,$EndDate))->count();
            
            $sourcescount =  Zongcalling::whereIn('ext',[$value->incommingPhone,$value->outgoingPhone])->whereIn('Source',['youtube','social media','sms','web','reference','follow-up','personal-call'])->whereBetween(DB::raw('DATE(datetime)'), array($StartDates,$EndDate))->count();
            
            $clientstatuscount =  Zongcalling::whereIn('ext',[$value->incommingPhone,$value->outgoingPhone])->whereIn('ClientStatus',['buyer','seller','agent'])->whereBetween(DB::raw('DATE(datetime)'), array($StartDates,$EndDate))->count();
            
            array_push($data, (object) [
            "id" => $value->id,
            "created_at" => $value->created_at,
            "updated_at" => $value->updated_at,
            "deleted_at" => $value->deleted_at,
            "emp_name"=>$value->emp_name,
            "StartDates"=>$StartDates,
            "EndDate"=>$EndDate,
            "totalOutGoing"=>$totalOutGoing,
            "totalConnected"=>$totalConnected,
            "followupCalls"=>$followupCalls,
            "personalCalls"=>$personalCalls,
            "outgoingTawkTime"=>$this->FloorAmount($outgoingTawkTime),
            "outgoingTawkTimed"=>$outgoingTawkTime,
            "todayAssignLeads"=>$todayAssignLeads,
            "totalIcomming"=>$totalIcomming,
            "totalIcommingAns"=>$totalIcommingAns,
            "totalMissedData"=>$totalIcomming-$totalIcommingAns,
            "totalcallback"=>0,
            "incommingTawkTime"=>$this->FloorAmount($incommingTawkTime),
            "incommingTawkTimed"=>$incommingTawkTime,
            "commentstatuscount"=>$commentstatuscount,
            "sourcescount"=>$sourcescount,
            "clientstatuscount"=>$clientstatuscount,
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