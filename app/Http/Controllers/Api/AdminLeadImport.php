<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Imports\LeadImports;
use App\Imports\AttendanceImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Models\LeadImportDatabase;
use App\Models\HR\Attendance;
use App\Models\HR\Employee;
use App\Models\ListAssignLeads;

use Validator;

class AdminLeadImport extends Controller
{
    public function index(Request $request){
       
        $orderBy = $request->orderBy ?? "id";
        $orderType = $request->orderType ?? "desc";
        $perPage = $request->perPage ?? "10";
        $page = $request->page ?? "1";
        $ProjectFilter = $request->ProjectFilter ?? "";
        $agentId = $request->agentId ?? "";
        $company_id = $request->company_id ?? "";
        $StatusFilter = $request->StatusFilter ?? "";
        
        if(!empty($ProjectFilter) && empty($agentId) && empty($company_id) && empty($StatusFilter)){
            $response = LeadImportDatabase::where(['leadstatus'=>'inactive','clientProject'=>$ProjectFilter,'leadAssignId'=>0,'companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $responseTotalPages = LeadImportDatabase::where(['leadstatus'=>'inactive','clientProject'=>$ProjectFilter,'leadAssignId'=>0,'companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage)->total();
            
            $count = LeadImportDatabase::where(['leadstatus'=>'inactive','clientProject'=>$ProjectFilter,'leadAssignId'=>0,'companyId'=>$company_id])->count();
        }else if(empty($ProjectFilter) && !empty($agentId) && !empty($company_id) && empty($StatusFilter)){
            
            $response = LeadImportDatabase::where(['leadstatus'=>'inactive','leadAssignId'=>$agentId,'companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $responseTotalPages = LeadImportDatabase::where(['leadstatus'=>'inactive','leadAssignId'=>$agentId,'companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage)->total();
            
            $count = LeadImportDatabase::where(['leadstatus'=>'inactive','leadAssignId'=>$agentId,'companyId'=>$company_id])->count();
            
        }else if(empty($ProjectFilter) && empty($agentId) && !empty($company_id) && !empty($StatusFilter)){
            
            $response = LeadImportDatabase::where(['leadstatus'=>'inactive','companyId'=>$company_id,'leadType'=>$StatusFilter])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $responseTotalPages = LeadImportDatabase::where(['leadstatus'=>'inactive','companyId'=>$company_id,'leadType'=>$StatusFilter])->orderBy($orderBy, $orderType)->paginate($perPage)->total();
            
            $count = LeadImportDatabase::where(['leadstatus'=>'inactive','companyId'=>$company_id,'leadType'=>$StatusFilter])->count();
            
        }else{
            $response = LeadImportDatabase::where(['leadstatus'=>'inactive','leadAssignId'=>0,'companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $responseTotalPages = LeadImportDatabase::where(['leadstatus'=>'inactive','leadAssignId'=>0,'companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage)->total();
            
            $count = LeadImportDatabase::where(['leadstatus'=>'inactive','leadAssignId'=>0,'companyId'=>$company_id])->count();
        }


        // SUCCESS
        if (count($response) > 0) {
            
            $TOtalPerPage = ceil($responseTotalPages/$perPage);
            
            $data = $this->renderList($response);
        
            return response()->json([
                'code' => '200',
                'success' => true,
                'message' => 'Leads Found',
                'data' => $data,
                'orderBy' => $orderBy,
                'orderType' => $orderType,
                'perPage' => $perPage,
                'pageNo' => $page,
                'TotalCount'=>$count,
                'TotalPerPage'=>$TOtalPerPage,
            ]);
        }
        // FAIL
        else {
            return app(CommonController::class)->getEmptyListResponse();
        }

    }

    public function renderList(Object $apiResponse)
    {
        $data = [];

        foreach ($apiResponse as $key => $value) {
            
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "clientName" => $value->clientName,
                "clientPhone" => $value->clientPhone,
                "clientemail" => $value->clientemail,
                "clientProject" => $value->clientProject,
                "clientDesignation" => $value->clientDesignation,
                "nature" => $value->nature,
                "leadStatus" => $value->leadStatus,
                "leadType" => $value->leadType,
                
            ]);
        }

        return $data;
    }
    public function leadSaveImport(Request $request){
       
        $rules=array(
            'uploadFile'=>'required',
            'nature'=>'required',
        );

        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return $validator->errors();
        }else{
            Excel::import(new LeadImports,request()->file('uploadFile'));
            return response()->json([
                'message' => "Leads Import successfully!",
                'code' => '200',
            ], 200);
        }
        
    }

