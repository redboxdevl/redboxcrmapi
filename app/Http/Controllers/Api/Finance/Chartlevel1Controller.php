<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\Chartlevel1Request;
use App\Models\Finance\Chartlevel1;



use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;
use DateTime;
use DB;

class Chartlevel1Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orderBy = $request->orderBy ?? "Code";
        $orderType = $request->orderType ?? "asc";
        $perPage = $request->perPage ?? "12";
        $page = $request->page ?? "1";
        $q = $request->q ?? "";
        $agentId = $request->agentId;
     
        $response = Chartlevel1::orderBy($orderBy, $orderType)->paginate($perPage);
        $count = Chartlevel1::count();
        
        // SUCCESS
        if (count($response) > 0) {
        $data = $this->renderList($response);
        
        return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Chart Level 1 List Found',
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

    // PREPARE LIST
    public function renderList(Object $apiResponse)
    {
        $data = [];

        foreach ($apiResponse as $key => $value) {
          
            
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "Code"=>$value->Code,
                "Description"=>$value->Description,
                "status" =>$value->status,
                "statusTitle" => $value->status == 0 ? 'Pending' : 'Approved',
                
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
    public function store(Chartlevel1Request $request)
    {
        $check = Chartlevel1::where(['Code'=>$request->Code])->first();
        if(isset($check)){
             return response()->json([
                'message' => "Your Entry Already Exisits",
                'data' => 'Your Entry Already Exisits',
                'code'=>'203'
            ], 203);
            
        }else{
            
            $response = Chartlevel1::create([
                'Code' => $request->Code,
                'Description' =>  $request->Description,
            ]);
            
            return response()->json([
                'message' => "Data saved successfully!",
                'data' => $response,
                'code'=>'200'
            ], 200);
          
            
        }
        
       
        
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show($id='')
    {
        $response = Chartlevel1::where(['id'=>$id])->get();
            
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
    public function update(Chartlevel1Request $request,$id="")
    {
        
        
        
        Chartlevel1::where(['id'=>$id])->update([
            'Code' => $request->Code,
            'Description' =>  $request->Description,
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
        // $checkoutwardregister->delete($request->all());
            $Chartlevel1 = Chartlevel1::where(['id'=>$id])->delete();
        return response()->json([
            'message' => "Data deleted successfully!",
            'data'=>$Chartlevel1,
            'code'=>'200'
        ], 200);
    }
  
    
}