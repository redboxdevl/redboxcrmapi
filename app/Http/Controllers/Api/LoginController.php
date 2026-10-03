<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\LoginRequest;
use App\Models\HR\Employee;
use App\Models\HR\empTeam;
use App\Models\RolesPermission\EmployeeRole;
use App\Models\RolesPermission\RoleCategory;
use App\Models\RolesPermission\CrmModuleFeatures;
use App\Models\RolesPermission\CrmModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        ini_set('memory_limit','-1');
        
        $orderBy = $request->orderBy ?? "id";
        $orderType = $request->orderType ?? "desc";
        $perPage = $request->perPage ?? "12";
        $page = $request->page ?? "1";

        $agentId = $request->agentId;

        if (!is_null($agentId)) {

            $response = Employee::orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Employee::count();

            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Employee Found',
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


    public function renderList(Object $apiResponse)
    {
        $data = [];

        foreach ($apiResponse as $key => $value) {

            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
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
    public function store(LoginRequest $request)
    {
        
        ini_set('memory_limit','-1');
        
        $dataFinal = [];
        
        $data = Employee::with(['getCompany' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['email'=>$request->post('email'),'status'=>'active'])->first();
        
        if($data){
        
            $pass = Hash::check($request->post('password'), $data->password);
           
            if($pass){
                
                $EmpRoleData = EmployeeRole::with(['getRole','getFeature' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['emp_id'=>$data->id])->get();
            
            $dataitem=[];
            $datamoduleitem=[];
            $SUperAdmin = "";
            
            
            foreach($EmpRoleData as $moduleItem){
                
                $roleName = $moduleItem->getRole == null ? '' : $moduleItem->getRole->role_category_name;
                
                $moduleQuery = CrmModule::where(['id'=>$moduleItem->getFeature->module_id])->first();
                
                $moduleName = $moduleItem->getFeature == null ? '' : $moduleQuery->module_name;
                
                $moduleId = $moduleItem->getFeature == null ? '' : $moduleQuery->id;
                
                $roleId = $moduleItem->getRole == null ? '' : $moduleItem->getRole->id;
                
                if($roleId == 3){
                    $SUperAdmin="all";
                }else{
                    $SUperAdmin="";
                }
                
                $featureName = $moduleItem->getFeature == null ? '' : $moduleItem->getFeature->feature_name;
                $featureId = $moduleItem->getFeature == null ? '' : $moduleItem->getFeature->id;
                
                 array_push($dataitem, (object) [
                  'role_name'=>$roleName,
                  'module_name'=>$moduleName,
                  'module_id'=>$moduleId,
                  'role_id'=>$roleId,
                  'features'=>[
                     'feature_name' =>$featureName,
                     'feature_id' =>$featureId,
                   ],
                   'sub_features'=>$moduleItem->permission_type
                ]);
            }
            
            
            
            array_push($dataFinal, (object) [
                "role"=>$dataitem,
            ]);
            
            if(!empty($request->post('Token'))){
                $Token = $request->post('Token');
            
                $modelEmp = Employee::where('id','=',$data->id)->first();
                $modelEmp->NotificationToken = $Token;
                $modelEmp->update();
            }
            
                
            $empTeam = empTeam::where(['agentId'=>$data->id])->first();
            
            return response()->json([
                'message' => "Login successfully!",
                'data' => $data,
                'superAdmin'=>$SUperAdmin,
                'rolepermission'=>$dataitem,
                'empTeam'=>$empTeam,
                'code'=>'200'
            ], 200);
            }else{
                return response()->json([
                    'message' => "Login Failed!",
                    'code'=>'203'
                ], 203);
            }
        }else{
            return response()->json([
                'message' => "Email Not Valid!",
                'code'=>'203'
            ], 203);
        }
    
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function show(Branch $branch)
    {
        // $response = Branch::findOrFail($branch->id);

        // return response()->json([
        //     'message' => "Data retrieved successfully!",
        //     'data' => $response,
        //     'code'=>'200'
        // ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function update(BranchRequest $request, Branch $branch)
    {
        // $branch->update($request->all());

        // return response()->json([
        //     'message' => "Data updated successfully!",
        //     'data' => $branch,
        //     'code'=>'200'
        // ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function destroy(BranchRequest $request, Branch $branch)
    {
        // $branch->delete($request->all());

        // return response()->json([
        //     'message' => "Data deleted successfully!",
        //     'code'=>'200'
        // ], 200);
    }
}