    public function assignconsultantleadsData(Request $request){

        
        $date = date("Y-m-d H:i:s");
        $rules=array(
            'agentid'=>'required',
            'leadid'=>'required',
            // 'companyId'=>'required',
        );

        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return $validator->errors();
        }else{
            
            $leadid = json_decode($request->leadid,JSON_UNESCAPED_SLASHES);
            
            $datax = [];

            // $leadid = explode(",",$request->post('leadid'));
            foreach ($leadid as $value) {
                
                $lieadId = $value['id'];

                LeadImportDatabase::where(['id'=>$lieadId,'companyId'=>$request->post('companyId')])
                ->update([
                'leadStatus' => 'active',
                ]);

                $ListAssignLeads = new ListAssignLeads();
                $ListAssignLeads->agentid = $request->post('agentid');
                $ListAssignLeads->leadid = $lieadId;
                $ListAssignLeads->shiftStatus = '0';
                $ListAssignLeads->publishAgentDate = $date;
                $ListAssignLeads->companyId = $request->post('companyId');

                $ListAssignLeads->save();
                
            }

            return response()->json([
                'message' => "Leads Import successfully!",
                'code' => '200',
            ], 200);
        }

      
        
    }
    
    
    public function AttendanceSaveImport(Request $request){
        
        ini_set('memory_limit','-1');
        ini_set('max_execution_time', '-1000'); //300 seconds = 5 minutes
        
        $rules=array(
            'uploadFile'=>'required',
        );

        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return $validator->errors();
        }else{
            $data = Excel::toArray(new AttendanceImport,request()->file('uploadFile'));
            
            $newArr = [];
            
            foreach($data[0] as $AttendanceData){
                
                 $device_id = $AttendanceData['deviceid'];
                $Employeexs = Employee::where(['device_id'=>$device_id])->get();
                foreach($Employeexs as $empId){
                    
                $empid = $empId->id;
               
                $location_lat = '0';
                $location_long = '0';
                $checkout_location_lat = '0';
                $checkout_location_long = '0';
                $is_mobile = '0';
                $attendance_date = date("Y-m-d",strtotime($AttendanceData['date']));
                $checkin = $AttendanceData['checkin'];
                $checkout = $AttendanceData['checkout'];
                $checkintype = 'In';
                $checkouttype = 'Out';
                
                $attendanceImport = new Attendance();
                $attendanceImport->emp_id = $empid;
                $attendanceImport->machine_id = $device_id;
                $attendanceImport->location_lat = $location_lat;
                $attendanceImport->location_long = $location_long;
                $attendanceImport->checkout_location_lat = $checkout_location_lat;
                $attendanceImport->checkout_location_long = $checkout_location_long;
                $attendanceImport->is_mobile = $is_mobile;
                $attendanceImport->attendance_date = $attendance_date;
                $attendanceImport->checkin = $checkin;
                $attendanceImport->checkout = $checkout;
                $attendanceImport->checkintype = $checkintype;
                $attendanceImport->checkouttype = $checkouttype;
                
                $attendanceImport->save();
                
                }
                
                
                // $newArr[]=array(
                //  'deviceid' => $AttendanceData['deviceid'],
                //  'date' => date("Y-m-d",strtotime($AttendanceData['date'])),
                //  'checkin' => $AttendanceData['checkin'],
                //  'checkout' => $AttendanceData['checkout'],
                //  "Employee"=>$Employeexs->id,
                
                // );
            }
            
            return response()->json([
                'message' => "Attendance Import successfully!",
                'code' => '200',
                'data'=>'',
            ], 200);

        }
        
    }

    
}
