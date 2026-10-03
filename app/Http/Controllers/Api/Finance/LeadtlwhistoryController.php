<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Models\ListMyLead;
use App\Models\LeadImpression;
use App\Models\CustomerActivity;
use App\Models\Zongcalling;
use App\Models\HR\Employee;
use App\Models\LeadImportDatabase;
use App\Models\ListAssignLeads;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;
use DB;

class LeadtlwhistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $agentId = $request->agentId;
        $orderBy = $request->orderBy ?? "id";
        $orderType = $request->orderType ?? "desc";
        $perPage = $request->perPage ?? "12";
        $page = $request->page ?? "1";

        if (!empty($agentId)) {
            
            // $response = ListMyLead::with(['ReminderData','getleads','getActivitiesAll','getEmployeeDetails' => function ($query){
            //     $query->orderBy('id', 'desc')->get();
            // } , 'ProjectData'])->whereIn('customer_leads_tbls.LeadStatus',['My Lead','hot'])->where(['customer_leads_tbls.ReasonStatus'=>'No','customer_leads_tbls.agentid'=>$agentId])->orderBy($orderBy,$orderType)->paginate($perPage);
        
            // $count = ListMyLead::with(['ReminderData','getleads','getActivitiesAll','getEmployeeDetails' => function ($query){
            //     $query->orderBy('id', 'desc')->get();
            // } , 'ProjectData'])->whereIn('customer_leads_tbls.LeadStatus',['My Lead','hot'])->where(['customer_leads_tbls.ReasonStatus'=>'No','customer_leads_tbls.agentid'=>$agentId])->orderBy($orderBy,$orderType)->count();
            
            $Employee = Employee::where(['id'=>$agentId])->first();
            
            $LeadImpression = CustomerActivity::with(['CustomerData' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ])->where(['agentid'=>$agentId])->where(DB::raw('date(created_at)'),date("Y-m-d"))->groupBy('customerId')->orderBy($orderBy,$orderType)->paginate($perPage);
            
            $count = CustomerActivity::with(['CustomerData' => function ($query){
                $query->orderBy('id', 'desc')->get();
            }])->where(['agentid'=>$agentId])->where(DB::raw('date(created_at)'),date("Y-m-d"))->get()->unique('customerId')->count();
        
        $data = $this->renderList($LeadImpression,$agentId);
            
            return response()->json([
                'code' => '200',
                'success' => true,
                'message' => 'Lead TLW List Found',
                'data' => $data,
                'orderBy' => $orderBy,
                'orderType' => $orderType,
                'perPage' => $perPage,
                'pageNo' => $page,
                'TotalCount'=>$count,
                'empName'=>$Employee == '' || $Employee == null ? '' : $Employee->emp_name
                // 'LeadImpression'=>$LeadImpression,
            ], 200);
        
        } 
        // AGENT ID NOT SELECTED
        else {
            return app(CommonController::class)->getExceptionResponse("Agent Id is required");
        }
    }

    
    public function renderList(Object $apiResponse,$agentId)
    {
        $data = [];

        foreach ($apiResponse as $key => $value) {
            
          
            
            // $customerId = $value->getCustomerDetails == '' || $value->getCustomerDetails == null ? '' : $value->getCustomerDetails->id;
            
            $CustomerActivity = CustomerActivity::where(['customerId'=>$value->CustomerData == null ? 0 : $value->CustomerData->id])->get();
            
            $Employee = Employee::where(['id'=>$agentId])->first();
            
            $phoneClientFilter = $value->CustomerData == '' || $value->CustomerData == null ? 0 : $value->CustomerData->mobileNo;
            
            $Zongcalling = Zongcalling::whereIn('ext',[$Employee->incommingPhone,$Employee->outgoingPhone])->where('phone', 'LIKE', '%'.$phoneClientFilter.'%')->get();
            
            $LeadImportDatabase  = LeadImportDatabase::where(['id'=>$value->CustomerData->leadid])->first();
            
            $ListAssignLeads = ListAssignLeads::where(['leadid'=>$value->CustomerData->leadid])->first();
            
            if($ListAssignLeads == '' || $ListAssignLeads == null){
                $statusLeads = '';
            }else if($ListAssignLeads->shiftStatus == '1'){
                $statusLeads="My Leads";
            }else if($ListAssignLeads->shiftStatus == '2'){
                $statusLeads="Not Interested Leads";
            }else if($ListAssignLeads->shiftStatus == '0'){
                $statusLeads="Assign Leads";
            }else{
                $statusLeads="";
            }
            
                 array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "CustomerData"=>$value->CustomerData,
                // "ReminderData"=>$value->ReminderData,
                "getActivities"=>$CustomerActivity,
                "clientActivityDesc"=>$value->CustomerData == '' || $value->CustomerData == null ? '' : $value->CustomerData->clientActivityDesc,
                "clientName"=>$value->CustomerData == '' || $value->CustomerData == null ? $LeadImportDatabase == '' || $LeadImportDatabase == null ? '' : $LeadImportDatabase->clientName : $value->CustomerData->clientName,
                "mobileNo"=>$value->CustomerData == '' || $value->CustomerData == null ? $LeadImportDatabase == '' || $LeadImportDatabase == null ? '' : $LeadImportDatabase->clientPhone : $value->CustomerData->mobileNo,
                // "getleads"=>$value->getleads,
                "clientProject"=>$LeadImportDatabase == '' || $LeadImportDatabase == null ? '' : $LeadImportDatabase->clientProject,
                // "leadStatus" => $value->getCustomerDetails == '' || $value->getCustomerDetails == null ? 'Assign' : 'Already Added',
                "leadStatus"=>$ListAssignLeads == '' || $ListAssignLeads == null ? '' : $statusLeads,
                // 'getAssignLeads'=>$value->getAssignLeads,
                "publishAgentDate"=>$ListAssignLeads == '' || $ListAssignLeads == null ? '' : date("Y-m-d",strtotime( $value->CustomerData == '' || $value->CustomerData == null ? $ListAssignLeads->publishAgentDate : $value->CustomerData->created_at)),
                'Zongcalling'=>$Zongcalling,
                // "emp_name"=>$value->getEmployeeDetails == '' || $value->getEmployeeDetails == null ? '' : $value->getEmployeeDetails->emp_name
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
        
       
        
        return response()->json([
            'message' => "Data saved successfully!",
            'data' => '',
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
       return response()->json([
                'message' => "Data Not Found!",
                'code'=>'203'
            ], 203);
        
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

       
        
        return response()->json([
            'message' => "Data updated successfully!",
            'data' => '',
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
       
        return response()->json([
            'message' => "Data deleted successfully!",
            'code'=>'200'
        ], 200);
    }
    
    
    
    
}