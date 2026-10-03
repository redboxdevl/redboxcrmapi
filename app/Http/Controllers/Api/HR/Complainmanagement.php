<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\HR\ComplainlistRequest;
use App\Models\HR\Complainlist;
use App\Models\HR\Circularcategory;
use App\Models\DocumentType;
use App\Models\Issuingauthority;
use App\Models\propertyType;
use App\Models\Purposeprop;
use App\Models\City;
use App\Models\HR\Employee;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;

class Complainmanagement extends Controller
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
        $agentId = $request->agentId ?? "";
        $deptId = $request->deptId ?? "";
       

        if (is_null($agentId) && empty($deptId)) {
            
            $response = Complainlist::orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = Complainlist::count();
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Complain Policy List Found',
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
        }   else if ($agentId == 'all' && empty($deptId)) {
            
                $response = Complainlist::orderBy($orderBy, $orderType)->paginate($perPage);
                
            $count = Complainlist::count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Complain Policy List Found',
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
            
        }else if ($agentId != 'all' && empty($deptId)) {
            
                $response = Complainlist::where(['agentId'=>$agentId])->orderBy($orderBy, $orderType)->paginate($perPage);
                
            $count = Complainlist::where(['agentId'=>$agentId])->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Complain Policy List Found',
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
            
        }else if ($agentId == 'all' && !empty($deptId)) {
            
                $response = Complainlist::orderBy($orderBy, $orderType)->paginate($perPage);
                
            $count = Complainlist::count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Complain Policy List Found',
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
              $response = Complainlist::where(['agentId'=>$agentId])->orderBy($orderBy, $orderType)->paginate($perPage);
                
            $count = Complainlist::where(['agentId'=>$agentId])->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Complain Policy List Found',
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
    }

    // PREPARE LIST
    public function renderList(Object $apiResponse)
    {
        $data = [];

        foreach ($apiResponse as $key => $value) {
         
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "agentId" => $value->agentId,
                "complainNote" => $value->complainNote,
                "status" => $value->status,
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
    public function store(ComplainlistRequest $request)
    {
        
        $response = Complainlist::create([
            'agentId' =>  $request->agentId,
            'complainNote' => $request->complainNote,
            'status' =>  $request->status,
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
    public function show(Complainlist $complainlist,$id="")
    {
        $response = Complainlist::where(['id'=>$id])->get();
            
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
    public function update(ComplainlistRequest $request, $id="")
    {


        Complainlist::where("id",$id)->update([
            'agentId' => $request->agentId,
            'complainNote' =>  $request->complainNote,
            'status' => $request->status,
        ]);
        
        return response()->json([
            'message' => "Data updated successfully!",
            'code'=>'200'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
     
    public function destroy(Request $request,$id='')
    {
        //$payreciepts->delete($request->all());
        
        $ComplainList = Complainlist::where(['id'=>$id])->delete();
      
        return response()->json([
            'message' => "Data deleted successfully!",
            'data'=>$ComplainList,
            'code'=>'200'
        ], 200);
    }
  
    
    
    
    
}