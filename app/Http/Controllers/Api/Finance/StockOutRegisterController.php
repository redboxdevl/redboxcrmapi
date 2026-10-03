<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\StockoutregisterRequest;
use App\Models\Finance\Stockregister;
use App\Models\Finance\Stockoutregister;


use App\Models\DocumentType;
use App\Models\Issuingauthority;
use App\Models\propertyType;
use App\Models\Purposeprop;
use App\Models\Finance\Stockrecby;
use App\Models\Finance\Stockserial;


use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;
use DB;

class StockOutRegisterController extends Controller
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



 $response = Stockoutregister::orderBy($orderBy, $orderType)->paginate($perPage);
    
$count = Stockoutregister::count();

// SUCCESS
if (count($response) > 0) {
    $data = $this->renderList($response);

    return response()->json([
        'code' => '200',
        'success' => true,
        'message' => 'Stock Out List Found',
        'data' => $data,
        'orderBy' => $orderBy,
        'orderType' => $orderType,
        'perPage' => $perPage,
        'pageNo' => $page,
        'TotalCount'=>$count,
    ]);
}


// AGENT ID NOT SELECTED
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
    "deleted_at" => $value->deleted_at,
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
public function store(StockoutregisterRequest $request)
{
    
    $projectId = $request->projectId;
    $stockTypeid = $request->stockTypeid;
    $stockNatureid = $request->stockNatureid;
    $serialFrom = $request->serialFrom;
    $serialTo = $request->serialTo;
    $quantityOut = $request->quantityOut;
    $stockDeliveredId = $request->stockDeliveredId;
    $StockOutId = $request->StockOutId;
    $stockOutdate = $request->stockOutdate;
    
    
    $CustomerName = $request->CustomerName;
    $PropertyNo = $request->PropertyNo;
    $Size = $request->Size;
    $PropertyType = $request->PropertyType;
    
    
    $checkSerialShow = Stockserial::where(['stockid'=>$stockTypeid])->get();
    
    $dataxs=[];
    
    if($stockTypeid == '13' || $stockTypeid == '2' || $stockTypeid == '6'){
        
        $countRange = range($serialFrom, $serialTo);
        
        foreach($countRange as $rangeItem){
            
            Stockserial::where(['stockid'=>$StockOutId,'serialNo'=>$rangeItem])->update([
            'status' =>  'Paid',
            ]);
        
        }
       
        $checkRemainingQty = Stockregister::where(['id'=>$StockOutId])->first();
        
        $remainingCount = $checkRemainingQty->remainingQty - $quantityOut;
        
        Stockregister::where(['id'=>$StockOutId])->update([
        'remainingQty' =>  $remainingCount,
        ]);
        
        $response = Stockoutregister::create([
        'StockInId'=>$StockOutId,
        'stockOutdate' =>  $stockOutdate,
        'projectId' =>  $projectId,
        'stockTypeid' =>  $stockTypeid,
        'stockNatureid' =>  $stockNatureid,
        'quantityOut' =>  $quantityOut,
        'stockDeliveredId' => $stockDeliveredId,
        'serialFrom' =>  $serialFrom,
        'serialTo' =>  $serialTo,
        'CustomerName' => $CustomerName,
        'PropertyNo' => $PropertyNo,
        'Size' =>  $Size,
        'PropertyType' =>  $PropertyType,
        ]);
        
        
        
    }else{
        
        $checkStock = Stockregister::where(['id'=>$StockOutId])->first();
        
        $remianingQty = $checkStock->remainingQty - $quantityOut;
        
        Stockregister::where('id',$StockOutId)->update(['remainingQty'=>$remianingQty]);
        
        $response = Stockoutregister::create([
        'StockInId'=>$StockOutId,
        'stockOutdate' =>  $stockOutdate,
        'projectId' =>  $projectId,
        'stockTypeid' =>  $stockTypeid,
        'stockNatureid' =>  $stockNatureid,
        'quantityOut' =>  $quantityOut,
        'stockDeliveredId' => $stockDeliveredId,
        'serialFrom' =>  0,
        'serialTo' =>  0,
        'CustomerName' => $CustomerName,
        'PropertyNo' => $PropertyNo,
        'Size' =>  $Size,
        'PropertyType' =>  $PropertyType,
        ]);
      
        
    }
    
        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $request->all(),
            'CountX'=>$dataxs,
            'code'=>'200'
        ], 200);
        
    // }
   

}

/**
* Display the specified resource.
*
* @param  \App\Models\Employee  $employee
* @return \Illuminate\Http\Response
*/
public function show(Stockregister $stockregister)
{
$response = Stockregister::where(['id'=>$stockregister->id])->get();

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
public function update(StockregisterRequest $request, Stockregister $stockregister,$id="")
{

}

/**
* Remove the specified resource from storage.
*
* @param  \App\Models\Employee  $employee
* @return \Illuminate\Http\Response
*/
public function destroy(StockregisterRequest $request, Stockregister $stockregister)
{
$stockregister->delete($request->all());

return response()->json([
'message' => "Data deleted successfully!",
'code'=>'200'
], 200);
}




}