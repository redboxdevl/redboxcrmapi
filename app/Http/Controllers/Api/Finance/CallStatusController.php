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

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;
use DB;

class CallStatusController extends Controller
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
        $StartDates = $request->StartDates ?? "";
        $EndDate = $request->EndDate ?? "";
        
       // echo $StartDates."<br>".$EndDate;

        if (is_null($agentId)) {
            
            $response = Employee::where(['status'=>'active','designation_id'=>'1'])->orderBy($orderBy, $orderType)->get();
            $count = Employee::where(['status'=>'active','designation_id'=>'1'])->count();
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response,$StartDates,$EndDate);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Bank List Found',
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
        }   else if ($agentId == 'all') {

            
            $response = Employee::where(['status'=>'active','designation_id'=>'1'])->orderBy($orderBy, $orderType)->get();
                
            $count = Employee::where(['status'=>'active','designation_id'=>'1'])->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

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
    public function renderList(Object $apiResponse , $StartDates ,$EndDate )
    {
        $data = [];

        foreach ($apiResponse as $key => $value) {
            
            if($StartDates == '' || $EndDate == ''){
                $ListAssignLeadsCount = ListAssignLeads::where(['agentid'=>$value->id,'shiftStatus'=>'0'])->select(DB::raw('*'))
                  ->whereRaw('Date(created_at) = CURDATE()')->count();
                  
                  $TotalAssignLeadsCount = ListAssignLeads::where(['agentid'=>$value->id])->select(DB::raw('*'))
                  ->whereRaw('Date(created_at) = CURDATE()')->count();
                  
            }else{
                
                $ListAssignLeadsCount = ListAssignLeads::where(['agentid'=>$value->id,'shiftStatus'=>'0'])->whereBetween(DB::raw('DATE(created_at)'), array($StartDates,$EndDate))->select(DB::raw('*'))
                  ->count();
                  
                  $TotalAssignLeadsCount = ListAssignLeads::where(['agentid'=>$value->id])->whereBetween(DB::raw('DATE(created_at)'), array($StartDates,$EndDate))->select(DB::raw('*'))
                  ->count();
            }
            
            if($StartDates == '' || $EndDate == ''){
                $ListMyLeadCount = ListMyLead::where(['agentid'=>$value->id,'LeadStatus'=>'My Lead','ReasonStatus'=>'No','DormentStatus'=>'No'])->select(DB::raw('*'))
                  ->whereRaw('Date(created_at) = CURDATE()')->count();
            }else{
                $ListMyLeadCount = ListMyLead::where(['agentid'=>$value->id,'LeadStatus'=>'My Lead','ReasonStatus'=>'No','DormentStatus'=>'No'])->whereBetween(DB::raw('DATE(created_at)'), array($StartDates,$EndDate))->select(DB::raw('*'))->count();
            }
                  
            if($StartDates == '' || $EndDate == ''){
                $ListHotLeadCount = ListMyLead::where(['agentid'=>$value->id,'LeadStatus'=>'hot','ReasonStatus'=>'No','DormentStatus'=>'No'])->select(DB::raw('*'))
                  ->whereRaw('Date(created_at) = CURDATE()')->count();
            }else{
                $ListHotLeadCount = ListMyLead::where(['agentid'=>$value->id,'LeadStatus'=>'hot','ReasonStatus'=>'No','DormentStatus'=>'No'])->whereBetween(DB::raw('DATE(created_at)'), array($StartDates,$EndDate))->select(DB::raw('*'))->count();
            }
                  
             if($StartDates == '' || $EndDate == ''){
                 $ListNotInterestedLeadCount = ListNotInterested::where(['agentid'=>$value->id])->select(DB::raw('*'))
                  ->whereRaw('Date(created_at) = CURDATE()')->count();
             }else{
                 $ListNotInterestedLeadCount = ListNotInterested::where(['agentid'=>$value->id])->whereBetween(DB::raw('DATE(created_at)'), array($StartDates,$EndDate))->select(DB::raw('*'))->count();
             }
                  
             if($StartDates == '' || $EndDate == ''){
                 $untabbedLeadsCount=ListAssignLeads::whereDoesntHave('LeadImpression')->where(['agentid'=>$value->id,'shiftStatus'=>'0'])->select(DB::raw('*'))
                  ->whereRaw('Date(created_at) = CURDATE()')->count();
             }else{
                 $untabbedLeadsCount=ListAssignLeads::whereDoesntHave('LeadImpression')->where(['agentid'=>$value->id,'shiftStatus'=>'0'])->whereBetween(DB::raw('DATE(created_at)'), array($StartDates,$EndDate))->select(DB::raw('*'))->count();
             }
                  
            if($StartDates == '' || $EndDate == ''){
                $InventorysoldCount = Inventorysold::where(['employee_id'=>$value->id])->select(DB::raw('*'))
                  ->whereRaw('Date(created_at) = CURDATE()')->count();
            }else{
                $InventorysoldCount = Inventorysold::where(['employee_id'=>$value->id])->whereBetween(DB::raw('DATE(created_at)'), array($StartDates,$EndDate))->select(DB::raw('*'))->count();
            }
                  
            
                  
            
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "emp_name" => $value->emp_name,
                "AssignLeadCount"=>$ListAssignLeadsCount,
                "MyLeadsCount"=>$ListMyLeadCount,
                "HoLeadsCount"=>$ListHotLeadCount,
                "NotInterestedCount"=>$ListNotInterestedLeadCount,
                "UntabbedLeadsCount"=>$untabbedLeadsCount,
                "InventorysoldCount"=>$InventorysoldCount,
                "StartDates"=>$StartDates,
                "EndDate"=>$EndDate,
                "TotalAssignLeadsCount"=>$TotalAssignLeadsCount,
            ]);
        }

        return $data;
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