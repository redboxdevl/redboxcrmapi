<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\MappingRequest;
use App\Models\Finance\Generatemapping;
use App\Models\Finance\Generatemappinglevel;
use App\Models\Finance\Dimensionlevel2;
use App\Models\Finance\Dimensionlevel1;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;
use DateTime;
use DB;

class GenerateMappingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orderBy = $request->orderBy ?? "id";
        $orderType = $request->orderType ?? "asc";
        $perPage = $request->perPage ?? "12";
        $page = $request->page ?? "1";
        $q = $request->q ?? "";
        $agentId = $request->agentId;
     
        $response = Generatemapping::with(['getLevel3','getmappLevel2' => function ($query){
            $query->orderBy('id', 'desc')->get();
        } ,])->orderBy($orderBy, $orderType)->paginate($perPage);
        $count = Generatemapping::count();
        
        // SUCCESS
        if (count($response) > 0) {
        $data = $this->renderList($response);
        
        return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Generate Mapping List Found',
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
            $data2=[];
            
            $dmnl2 = json_decode($value->dimensionL2D,JSON_UNESCAPED_SLASHES);
            
            // $level3Code =  $value->getLevel3 == null ||  $value->getLevel3 == '' ? "" :  $value->getLevel3->Code;
            
            foreach($dmnl2 as $itemL2){
                // $data2[]=$itemL2;
                $Dimensionlevel2 = Dimensionlevel2::where(['id'=>$itemL2['value']])->first();
                // if($Dimensionlevel2 == null){
                //     $Dimensionlevel1="";
                // }else{
                //     $Dimensionlevel1 = Dimensionlevel1::where(['id'=>$Dimensionlevel2->level1Code])->first();
                // }
                
                
                array_push($data2, (object) [
                  'label' => $Dimensionlevel2 == null || $Dimensionlevel2 == '' ? '' : $Dimensionlevel2->Description,
                  'value' => '',
                ]);
            }
            
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "glLevel3id" => $value->glLevel3id,
                // "dimensionL2D" => json_decode($value->dimensionL2D,JSON_UNESCAPED_SLASHES),
                "getLevel3" => $value->getLevel3,
                // "Dimensionlevel2"=>$Dimensionlevel2,
                // "Dimensionlevel1"=>$Dimensionlevel1,
                // "mergeCode" => $value->getmappLevel2,
                'dimensionL2D'=>$data2,
               
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
    public function store(MappingRequest $request)
    {
       
       $check = Generatemapping::where(['glLevel3id'=>$request->glLevel3id])->count();
       
       if($check){
         return response()->json([
            'message' => "Data Already Exists!",
            'data' => $check,
            'code'=>'201'
        ], 200);
       }else{

        $response = Generatemapping::create([
            'glLevel3id' => $request->glLevel3id,
            'dimensionL2D' => $request->DimensionLevel2,
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
        $response = Generatemapping::where(['id'=>$id])->get();
            
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
    public function update(MappingRequest $request,$id="")
    {
        
        
        
        Generatemapping::where(['id'=>$id])->update([
            'glLevel3id' => $request->glLevel3id,
            'dimensionL2D' => $request->DimensionLevel2,
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
            $CGeneratemapping = Generatemapping::where(['id'=>$id])->delete();
        return response()->json([
            'message' => "Data deleted successfully!",
            'data'=>$CGeneratemapping,
            'code'=>'200'
        ], 200);
    }
  
    
}