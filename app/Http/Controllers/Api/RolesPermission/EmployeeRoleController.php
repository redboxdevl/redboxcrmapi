<?php

namespace App\Http\Controllers\Api\RolesPermission;

use App\Http\Requests\RolesPermission\EmployeeRoleRequest;
use App\Http\Controllers\Controller;
use App\Models\RolesPermission\EmployeeRole;
use Illuminate\Http\Request;

class EmployeeRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmployeeRoleRequest $request)
    {   
        $employeeId = $request->post('emp_id');
        $roleId = $request->post('role_id');
        // foreach ($request->post('permission_type') as $key => $valuePer) {
            
        //     $model=new EmployeeRole();

        //     $model->emp_id = $employeeId;
        //     $model->role_id = $roleId;
        //     $model->feature_id = $valuePer['FeatureId'];
        //     $model->permission_type = $valuePer['value'];
        //     $data = $model->save();

        // }
        
       $daxs = [];
        
        
        foreach($request->post('permission_type') as $mykey => $mydata){
            $FeatureId = $mydata['FeatureId'];
            $myarray = $mydata['myarray'];
            foreach($myarray as $newKey => $newData){
               
                $model=new EmployeeRole();
            
                $model->emp_id = $employeeId;
                $model->role_id = $roleId;
                $model->feature_id = $FeatureId;
                $model->permission_type = $newData;
                $data = $model->save();

                // $daxs[]=array(
                //  'FeatureId'=>$FeatureId,
                //  'newData'=>$newData,
                // );
            }
         
        }

        return response()->json([
            'message' => "Data saved successfully!",
            'data' => '',
            'code'=>'200'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RolesPermission\EmployeeRole  $employeeRole
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeRole $employeerole)
    {
        $response = EmployeeRole::where(['emp_id'=>$employeerole->id])->get();

        return response()->json([
            'message' => "Data retrieved successfully!",
            'data' => $response,
            'code'=>'200'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RolesPermission\EmployeeRole  $employeeRole
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeRole $employeeRole)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RolesPermission\EmployeeRole  $employeeRole
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeRole $employeeRole)
    {
        //
    }
}
