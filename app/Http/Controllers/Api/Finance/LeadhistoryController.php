<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\BanklistRequest;
use App\Models\Finance\Banklist;
use App\Models\DocumentType;
use App\Models\Issuingauthority;
use App\Models\propertyType;
use App\Models\LeadImportDatabase;
use App\Models\LeadImpression;
use App\Models\HR\Employee;
use App\Models\ListAssignLeads;
use App\Models\ListMyLead;
use App\Models\Inventory\Inventorysold;
use App\Models\ListNotInterested;
use App\Models\Zongcalling;
use App\Models\CustomerActivity;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;
use DB;

class LeadhistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $LeadId = $request->leadid ?? "";
    
        if (empty($LeadId)) {
            
           return app(CommonController::class)->getExceptionResponse("Lead Id is required");
          
        }   else {
            
            $PhoneCount = LeadImpression::where(['impType'=>'phone','leadid'=>$LeadId])->get()->unique('leadid')->count();
            $whatsappCount = LeadImpression::where(['impType'=>'whatsapp','leadid'=>$LeadId])->get()->unique('leadid')->count();
            $smsCount = LeadImpression::where(['impType'=>'sms','leadid'=>$LeadId])->get()->unique('leadid')->count();
            $emailCount = LeadImpression::where(['impType'=>'email','leadid'=>$LeadId])->get()->unique('leadid')->count();
            
            $LeadImportDatabase = LeadImportDatabase::where(['id'=>$LeadId])->first();
            
            $ZongCalls = Zongcalling::where('phone','LIKE',"%{$LeadImportDatabase->clientPhone}")->get();
            
            $AssignLeads = ListAssignLeads::where(['leadid'=>$LeadId])->first();
            
            if($AssignLeads->shiftStatus == 0){
                 $LeadStatus = "Assign Leads";
                 $MyLeads='';
                 $activtyLog='';
            }else if($AssignLeads->shiftStatus == 1){
                 $LeadStatus = "My Leads";
                 
                 $MyLeads = ListMyLead::where(['leadid'=>$LeadId])->first();
                 $activtyLog = CustomerActivity::with(['CustomerData','customer_activity_type','customer_activity_outcome' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['customerId'=>$MyLeads->id])->get();
                 
            }else if($AssignLeads->shiftStatus == 2){
                 $LeadStatus = "Not Interested";
                 $MyLeads='';
                 $activtyLog='';
            }else{
                $LeadStatus = "";
                $MyLeads='';
                $activtyLog='';
            }
            
            
            $Employee = Employee::where(['id'=>$AssignLeads->agentid])->first();

            return response()->json([
                'code' => '200',
                'success' => true,
                'message' => 'Lead History List Found',
                'PhoneCount' => $PhoneCount,
                'whatsappCount' => $whatsappCount,
                'smsCount' => $smsCount,
                'emailCount' => $emailCount,
                'clientPhone'=>$LeadImportDatabase == '' || $LeadImportDatabase == null ? '' : $LeadImportDatabase->clientPhone,
                'clientName'=>$LeadImportDatabase == '' || $LeadImportDatabase == null ? '' : $LeadImportDatabase->clientName,
                'callHistory'=>$ZongCalls,
                'AssignLeads'=>$AssignLeads,
                'leadStatus'=>$LeadStatus,
                'MyLeads'=>$MyLeads,
                'activtyLog'=>$activtyLog,
                'Employee'=>$Employee,
                "emp_name"=>$Employee == '' || $Employee == null ? '' : $Employee->emp_name,
                'created_at'=>$LeadImportDatabase == '' || $LeadImportDatabase == null ? '' : date("Y-m-d",strtotime($LeadImportDatabase->created_at)),
            ]);
        }
        
    }

    // PREPARE LIST
    public function renderList(Object $apiResponse , $StartDates ,$EndDate )
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