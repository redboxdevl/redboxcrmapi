<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\ChequeRegisterRequest;
use App\Models\Finance\Chequeregister;
use App\Models\Finance\Checkoutwardregister;
use App\Models\Finance\Chequelist;
use App\Models\Finance\Banklist;
use App\Models\City;


use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;
use DateTime;

class ChequeRegisterController extends Controller
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
    $agentId = $request->agentId;
    $company_id = $request->company_id ?? "";

    if (is_null($agentId)) {

        $response = Chequeregister::where(['company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
        $count = Chequeregister::where(['company_id'=>$company_id])->count();
        // SUCCESS
        if (count($response) > 0) {
            $data = $this->renderList($response);

            return response()->json([
                'code' => '200',
                'success' => true,
                'message' => 'Cheque Register List Found',
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
        
        $response = Chequeregister::where(['company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
            
        $count = Chequeregister::where(['company_id'=>$company_id])->count();
        
        // SUCCESS
        if (count($response) > 0) {
            $data = $this->renderList($response);

            return response()->json([
                'code' => '200',
                'success' => true,
                'message' => 'Cheque Register List Found',
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
        
        $Banklist = Banklist::where(['id'=>$value->AccountNo])->first();
        if($Banklist == '' || $Banklist == null){
            $City = "";
        }else{
            $City = City::where(['id'=>$Banklist->City])->first();
        }
        
           
        array_push($data, (object) [
            "id" => $value->id,
            "created_at" => $value->created_at,
            "updated_at" => $value->updated_at,
            "deleted_at" => $value->deleted_at,
            "chequeDate" => $value->chequeDate,
            "AccountNoId" =>$value->AccountNo,
            "AccountNo" => $Banklist == '' || $Banklist == null ? '' : $Banklist->accountno,
            "BankName" => $Banklist == '' || $Banklist == null ? '' : $Banklist->bankname,
            "BranchName" => $Banklist == '' || $Banklist == null ? '' : $Banklist->branch,
            "AccountTitle" => $Banklist == '' || $Banklist == null ? '' : $Banklist->title,
            "cityName" => $City == '' || $City == null ? '' : $City->cityName,
            "SerialStart" => $value->SerialStart,
            "SerialEnd" => $value->SerialEnd,
            "NoofCheque" => $value->NoofCheque,
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
public function store(ChequeRegisterRequest $request)
{
    
    $checkCOnd = Chequeregister::where(['SerialStart'=>$request->StartSerialNo,'SerialEnd'=>$request->EndSerialNo])->first();
    
    if($checkCOnd == null){
        
        $checkList =$request->NoofCheque;
        
        $response = Chequeregister::create([
            'chequeDate' =>  $request->CuDate,
            'AccountNo' =>  $request->AccountNo,
            'SerialStart' =>  $request->StartSerialNo,
            'SerialEnd' =>  $request->EndSerialNo,
            'NoofCheque' =>  $request->NoofCheque,
            'company_id'=>$request->company_id,
        ]);
        
        $chequeId = $response->id;
      
         $seq = [];
         
         $FreeInput = $request->StartSerialNo;
        
         $numericVal = preg_replace('/[^0-9]/', '', $request->StartSerialNo);
         $numericEndVal = preg_replace('/[^0-9]/', '', $request->EndSerialNo);
         $AlphaVal = preg_replace('/[^A-Z]/', '', $request->StartSerialNo);
        
         $i=0;
         
         $checkNox = [];
        for ($x = 1; $x <= $checkList; $x++) {
        
            $ix = $i++;
        
            if($AlphaVal == ''){
                if($numericVal < 10){
                    $lastdigit = substr($numericVal, 0, -1);
                    $chequeListNew =$lastdigit."".+$ix+1;
                }else{
                    $chequeListNew =$numericVal;
                }
                $chequeListNew = $AlphaVal."".$numericVal+$ix;
            }else{
                $chequeListNew = $AlphaVal."".$numericVal+$ix;
            }
        
            $checkNox[]=$chequeListNew;
        
            $Chequelist = new Chequelist();
        
            $Chequelist->chequeId = $chequeId;
            $Chequelist->chequeNo = $chequeListNew;
            $Chequelist->cheqStatus = 'Released';
        
            $Chequelist->save();
        
            // $seq[]=$chequeListNew;
        
        }
    
        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $checkNox,
            'code'=>'200'
        ], 200);
        
    }else{
        
        return response()->json([
            'message' => "Check Serial Sequence Already Add Kindly Please Correct Serial Sequence !",
            'data' => '',
            'code'=>'201'
        ], 201);
        
    }
    
    

    
    
    
    
}

/**
 * Display the specified resource.
 *
 * @param  \App\Models\Employee  $employee
 * @return \Illuminate\Http\Response
 */
public function show(Chequeregister $chequeregister)
{
    $response = Chequeregister::where(['id'=>$chequeregister->id])->get();
        
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
public function update(ChequeRegisterRequest $request, Chequeregister $chequeregister,$id="")
{
    $chequeregister->update([
        'status' =>  $request->status,
        'company_id'=>$request->company_id,
    ]);
    
    return response()->json([
        'message' => "Data updated successfully!",
        'data' => $chequeregister,
        'code'=>'200'
    ], 200);
}

/**
 * Remove the specified resource from storage.
 *
 * @param  \App\Models\Employee  $employee
 * @return \Illuminate\Http\Response
 */
public function destroy(AssetRegisterRequest $request, Chequeregister $chequeregister)
{
    $chequeregister->delete($request->all());

    return response()->json([
        'message' => "Data deleted successfully!",
        'code'=>'200'
    ], 200);
}

public function GetChequeListNo (Request $request , $id='') {
       
        
    $response = Chequeregister::where(['AccountNo'=>$id,'status'=>'1'])->get();
    
    $acc = $request->acc;
   
    $data = [];
    
    foreach($response as $cheqD){
        
        if(empty($acc)){
             $chequeListData = Chequelist::where(['chequeId'=>$cheqD->id,'cheqStatus'=>'Released'])->get();
        }else{
             $chequeListData = Chequelist::where(['chequeId'=>$cheqD->id])->get();
        }
        
       
        foreach($chequeListData as $value){
            
            $matchCheqye = Checkoutwardregister::where(['chequeNoId'=>$value->id])->count();
            
            if($matchCheqye){
                
            }else{
                array_push($data, (object) [
                    "id" => $value->id,
                    "created_at" => $value->created_at,
                    "updated_at" => $value->updated_at,
                    "deleted_at" => $value->deleted_at,
                    "chequeNo" => $value->chequeNo,
                    "cheqStatus" => $value->cheqStatus,
                ]);
            }
            
            
        }
       
         
    }
    
    
    return response()->json([
        'message' => "Data retrieved successfully!",
        'data' => $data,
        'code'=>'200'
    ], 200);
    
   
}






}