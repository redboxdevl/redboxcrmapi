<?php

namespace App\Http\Controllers\Api\RolesPermission;

use App\Http\Controllers\Controller;
use App\Models\RolesPermission\RolePermissions;
use App\Http\Controllers\Api\CommonController;
use App\Models\RolesPermission\CrmModule;
use App\Models\RolesPermission\RoleCategory;
use App\Models\RolesPermission\CrmModuleFeatures;
use Illuminate\Http\Request;

class RolePermissionsModuleController extends Controller
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
        $perPage = $request->perPage ?? "20";
        $page = $request->page ?? "1";

        $agentId = $request->agentId;

            $response = CrmModule::with(['getFeatures' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->orderBy($orderBy, $orderType)->get();
            $count = CrmModule::count();

            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'branch Found',
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

    public function renderList(Object $apiResponse)
    {
        $data = [];
        

        foreach ($apiResponse as $key => $value) {

            $dataFeature = [];

            foreach ($value->getFeatures as $valueFeature) {
                array_push($dataFeature, (object) [
                    "id" => $valueFeature->id,
                    "module_id" => $valueFeature->module_id,
                    "updated_at" => $valueFeature->updated_at,
                    "created_at" => $valueFeature->created_at,
                    "feature_name" => $valueFeature->feature_name,
                    "PermissionsData" => json_decode($valueFeature->PermissionsData,JSON_UNESCAPED_SLASHES),
                    "featureId"=> $value->id,
                ]);
            }
            
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "module_name" => $value->module_name,
                "feature_name" => $dataFeature,
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RolesPermission\RolePermissions  $rolePermissions
     * @return \Illuminate\Http\Response
     */
    public function show(RolePermissions $rolePermissions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RolesPermission\RolePermissions  $rolePermissions
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RolePermissions $rolePermissions)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RolesPermission\RolePermissions  $rolePermissions
     * @return \Illuminate\Http\Response
     */
    public function destroy(RolePermissions $rolePermissions)
    {
        //
    }
}
