<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\Chartlevel3Request;
use App\Models\Finance\Chartlevel3;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;
use DateTime;
use DB;

class Chartlevel3Controller extends Controller
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
        $Level1Id = $request->Level1Id ?? "";
        $Level2Id = $request->Level2Id ?? "";
        
        if(empty($Level1Id) && empty($Level2Id)){
            $response = Chartlevel3::with(['getLevel1','getLevel2' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->orderBy($orderBy, $orderType)->paginate($perPage);
             $count = Chartlevel3::count();
        }else if(!empty($Level1Id) && empty($Level2Id)){
            $response = Chartlevel3::with(['getLevel1','getLevel2' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['level1Code'=>$Level1Id])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Chartlevel3::where(['level1Code'=>$Level1Id])->count();
        }else if(!empty($Level1Id) && !empty($Level2Id)){
            $response = Chartlevel3::with(['getLevel1','getLevel2' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['level1Code'=>$Level1Id,'level2Code'=>$Level2Id])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Chartlevel3::where(['level1Code'=>$Level1Id,'level2Code'=>$Level2Id])->count();
        }else{
            $response = Chartlevel3::with(['getLevel1','getLevel2' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Chartlevel3::count();
        }
     
        
       
        
        // SUCCESS
        if (count($response) > 0) {
        $data = $this->renderList($response);
        
        return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Chart Level 3 List Found',
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
                "level1Code"=>$value->level1Code,
                "level2Code"=>$value->level2Code,
                "Code"=>$value->Code,
                "Description"=>$value->Description,
                "status" =>$value->status,
                "statusTitle" => $value->status == 0 ? 'Pending' : 'Approved',
                "level1Codes"=>$value->getLevel1 == null || $value->getLevel1 == '' ? '' : $value->getLevel1->Code,
                 "level2Codes"=>$value->getLevel2 == null || $value->getLevel2 == '' ? '' : $value->getLevel2->Code,
                 "getLevel1"=>$value->getLevel1,
                 "getLevel2"=>$value->getLevel2,
                 'glstatus'=>$value->glstatus,
                
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
    public function store(Chartlevel3Request $request)
    {
        $check = Chartlevel3::where(['Code'=>$request->Code,'level1Code'=>$request->level1Code,'level2Code'=>$request->level2Code])->first();
        if(isset($check)){
             return response()->json([
                'message' => "Your Entry Already Exisits",
                'data' => 'Your Entry Already Exisits',
                'code'=>'203'
            ], 203);
            
        }else{
            
            $response = Chartlevel3::create([
                'level1Code' => $request->level1Code,
                'level2Code' => $request->level2Code,
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
        $response = Chartlevel3::where(['id'=>$id])->get();
            
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
    public function update(Chartlevel3Request $request,$id="")
    {
        
        Chartlevel3::where(['id'=>$id])->update([
            'level1Code' => $request->level1Code,
            'level2Code' => $request->level2Code,
            'Code' => $request->Code,
            'Description' =>  $request->Description,
            'glstatus'=>$request->glstatus,
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
            $Chartlevel1 = Chartlevel3::where(['id'=>$id])->delete();
        return response()->json([
            'message' => "Data deleted successfully!",
            'data'=>$Chartlevel1,
            'code'=>'200'
        ], 200);
    }
  
    
}