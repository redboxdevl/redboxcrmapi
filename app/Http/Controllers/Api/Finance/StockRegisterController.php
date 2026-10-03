<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\StockregisterRequest;
use App\Models\Finance\Stockregister;
use App\Models\DocumentType;
use App\Models\Issuingauthority;
use App\Models\propertyType;
use App\Models\Purposeprop;
use App\Models\Finance\Stockrecby;
use App\Models\Finance\Stockserial;
use App\Models\Finance\stockType;


use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;
use DB;

class StockRegisterController extends Controller
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

$tid = $request->tid;
$pid = $request->pid;
$nid = $request->nid;


    $StockNatureId = $request->StockNatureId ?? "";
    $ProjectId = $request->ProjectId ?? "";
    $StockTypeId = $request->StockTypeId ?? "";

if($tid !='' || $pid !='' || $nid !=''){

 $response = Stockregister::with(['getStockTypeId','getProjectId','getNatureId' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->select('*', DB::raw('SUM(quantityIn) as quantityCOunt') , DB::raw('SUM(remainingQty) as remainingquantityCOunt') , DB::raw('SUM(serialTo) as serialToCOunt'))->groupBy(['projectid','stocktypeid','stocknatureid'])->where(['stocktypeid'=>$tid,'projectid'=>$pid,'stocknatureid'=>$nid])->orderBy($orderBy, $orderType)->get();
    
$count = Stockregister::count();

// SUCCESS
if (count($response) > 0) {
    $data = $this->renderList($response);

    return response()->json([
        'code' => '200',
        'success' => true,
        'message' => 'Stock List Found',
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


}else if($request->tsid !=''){

 $response = Stockregister::with(['getStockTypeId','getProjectId','getNatureId' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->where(['stocktypeid'=>$request->tsid])->orderBy($orderBy, $orderType)->paginate($perPage);
    
$count = Stockregister::count();

// SUCCESS
if (count($response) > 0) {
    $data = $this->renderList($response);

    return response()->json([
        'code' => '200',
        'success' => true,
        'message' => 'Stock List Found',
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


}else{

if (is_null($agentId)) {

$response = Stockregister::with(['getStockTypeId','getProjectId','getNatureId' => function ($query){
    $query->orderBy('id', 'desc')->get();
} ,])->select('*', DB::raw('SUM(quantityIn) as quantityCOunt') , DB::raw('SUM(remainingQty) as remainingquantityCOunt'))->groupBy(['projectid','stocktypeid','stocknatureid'])->orderBy($orderBy, $orderType)->paginate($perPage);
$count = Stockregister::count();
// SUCCESS
if (count($response) > 0) {
    $data = $this->renderList($response);

    return response()->json([
        'code' => '200',
        'success' => true,
        'message' => 'Stock List Found',
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
    
        if($StockNatureId !=''){
            
            $response = Stockregister::with(['getStockTypeId','getProjectId','getNatureId' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ,])->where(['stocknatureid'=>$StockNatureId])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = Stockregister::where(['stocknatureid'=>$StockNatureId])->count();
            
        }else if($ProjectId !=''){
            
            $response = Stockregister::with(['getStockTypeId','getProjectId','getNatureId' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ,])->where(['projectid'=>$ProjectId])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = Stockregister::where(['projectid'=>$ProjectId])->count();
            
        }else if($StockTypeId !=''){
            
            $response = Stockregister::with(['getStockTypeId','getProjectId','getNatureId' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ,])->where(['stocktypeid'=>$StockTypeId])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = Stockregister::where(['stocktypeid'=>$StockTypeId])->count();
            
        }else{
            
            //echo 'as';
            
            $response = Stockregister::with(['getStockTypeId','getProjectId','getNatureId' => function ($query){
            $query->orderBy('id', 'desc')->get();
        } ,])->select('*', DB::raw('SUM(quantityIn) as quantityCOunt') , DB::raw('SUM(remainingQty) as remainingquantityCOunt') , DB::raw('SUM(serialTo) as serialToCOunt'))->groupBy(['projectid','stocktypeid','stocknatureid'])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = Stockregister::count();
        }
        
    

// SUCCESS
if (count($response) > 0) {
    $data = $this->renderList($response);

    return response()->json([
        'code' => '200',
        'success' => true,
        'message' => 'Stock List Found',
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



}

// PREPARE LIST
public function renderList(Object $apiResponse)
{
$data = [];

foreach ($apiResponse as $key => $value) {
    
    //Balance Qty Formula
    
    $BalanceQty = $value->quantityIn - 0 - 0;
    
    
array_push($data, (object) [
    "id" => $value->id,
    "created_at" => $value->created_at,
    "updated_at" => $value->updated_at,
    "deleted_at" => $value->deleted_at,
    "stockIndate" => $value->stockIndate,
    "projectid" => $value->projectid,
    "stocknatureid" => $value->stocknatureid,
    "stocktypeid" => $value->stocktypeid,
    "typeName" => $value->getStockTypeId == null || $value->getStockTypeId == '' ? '' : $value->getStockTypeId->typeName,
    "project_name" => $value->getProjectId == null || $value->getProjectId == '' ? '' : $value->getProjectId->project_name,
    "natureTitle" => $value->getNatureId == null || $value->getNatureId == '' ? '' : $value->getNatureId->natureTitle,
    "quantityIn" => $value->quantityIn,
    "remainingQty" => $value->remainingQty,
    "stockrecby" => $value->stockrecby,
    "stockdeliveredby" => $value->stockdeliveredby,
    "serialFrom" => $value->serialFrom,
    "serialTo" => $value->serialTo,
    "quantityCOunt"=>$value->quantityCOunt,
    "remainingCount" =>$value->remainingquantityCOunt,
    "serialToCOunt"=>$value->serialToCOunt,
    "BalanceQty" => $BalanceQty,
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
public function store(StockregisterRequest $request)
{

if($request->hasFile("stockImg")){
$image = $request->file("stockImg");
$ext = $image->extension();
$image_name=time().'.'.$ext;
$image->storeAs('/public/StockImage/',$image_name);
$FileData = "public/storage/StockImage/".$image_name;

}else{
$FileData="";
}

$StockRecByIds=0;
$StockTypeIds=0;

if($request->stocktypeid == 'others'){
    $StockData = stockType::create([
        'typeName' =>  $request->otherStockType,
        'status' =>  1,
    ]);
    $lastId = $StockData->id;
    $StockTypeIds = $lastId;
}else{
    $StockTypeIds = $request->stocktypeid;
}

if($request->stockrecby == 'others'){

    $RecData = Stockrecby::create([
        'rec_title' =>  $request->otherStockById,
        'status' =>  1,
    ]);
    
    $lastId = $RecData->id;
    $StockRecByIds = $lastId;

}else{
    $StockRecByIds = $request->stockrecby;
}


if($request->stocktypeid == '2' || $request->stocktypeid == '6' || $request->stocktypeid == '13' ){
    // serial no insert
    
    $checkReg = Stockregister::where(['projectid'=>$request->projectid,'stocknatureid'=>$request->stocknatureid,'stocktypeid'=>$request->stocktypeid])->first();
    
    if(is_null($checkReg)){
        
        $response = Stockregister::create([
        'stockIndate' =>  $request->stockIndate,
        'projectid' =>  $request->projectid,
        'stocknatureid' =>  $request->stocknatureid,
        'stocktypeid' =>  $StockTypeIds,
        'quantityIn' =>  $request->quantityIn,
        'remainingQty' =>  $request->quantityIn,
        'stockrecby' =>  $StockRecByIds,
        'stockdeliveredby' =>  $request->stockdeliveredby,
        'stockImg' => $FileData,
        'serialFrom' =>  $request->serialFrom,
        'serialTo' =>  $request->serialTo == null || $request->serialTo == '' ? $request->quantityIn : $request->serialTo,
        ]);
        
        $stockId = $response->id;
    
        $countRange = range($request->serialFrom, $request->serialTo);
        
        $checkData = Stockregister::where(['id'=>$stockId])->first();
        
        foreach($countRange as $rangeItem){
            
            $secondCheck = Stockserial::where(['stockid'=>$stockId,'serialNo'=>$rangeItem])->first();
            
            if(is_null($secondCheck)){
                $response2 = Stockserial::create([
                    'stockid' =>  $stockId,
                    'serialFrom' =>  $checkData->serialFrom,
                    'serialTo' =>  $rangeItem,
                    'serialNo' => $rangeItem,
                    'status' => 'unpaid',
                ]);
            }else{
                
            }
                
        }
                
        
    }else{
       
       $idx = $checkReg['id'];
       $int = (int)$idx;
       
       
       
        $response = Stockregister::where(['id'=>$int])->update([
        'stockIndate' =>  $request->stockIndate,
        'projectid' =>  $request->projectid,
        'stocknatureid' =>  $request->stocknatureid,
        'stocktypeid' =>  $StockTypeIds,
        'quantityIn' =>  $checkReg['quantityIn'] + $request->quantityIn,
        'remainingQty' => $checkReg['remainingQty'] + $request->quantityIn,
        'stockrecby' =>  $StockRecByIds,
        'stockdeliveredby' =>  $request->stockdeliveredby,
        'stockImg' => $FileData,
        'serialFrom' =>   $checkReg['serialFrom'],
        'serialTo' =>  $checkReg['quantityIn'] + $request->quantityIn,
        ]);

        $stockId = $int;
        
        $countRange = range($request->serialFrom, $request->serialTo);
        
        $checkData = Stockregister::where(['id'=>$stockId])->first();
        
        foreach($countRange as $rangeItem){
      
        $secondCheck = Stockserial::where(['stockid'=>$stockId,'serialNo'=>$rangeItem])->first();
            
            if(is_null($secondCheck)){
            $response2 = Stockserial::create([
                'stockid' =>  $stockId,
                'serialFrom' =>  $checkData->serialFrom,
                'serialTo' =>  $rangeItem,
                'serialNo' => $rangeItem,
                'status' => 'unpaid',
            ]);
            
            }else{
                
            }
        
        
        }
        
    }
    
    
    
    
}else{
    //serial not add
    
  
    
    $response = Stockregister::create([
        'stockIndate' =>  $request->stockIndate,
        'projectid' =>  $request->projectid,
        'stocknatureid' =>  $request->stocknatureid,
        'stocktypeid' =>  $StockTypeIds,
        'quantityIn' =>  $request->quantityIn,
        'remainingQty' =>  $request->quantityIn,
        'stockrecby' =>  $StockRecByIds,
        'stockdeliveredby' =>  $request->stockdeliveredby,
        'stockImg' => $FileData,
        'serialFrom' => 0,
        'serialTo' =>  0,
    ]);
    
}

   

    return response()->json([
        'message' => "Data saved successfully!",
        'data' => $request->all(),
        'code'=>'200'
    ], 200);


}

/**
* Display the specified resource.
*
* @param  \App\Models\Employee  $employee
* @return \Illuminate\Http\Response
*/
public function show(Stockregister $stockregister)
{
$response = Stockregister::with(['getStockTypeId','getProjectId','getNatureId' => function ($query){
            $query->orderBy('id', 'desc')->get();
        } ,])->select('*', DB::raw('SUM(quantityIn) as quantityCOunt') , DB::raw('SUM(remainingQty) as remainingquantityCOunt') , DB::raw('SUM(serialTo) as serialToCOunt'))->where(['id'=>$stockregister->id])->groupBy(['projectid','stocktypeid','stocknatureid'])->get();

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
public function update(StockregisterRequest $request, Stockregister $stockregister,$id="")
{

$checkFiles = Stockregister::findOrFail($stockregister->id);

if($request->hasFile("stockImg")){
$image = $request->file("stockImg");
$ext = $image->extension();
$image_name=time().'.'.$ext;
$image->storeAs('/public/StockImage/',$image_name);
$image_names = "public/storage/StockImage/".$image_name;
}else{
$image_names=$checkFiles->stockImg;
}


if($request->stocktypeid == '2' || $request->stocktypeid == '6' || $request->stocktypeid == '13' ){
    
    //serial update condition
    
    $stockId = $request->stockId;
    $serialFrom = $request->serialFrom;
    $serialTo = $request->serialTo;
    
    $checkData = Stockregister::where(['id'=>$stockregister->id])->first();
    
    $rangeSerial = range($serialFrom,$serialTo);
        
    $getSerialData=[];
        
    foreach($rangeSerial as $x => $rangeData ){
 
       $secondCheck = Stockserial::where(['stockid'=>$stockId,'serialNo'=>$rangeData])->first();
       $getSerialData[]=$secondCheck;
      
       
      if(is_null($secondCheck)){
            $response2 = Stockserial::create([
                'stockid' =>  $stockId,
                'serialFrom' =>  $checkData->serialFrom,
                'serialTo' =>  $rangeData,
                'serialNo' => $rangeData,
                'status' => 'unpaid',
            ]);
      }else{
         
      }
       
    }
    
    $getLastSerialNo = Stockserial::where(['stockid'=>$stockId])->orderBy('id', 'desc')->first();
    
    $stockregister->update([
        'stockIndate' =>  $request->stockIndate,
        'projectid' =>  $request->projectid,
        'stocknatureid' =>  $request->stocknatureid,
        'stocktypeid' =>  $request->stocktypeid,
        'quantityIn' =>  $getLastSerialNo->serialNo,
        'remainingQty' =>  $checkData->remainingQty+$request->quantityIn,
        'stockrecby' =>  $request->stockrecby,
        'stockdeliveredby' =>  $request->stockdeliveredby,
        'stockImg' =>  $image_names,
        'serialFrom' =>  $checkData->serialFrom,
        'serialTo' =>  $getLastSerialNo->serialNo,
    ]);
        
    
}else{
    
    //quanity update conditon
    
    $checkData = Stockregister::where(['id'=>$stockregister->id])->first();
    
    $stockregister->update([
        'stockIndate' =>  $request->stockIndate,
        'projectid' =>  $request->projectid,
        'stocknatureid' =>  $request->stocknatureid,
        'stocktypeid' =>  $request->stocktypeid,
        'quantityIn' =>  $checkData->quantityIn + $request->quantityIn,
        'remainingQty' => $checkData->remainingQty + $request->quantityIn,
        'stockrecby' =>  $request->stockrecby,
        'stockdeliveredby' =>  $request->stockdeliveredby,
        'stockImg' =>  $image_names,
        'serialFrom' =>  0,
        'serialTo' =>  0,
    ]);
    
}





return response()->json([
'message' => "Data updated successfully!",
'data' => $request->all(),
'code'=>'200'
], 200);
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


public function StockFilters(Request $request){
    
    $StockInDateStart = $request->StockInDateStart ?? "";
    $StockInDateEnd = $request->StockInDateEnd ?? "";
    
    $ProjectsFrom = $request->ProjectsFrom ?? "";
    $ProjectsTo = $request->ProjectsTo ?? "";
    
    $StockNatureFrom = $request->StockNatureFrom ?? "";
    $StockNatureTo = $request->StockNatureTo ?? "";
    
    $RecivedByFrom = $request->RecivedByFrom ?? "";
    $RecivedByFrom = $request->RecivedByFrom ?? "";
    
    if($StockInDateStart != '' && $StockInDateEnd !=''){
    
     $response = Stockregister::with(['getStockTypeId','getProjectId','getNatureId' => function ($query){
    $query->orderBy('id', 'desc')->get();
    } ,])->whereBetween('stockIndate', array($StockInDateStart,$StockInDateEnd))->orderBy('id','desc')->get();
     $count = Stockregister::whereBetween('stockIndate', array($StockInDateStart,$StockInDateEnd))->count();
    
    }else if($ProjectsFrom != '' && $ProjectsTo !=''){
   
     $response = Stockregister::with(['getStockTypeId','getProjectId','getNatureId' => function ($query){
    $query->orderBy('id', 'desc')->get();
    } ,])->whereBetween('projectid', array($ProjectsFrom,$ProjectsTo))->orderBy('id','desc')->get();
     $count = Stockregister::whereBetween('projectid', array($ProjectsFrom,$ProjectsTo))->count();
    
    }else if($StockNatureFrom != '' && $StockNatureTo !=''){
    
     $response = Stockregister::with(['getStockTypeId','getProjectId','getNatureId' => function ($query){
    $query->orderBy('id', 'desc')->get();
    } ,])->whereBetween('stocknatureid', array($StockNatureFrom,$StockNatureTo))->orderBy('id','desc')->get();
     $count = Stockregister::whereBetween('stocknatureid', array($StockNatureFrom,$StockNatureTo))->count();
    
    }else if($RecivedByFrom != '' && $RecivedByFrom !=''){
    
     $response = Stockregister::with(['getStockTypeId','getProjectId','getNatureId' => function ($query){
    $query->orderBy('id', 'desc')->get();
    } ,])->whereBetween('stockrecby', array($RecivedByFrom,$RecivedByFrom))->orderBy('id','desc')->get();
     $count = Stockregister::whereBetween('stockrecby', array($RecivedByFrom,$RecivedByFrom))->count();
       
    }else{
  
     $response = Stockregister::with(['getStockTypeId','getProjectId','getNatureId' => function ($query){
    $query->orderBy('id', 'desc')->get();
    } ,])->orderBy('id','desc')->get();
     $count = Stockregister::count();
            
    }
    
    
    // SUCCESS
    if (count($response) > 0) {
        $data = $this->renderList($response);
    
        return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Asset Files Found',
            'data' => $data,
            'orderBy' => '',
            'orderType' => '',
            'perPage' => '',
            'pageNo' => '',
            'TotalCount'=>'',
        ]);
    }
    
    // FAIL
    else {
        return app(CommonController::class)->getEmptyListResponse();
    }
}
 
}