<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\DimensionlistRequest;
use App\Models\Finance\Dimensioncode;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;

class DimensionController extends Controller
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
        $perPage = $request->perPage ?? "500";
        $page = $request->page ?? "1";
        $q = $request->q ?? "";
        $agentId = $request->agentId;

        if (is_null($agentId)) {
            
            $response = Dimensioncode::orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = Dimensioncode::count();
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Dimension List Found',
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
        }   else if ($agentId == 'all') {
            
                $response = Dimensioncode::orderBy($orderBy, $orderType)->paginate($perPage);
                
            $count = Dimensioncode::count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Dimension List Found',
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
                "code" => $value->code,
                "dimensionDesc" => $value->dimensionDesc,
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
    public function store(DimensionlistRequest $request)
    {
        
        // if($request->hasFile("Attachement")){
        //     $image = $request->file("Attachement");
        //     $ext = $image->extension();
        //     $image_name=time().'.'.$ext;
        //     $image->storeAs('/public/bankLogo/',$image_name);
        //     $FileData = "public/storage/bankLogo/".$image_name;
            
        // }else{
        //     $FileData="";
        // }
        
        
        // $response = Banklist::create([
        //     'title' =>  $request->title,
        //     'branch' =>  $request->branchtitle,
        //     'accountno' =>  $request->accountno,
        //     'iban' =>  $request->iban,
        //     'bankname' =>  $request->bankname,
        //     'branchcode' =>  $request->branchcode,
        //     'City' =>  $request->City,
        //     'SwiftCode' =>  $request->SwiftCode,
        //     'NiftCode' =>  $request->NiftCode,
        //     'bankimage' =>  $FileData,
        // ]);
        
        return response()->json([
            'message' => "Data saved successfully!",
            'data' => '',
            'code'=>'200'
        ], 200);
        
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Dimensioncode $dimensioncode,$id="")
    {
        $response = Dimensioncode::where(['id'=>$id])->get();
            
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
    public function update(DimensionlistRequest $request, Dimensioncode $dimensioncode,$id="")
    {

        // $checkFiles = Banklist::findOrFail($banklist->id);
        
        // if($request->hasFile("Attachement")){
        //     $image = $request->file("Attachement");
        //     $ext = $image->extension();
        //     $image_name=time().'.'.$ext;
        //     $image->storeAs('/public/bankLogo/',$image_name);
        //     $image_names = "public/storage/bankLogo/".$image_name;
        // }else{
        //     $image_names=$checkFiles->bankimage;
        // }

        // $banklist->update([
        //     'title' =>  $request->title,
        //     'branch' =>  $request->branchtitle,
        //     'accountno' =>  $request->accountno,
        //     'iban' =>  $request->iban,
        //     'bankname' =>  $request->bankname,
        //     'branchcode' =>  $request->branchcode,
        //     'bankimage' =>  $image_names,
        //     'City' =>  $request->City,
        //     'SwiftCode' =>  $request->SwiftCode,
        //     'NiftCode' =>  $request->NiftCode,
        // ]);
        
        return response()->json([
            'message' => "Data updated successfully!",
            'data' => '',
            'code'=>'200'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(DimensionlistRequest $request, Dimensioncode $dimensioncode)
    {
       // $glcode->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code'=>'200'
        ], 200);
    }
    
    
    
    
}