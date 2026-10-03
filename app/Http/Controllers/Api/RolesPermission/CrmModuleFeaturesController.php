<?php

namespace App\Http\Controllers\Api\RolesPermission;

use App\Http\Controllers\Controller;
use App\Http\Requests\RolesPermission\CrmModuleFeaturesRequest;
use App\Models\RolesPermission\CrmModuleFeatures;
use App\Http\Controllers\Api\CommonController;
use Illuminate\Http\Request;

class CrmModuleFeaturesController extends Controller
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
        $moduleId = $request->moduleId;

        if (!is_null($agentId)) {

            $response = CrmModuleFeatures::orderBy($orderBy, $orderType)->paginate($perPage);
            $count = CrmModuleFeatures::count();

            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Feature Found',
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
        }else if(is_null($agentId) && !is_null($moduleId)){
            
            $response = CrmModuleFeatures::where(['module_id'=>$moduleId])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = CrmModuleFeatures::where(['module_id'=>$moduleId])->count();

            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderListFeature($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Feature Found',
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
                "deleted_at" => $value->deleted_at,
                "module_id" => $value->module_id,
                "feature_name" => $value->feature_name,
            ]);
        }

        return $data;
    }
    
        public function renderListFeature(Object $apiResponse)
        {
        $data = [];
        
        foreach ($apiResponse as $key => $value) {
        
            array_push($data, (object) [
                "featureId" => $value->id,
                "module_id" => $value->module_id,
                "feature_name" => $value->feature_name,
            ]);
        }
        
        return json_encode($data);
        }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CrmModuleFeaturesRequest $request)
    {
        // // $response = CrmModuleFeatures::create($request->all());
        // $exp = explode(",",$request->post('feature_name'));
            
        $permissionData = '[{"value":"List","label":"List","selected":true},{"value":"View","label":"View","selected":true},{"value":"Create","label":"Create","selected":true},{"value":"Import","label":"Import","selected":true},{"value":"Export","label":"Export","selected":true},{"value":"Report","label":"Report","selected":true}]';

        // foreach ($exp as $key => $value) {
            
        //     $cmsFeature = new CrmModuleFeatures();
        //     $cmsFeature->module_id = $request->post('module_id');
        //     $cmsFeature->feature_name = $value;
        //     $cmsFeature->PermissionsData = $permissionData;
        //     $data = $cmsFeature->save();

        // }
        
        $FeatureMethodJson = json_decode($request->feature_name,JSON_UNESCAPED_SLASHES);
        
        foreach($FeatureMethodJson as $myfeatures){
            
            $featureId = $myfeatures['featureId'];
            $feature_name = $myfeatures['feature_name'];
            $module_id = $myfeatures['module_id'];
            
            if($featureId == '' || $featureId == null){
                
                $featureInsert = CrmModuleFeatures::create([
                    'module_id'=>$module_id,
                    'feature_name' =>  $feature_name,
                    'PermissionsData' => $permissionData,
                ]);
                
            }else{
                
                CrmModuleFeatures::where("id",$featureId)->update([
                    'module_id' =>  $module_id,
                    'feature_name' => $feature_name,
                    'PermissionsData' => $permissionData,
                ]);
                
            }
           
            
                     
        }
        

        return response()->json([
            'message' => "Data saved successfully!",
            // 'data'=>$FeatureMethodJson,
            'code'=>'200'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RolesPermission\\CrmModuleFeatures  $crmModuleFeatures
     * @return \Illuminate\Http\Response
     */
    public function show(CrmModuleFeatures $crmModuleFeatures)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RolesPermission\\CrmModuleFeatures  $crmModuleFeatures
     * @return \Illuminate\Http\Response
     */
    public function update(CrmModuleFeaturesRequest $request)
    {
        $exp = explode(",",$request->post('feature_id'));
        $expxs = explode(",",$request->post('feature_name'));
        
        $data=[];
        
         foreach ($exp as $key => $value) {
           
            //  CrmModuleFeatures::where('module_id', $request->post('module_id'),'id',$value)
            // ->update([
            // 'module_id' => $request->post('module_id'),
            // 'feature_name' => $expxs[$key]
            // ]);
            
            $cmsFeature = CrmModuleFeatures::where(['module_id'=>$request->post('module_id'),'id'=>$value])->first();
            $cmsFeature->module_id = $request->post('module_id');
            $cmsFeature->feature_name = $expxs[$key];
            $cmsFeature->update();
            
            $data[]=$cmsFeature;

        }
        
        // CrmModuleFeatures::where('leadid', $leadid)
        //     ->update([
        //     'agentid' => $tagent,
        //     ]);
        
         return response()->json([
            'message' => "Data Update successfully!",
            'data'=>$request->all(),
            'edata'=>$data,
            'code'=>'200',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RolesPermission\\CrmModuleFeatures  $crmModuleFeatures
     * @return \Illuminate\Http\Response
     */
    public function destroy(CrmModuleFeatures $crmModuleFeatures)
    {
        //
    }
}
