<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Requests\Inventory\ProjectCategoryRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Models\Inventory\Projectcategory;
use App\Models\Inventory\Projectinventory;
use Illuminate\Http\Request;

class InventoryfilterdataController extends Controller
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
        $proid = $request->proid ?? "";
        
        if($proid == 17 || $proid == 12 || $proid == 11 || $proid == 9 || $proid == 8){
            $getBlockData = Projectinventory::where(['project_id'=>$proid])->groupBy('jsonFormat->tower')->get();
             $getSizeData = Projectinventory::where(['project_id'=>$proid])->groupBy('jsonFormat->size')->get();
        }else{
            $getBlockData = Projectinventory::where(['project_id'=>$proid])->groupBy('jsonFormat->block')->get();
             $getSizeData = Projectinventory::where(['project_id'=>$proid])->groupBy('jsonFormat->measuring_unit')->get();
        }
       
        $getTypeData = Projectinventory::where(['project_id'=>$proid])->groupBy('jsonFormat->property_type')->get();
        
        // $data = $this->renderblockList($getBlockData);
        
        return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Record Found',
            'getBlockData' => $getBlockData,
            'getSizeData' => $getSizeData,
            'getTypeData'=>$getTypeData,
            
        ]);
       
    }

    // PREPARE LIST
    public function renderblockList(Object $getBlockData)
    {
        $data = [];

        foreach ($getBlockData as $key => $value) {
            $newdata=json_decode($value->jsonFormat,JSON_UNESCAPED_SLASHES);
            array_push($data, (object) [
                "id" => $value->id,
                "project_id" => $value->project_name,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "project_category_name" => $value->location_id,
                "plotno" => $newdata['property_no'],
            ]);
        }

        return $data;
    }
    
    
    public function getPropertyNo(Request $request){
        
        $proid = $request->ProjectId;
        $BlockNo = $request->BlockNo;
        
        if($proid == 17 || $proid == 12 || $proid == 11 || $proid == 9 || $proid == 8){
            $getPlotNoData = Projectinventory::where(['project_id'=>$proid,'jsonFormat->tower'=>$BlockNo])->get();
             
        }else{
            $getPlotNoData = Projectinventory::where(['project_id'=>$proid,'jsonFormat->block'=>$BlockNo])->get();
            
        }
        
         $data = $this->renderblockList($getPlotNoData);
        
        return response()->json([
            'message' => "Data Recieved",
            'data' => $data,
        ], 200);
        
    }
    
    
   
    
     public function getPropertyDetails(Request $request){
        
        $proid = $request->ProjectId;
        $BlockNo = $request->BlockNo;
        $PropertyNo = $request->PropertyNo;
        
       
            if($proid == 17 || $proid == 12 || $proid == 11 || $proid == 9 || $proid == 8){
            $getPlotNoDetailsData = Projectinventory::where(['project_id'=>$proid,'jsonFormat->tower'=>$BlockNo,'jsonFormat->property_no'=>$PropertyNo])->get();
             
            }else{
                $getPlotNoDetailsData = Projectinventory::where(['project_id'=>$proid,'jsonFormat->block'=>$BlockNo,'jsonFormat->property_no'=>$PropertyNo])->get();
                
            }
            
             $data = $this->renderProDetailsList($getPlotNoDetailsData);
       
        
        return response()->json([
            'message' => "Data Recieved",
            'data' => $data,
        ], 200);
        
    }
    
     // PREPARE LIST
    public function renderProDetailsList(Object $getProDetailsData)
    {
        $data = [];

        foreach ($getProDetailsData as $key => $value) {
            $newdata=json_decode($value->jsonFormat,JSON_UNESCAPED_SLASHES);
            array_push($data, (object) [
                "id" => $value->id,
                "project_id" => $value->project_id,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "project_category_name" => $value->location_id,
                "newdata" => $newdata,
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
    public function store(ProjectCategoryRequest $request)
    {
        $response = Projectcategory::create($request->all());

        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $response
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inventory\Projectcategory  $projectcategory
     * @return \Illuminate\Http\Response
     */
    public function show(Projectcategory $projectcategory)
    {
        $response = Projectcategory::findOrFail($projectcategory->id);

        return response()->json([
            'message' => "Data retrieved successfully!",
            'data' => $response
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inventory\Projectcategory  $projectcategory
     * @return \Illuminate\Http\Response
     */
    public function update(ProjectCategoryRequest $request, Projectcategory $projectcategory)
    {
        $projectcategory->update($request->all());

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $projectcategory
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inventory\Projectcategory  $projectcategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProjectCategoryRequest $request, Projectcategory $projectcategory)
    {
        $projectcategory->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
        ], 200);
    }
}
