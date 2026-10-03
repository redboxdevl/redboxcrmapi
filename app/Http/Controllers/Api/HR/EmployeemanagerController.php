<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\HR\EmployeeRequest;
use App\Models\HR\Employee;
use App\Models\HR\Leavebalance;
use App\Models\HR\Leavetype;
use App\Models\City;
use App\Models\Country;
use App\Models\HR\Branch;
use App\Models\HR\Company;
use App\Models\HR\Department;
use App\Models\HR\Designation;
use App\Models\HR\Workexperience;
use App\Models\HR\Educationqualify;
use App\Models\LeadImportDatabase;

use App\Models\ListAssignLeads;
use App\Models\RolesPermission\EmployeeRole;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;

class EmployeemanagerController extends Controller
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
        $perPage = $request->perPage ?? "10";
        $page = $request->page ?? "1";
        
        $q = $request->q ?? "";
        $teamObj = $request->teamObj ?? "";
        
        $agentId = $request->agentId;


     $response = Employee::with(['getCompany','getDesignation','getDepartment','getBranch','getRole' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['status'=>'active'])->whereIn('designation_id',[41,54,62])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Employee::where(['status'=>'active'])->count();
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'employee Found',
                    'Keywords'=>$q,
                    'data' => $data,
                    'orderBy' => $orderBy,
                    'orderType' => $orderType,
                    'perPage' => $perPage,
                    'pageNo' => $page,
                    'TotalCount'=>$count,
                    'Count'=>$count,
                ]);
            }

            // FAIL
            else {
                return app(CommonController::class)->getEmptyListResponse();
            }
        
    }

    // PREPARE LIST
    public function renderList(Object $apiResponse)
    {
        $data = [];

        foreach ($apiResponse as $key => $value) {
                
                if($value->getRole == null){
                    $roleData="";
                   
                }else{
                    $roleData = EmployeeRole::where(['role_id'=>$value->getRole->role_id])->get();
                   
                }
                
                $AssignLeadsTotal = ListAssignLeads::join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsassigndatabase.shiftStatus'=>'0','leadsassigndatabase.agentid'=>$value->id])->select('leadsassigndatabase.leadid','leadsimportdatabase.id','leadsassigndatabase.agentid')->count();
                
                $AssignLeadsToday = ListAssignLeads::join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsassigndatabase.shiftStatus'=>'0','leadsassigndatabase.agentid'=>$value->id])->whereDate('leadsassigndatabase.publishAgentDate', date('Y-m-d'))->select('leadsassigndatabase.leadid','leadsimportdatabase.id','leadsassigndatabase.agentid')->count();
                
                 $AssignLeadsAll = ListAssignLeads::join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsassigndatabase.agentid'=>$value->id])->select('leadsassigndatabase.leadid','leadsimportdatabase.id','leadsassigndatabase.agentid')->count();
                 
                 
                 $Workexperience = Workexperience::where(['empId'=>$value->id])->get();
                 
                 $Educationqualify = Educationqualify::where(['empId'=>$value->id])->get();
                 
                 $dataWork=[];
                 
                 foreach($Workexperience as $workDx){
                     
                     array_push($dataWork, (object) [
                         'WorkId'=>$workDx->id,
                         'Company'=>$workDx->WorkCompany,
                         'Designation'=>$workDx->Workdesignation,
                         'Salary'=>$workDx->Worksalary,
                         'Address'=>$workDx->Workaddress,
                         "tenureFrom"=>$workDx->tenureFrom,
                         "tenureTo"=>$workDx->tenureTo,
                     ]);
                     
                 }
                 
                  $dataeduc=[];
                 
                 foreach($Educationqualify as $educDx){
                     
                     array_push($dataeduc, (object) [
                         'EducationId'=>$educDx->id,
                         'school_Univ'=>$educDx->schoolUniv,
                         'Qualification'=>$educDx->schoolQualify,
                         'Level'=>$educDx->schoolLevel,
                         'YearOfPassing'=>$educDx->SchoolPassingYear,
                     ]);
                     
                 }
                
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "AssignLeadsAll" => $AssignLeadsAll,
                "AssignLeadsTotal" => $AssignLeadsTotal,
                "AssignLeadsToday" => $AssignLeadsToday,
                "emp_title"=>$value->emp_title,
                "fatherName"=>$value->fatherName,
                "cnic"=>$value->cnic,
                "ReportTo"=>$value->ReportTo,
                "employment_type"=>$value->employment_type,
                "emp_code" => $value->emp_code,
                "emp_name" => $value->emp_name,
                "email" => $value->email,
                "password" => $value->password,
                "dob" => $value->dob,
                "marital_status" => $value->marital_status,
                "gender" => $value->gender,
                "joining_date" => $value->joining_date,
                "phone_number" => $value->phone_number,
                "mobile_number" => $value->mobile_number,
                "designation_id" => $value->designation_id,
                "designation_name" => $value->getDesignation == '' ? '' : $value->getDesignation->designation_name,
                "company_id" => $value->company_id,
                "company_name" => $value->getCompany == '' ? '' : $value->getCompany->company_name,
                "branch_id" => $value->branch_id,
                "branch_name" => $value->getBranch == '' ? '' : $value->getBranch->branch_name,
                "department_id" => $value->department_id,
                "department_name" => $value->getDepartment == '' ? '' : $value->getDepartment->department_name,
                "status" => $value->status,
                "device_id" => $value->device_id,
                "profile_pic" => $value->profile_pic,
                "utilityBill" => $value->utilityBill,
                "CnicFront" => $value->CnicFront,
                "CnicBack" => $value->CnicBack,
                "resumeUpload" => $value->resumeUpload,
                "fuel" => $value->fuel,
                "mobilecharge" => $value->mobilecharge,
                'dataWork'=>json_encode($dataWork),
                'dataeduc'=>json_encode($dataeduc),
                "date_of_confirmation"=>$value->date_of_confirmation,
                "probation_period"=>$value->probation_period,
                "current_address"=>$value->current_address,
                "permanent_address"=>$value->permanent_address,
                "current_address_type"=>$value->current_address_type,
                "permanent_address_type"=>$value->permanent_address_type,
                "emergency_contact_no"=>$value->emergency_contact_no,
                "ref_contact_name"=>$value->ref_contact_name,
                "emergency_phone_no"=>$value->emergency_phone_no,
                "holidy_id"=>$value->holidy_id,
                "default_shift"=>$value->default_shift,
                "personal_email"=>$value->personal_email,
                "salary_mode"=>$value->salary_mode,
                "bank_name"=>$value->bank_name,
                "bank_account"=>$value->bank_account,
                "blood_group"=>$value->blood_group,
                "family_background"=>$value->family_background,
                "health_details"=>$value->health_details,
                "education_qualify"=>$value->education_qualify,
                "date_of_probation_period"=>$value->date_of_probation_period,
                "salary_amount"=>$value->salary_amount,
                "relationship"=>$value->relationship,
                "ref_phone_no"=>$value->ref_phone_no,
               
                // "empRole"=>$value->getRole == null ? '' : $roleData,
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
    public function employeeleaddistribution(Request $request)
    {
        
        $date = date("Y-m-d H:i:s");
        $rules=array(
            'agentid'=>'required',
            'leadid'=>'required',
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
                'leadAssignId' => $request->agentid,
                ]);
                
            }

            return response()->json([
                'message' => "Leads Import successfully!",
                'code' => '200',
            ], 200);
        }
        
        // return response()->json([
        //     'message' => "Data saved successfully!",
        //     'data' => $request->all(),
        //     'code'=>'200'
        // ], 200);
        
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        // $response = Employee::findOrFail($employee->id);
        
        $response = Employee::with(['getCompany','getDesignation','getDepartment','getBranch' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['id'=>$employee->id])->get();
            
        if($response){
            
            $data = $this->renderList($response);
            return response()->json([
                'message' => "Data retrieved successfully!",
                'data' => $data,
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
    public function update(EmployeeRequest $request, Employee $employee)
    {

        $checkPic = Employee::findOrFail($employee->id);
        
        //  //$payLoad = json_decode(request()->all());
        
        if($request->hasFile("profile_pic")){
            $image = $request->file("profile_pic");
            $ext = $image->extension();
            $image_name=time().'.'.$ext;
            $image->storeAs('/public/profilePic/',$image_name);
            $image_name = "public/storage/profilePic/".$image_name;
        }else{
            $image_name=$checkPic->profile_pic;
        }
        
        if($request->hasFile("utilityBill")){
            
            $image2 = $request->file("utilityBill");
            $ext = $image2->extension();
            $image_name_utility=time().'utility.'.$ext;
            $image2->storeAs('/public/profilePic/',$image_name_utility);
            $image_name_utility = "public/storage/profilePic/".$image_name_utility;
            
        }else{
            $image_name_utility=$checkPic->utilityBill;
        }
        
        if($request->hasFile("CnicFront")){
            $image3 = $request->file("CnicFront");
            $ext = $image3->extension();
            $image_name_cnicfront=time().'cnicfront.'.$ext;
            $image3->storeAs('/public/profilePic/',$image_name_cnicfront);
            $image_name_cnicfront = "public/storage/profilePic/".$image_name_cnicfront;
        }else{
            $image_name_cnicfront=$checkPic->CnicFront;
        }
        
         if($request->hasFile("CnicBack")){
            $image4 = $request->file("CnicBack");
            $ext = $image4->extension();
            $image_name_cnicback=time().'cnicback.'.$ext;
            $image4->storeAs('/public/profilePic/',$image_name_cnicback);
            $image_name_cnicback = "public/storage/profilePic/".$image_name_cnicback;
        }else{
            $image_name_cnicback=$checkPic->CnicBack;
        }
        
        if($request->hasFile("resumeUpload")){
            $image5 = $request->file("resumeUpload");
            $ext = $image5->extension();
            $image_name_resume=time().'resumeUpload.'.$ext;
            $image5->storeAs('/public/profilePic/',$image_name_resume);
            $image_name_resume = "public/storage/profilePic/".$image_name_resume;
        }else{
            $image_name_resume=$checkPic->resumeUpload;
        }
        
        $employee->update([
            
            'emp_title' =>  $request->agent_title,
            'emp_name' => $request->emp_name,
            'email' => $request->email,
            'fatherName' => $request->fatherName,
            'cnic' => $request->cnic,
            'password' => $request->password == $checkPic->password ? $request->password : Hash::make($request->password),
            'dob' => $request->dob,
            'marital_status' => $request->marital_status,
            'gender' => $request->gender,
            'joining_date' => $request->joining_date,
            'company_id' => $request->company_id,
            'phone_number' => $request->phone_number,
            'mobile_number' => $request->mobile_number,
            'department_id' => $request->department_id,
            'designation_id' => $request->designation_id,
            'branch_id' => $request->branch_id,
            'device_id' => $request->device_id,
            'status' => $request->status,
            'ReportTo' => $request->ReportTo == 'null' ? '0' : $request->ReportTo,
            'employment_type' => $request->employment_type == 'null' ? '0' : $request->employment_type,
            'date_of_confirmation' => $request->date_of_confirmation,
            'probation_period' => $request->probation_period,
            'date_of_probation_period' => $request->date_of_probation_period,
            'personal_email' => $request->personal_email,
            'current_address' => $request->current_address,
            'permanent_address' => $request->permanent_address,
            'current_address_type' => $request->current_address_type,
            'permanent_address_type' => $request->permanent_address_type,
            'emergency_contact_no' => $request->emergency_contact_no,
            'emergency_phone_no' => $request->emergency_phone_no,
            'relationship' => $request->relationship,
            'ref_contact_name' => $request->ref_contact_name,
            'ref_phone_no' => $request->ref_phone_no,
            'holidy_id' => $request->holidy_id,
            'default_shift' => $request->default_shift,
            'salary_amount' => $request->salary_amount,
            'currency' => $request->currency,
            'salary_mode' => $request->salary_mode,
            'bank_name' => $request->bank_name,
            'bank_account' => $request->bank_account,
            'blood_group' => $request->blood_group,
            'family_background' => $request->family_background,
            'health_details' => $request->health_details,
            'fuel'=>$request->fuel,
            'mobilecharge'=>$request->mobilecharge,
            'profile_pic' => $image_name,
            'utilityBill' => $image_name_utility,
            'CnicFront' => $image_name_cnicfront,
            'CnicBack' => $image_name_cnicback,
            'resumeUpload' => $image_name_resume,
        ]);
        
        $workExperienceJson = json_decode($request->work_experience,JSON_UNESCAPED_SLASHES);
       
        foreach($workExperienceJson as $itemWork){
            
            $WorkId = $itemWork['WorkId'];
            $Company = $itemWork['Company'];
            $Address = $itemWork['Address'];
            $Designation = $itemWork['Designation'];
            $Salary = $itemWork['Salary'];
            $tenureFrom = $itemWork['tenureFrom'];
            $tenureTo = $itemWork['tenureTo'];
            
            if($WorkId == '' || $WorkId == null){
                
                $workExperienceData = Workexperience::create([
                    'empId'=>$employee->id,
                    'WorkCompany' =>  $Company,
                    'Workaddress' =>  $Address,
                    'Workdesignation' =>  $Designation,
                    'Worksalary' =>  $Salary,
                    'tenureFrom'=>$tenureFrom,
                    'tenureTo'=>$tenureTo,
                ]);
                
            }else{
                
                Workexperience::where("id",$WorkId)->update([
                    'empId'=>$employee->id,
                    'WorkCompany' =>  $Company,
                    'Workaddress' =>  $Address,
                    'Workdesignation' =>  $Designation,
                    'Worksalary' =>  $Salary,
                    'tenureFrom'=>$tenureFrom,
                    'tenureTo'=>$tenureTo,
                ]);
                
            }
            
        }
        
        
        $educationQualifyJson = json_decode($request->education_qualify,JSON_UNESCAPED_SLASHES);
       
        foreach($educationQualifyJson as $itemEdu){
            
            $EducationId = $itemEdu['EducationId'];
            $school_Univ = $itemEdu['school_Univ'];
            $Qualification = $itemEdu['Qualification'];
            $Level = $itemEdu['Level'];
            $YearOfPassing = $itemEdu['YearOfPassing'];
            
            if($EducationId == '' || $EducationId == null){
                
                $EducationData = Educationqualify::create([
                    'empId'=>$employee->id,
                    'schoolUniv' =>  $school_Univ,
                    'schoolQualify' =>  $Qualification,
                    'schoolLevel' =>  $Level,
                    'SchoolPassingYear' =>  $YearOfPassing,
                ]);
                
            }else{
                
                Educationqualify::where("id",$EducationId)->update([
                    'empId'=>$employee->id,
                    'schoolUniv' =>  $school_Univ,
                    'schoolQualify' =>  $Qualification,
                    'schoolLevel' =>  $Level,
                    'SchoolPassingYear' =>  $YearOfPassing,
                ]);
                
            }
            
        }

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $employee,
            'code'=>'200'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeRequest $request, Employee $employee)
    {
        $employee->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code'=>'200'
        ], 200);
    }
    
    
    public function ForgotPassword(Request $request)
    {
        
        $uniqueId = Str::random(10);
        $rules=array(
            'email'=>'required',
        );
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return $validator->errors();
        }else{

            $checkEmp = Employee::where(['email'=>$request->post('email')])->get();
            if(count($checkEmp) > 0){
                
                

                $data=['data'=>$checkEmp,'code'=>$uniqueId];

                $user['to']=$request->post('email');
    
                Mail::send('ForgotPassword',$data,function($messages) use($user){
    
                    $messages->to($user['to']);
    
                    $messages->from('test@redbox.estate');
    
                    $messages->subject('Forgot Password Request');
    
                });

                

                Employee::where('id', $checkEmp[0]->id)
                ->update([
                'resetCode' => $uniqueId,
                ]);
                
                return response()->json([
                    'message' => "Email with reset link sent successfully",
                    'data' => $checkEmp[0]->id,
                    'code'=>'200'
                ], 200);

            }else{
                
                return response()->json([
                    'message' => "Data Not Found",
                    'code'=>'200'
                ], 200);

            }


        }

       
    }


    public function ResetPassword(Request $request)
    {
       
        $rules=array(
            'resetCode'=>'required',
            'newpassword'=>'required',
        );

    
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return $validator->errors();
        }else{

            $checkEmp = Employee::where(['resetCode'=>$request->resetCode])->get();

            if(count($checkEmp) > 0){

                Employee::where('id', $checkEmp[0]->id)
                ->update([
                'password' => Hash::make($request->newpassword),
                ]);
                
                return response()->json([
                    'message' => "Password Successfully Changed",
                    'code'=>'200'
                ], 200);


            }else{

                return response()->json([
                    'message' => "Password Not Changed",
                    'code'=>'203'
                ], 203);

            }


        }

       
    }
    
    public function employeeStatus(Request $request){
        
         $rules=array(
            'emp_id'=>'required',
            'status'=>'required',
        );

    
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return $validator->errors();
        }else{
            
            $status = $request->post('status');
            
            if($status == 'active'){
                
                Employee::where('id', $request->post('emp_id'))
                ->update([
                'status' => 'inactive',
                ]);
                
            }else{
                
                Employee::where('id', $request->post('emp_id'))
                ->update([
                'status' => 'active',
                ]);
                
            }
            
            return response()->json([
                'message' => "Employee Status Updated",
                'code'=>'200'
            ], 200);
            
        }
        
    }
    
}
