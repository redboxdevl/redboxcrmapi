<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\PaymentvoucherRequest;
use App\Models\Finance\Paymentvoucher;
use App\Models\Finance\Voucherentry;
use App\Models\Finance\Banklist;
use App\Models\Finance\Glcode;
use App\Models\Finance\Dimensioncode;
use App\Models\HR\Company;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;
use DB;

class PaymentVoucherController extends Controller
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

$VoucherDate = $request->VoucherDate ?? "";
$TransactionDate = $request->TransactionDate ?? "";
$CompanyId = $request->CompanyId ?? "";
$Status = $request->Status ?? "";
$Amount = $request->Amount ?? "";
$PayTo = $request->PayTo ?? "";
$PaymentType = $request->PaymentType ?? "";
$company_id = $request->company_id ?? "";

if (is_null($agentId)) {

if($TransactionDate !=''){
    
    if($CompanyId !='' && $Status !='' && $TransactionDate !=''){
        $response = Paymentvoucher::where(['companyId'=>$CompanyId,'status'=>$Status,'transactionDate'=>$TransactionDate,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
        $count = Paymentvoucher::where(['companyId'=>$CompanyId,'status'=>$Status,'transactionDate'=>$TransactionDate,'company_id'=>$company_id])->count();
    }else{
        $response = Paymentvoucher::where(['transactionDate'=>$TransactionDate,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
        $count = Paymentvoucher::where(['transactionDate'=>$TransactionDate,'company_id'=>$company_id])->count();
        
    }
    
}else if($PayTo !=''){
    
    $response = Paymentvoucher::where(['payTo'=>$PayTo,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Paymentvoucher::where(['payTo'=>$PayTo,'company_id'=>$company_id])->count();
    
}else if($Amount !=''){
    
    $response = Paymentvoucher::join('voucherEntry', 'voucherEntry.voucherId', '=', 'paymentVoucher.id')->where('voucherEntry.voucherAmt', 'LIKE', '%'.$Amount.'%')->where(['company_id'=>$company_id])->select('paymentVoucher.*')->paginate($perPage);
    $count = Paymentvoucher::where(['payTo'=>$PayTo])->count();
    
}else if($CompanyId !=''){
    
    if($CompanyId !='' && $Status !=''){
        $response = Paymentvoucher::where(['companyId'=>$CompanyId,'status'=>$Status,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
        $count = Paymentvoucher::where(['companyId'=>$CompanyId,'status'=>$Status,'company_id'=>$company_id])->count();
    }else{
        $response = Paymentvoucher::where(['companyId'=>$CompanyId,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Paymentvoucher::where(['companyId'=>$CompanyId,'company_id'=>$company_id])->count();
    }
    
    
}else if($Status !=''){
    $response = Paymentvoucher::where(['status'=>$Status,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Paymentvoucher::where(['status'=>$Status,'company_id'=>$company_id])->count();
}else if($PaymentType !=''){
    $response = Paymentvoucher::where(['paymentType'=>$PaymentType,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Paymentvoucher::where(['paymentType'=>$PaymentType,'company_id'=>$company_id])->count();
}else{
    $response = Paymentvoucher::where(['company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Paymentvoucher::where(['company_id'=>$company_id])->count();
}


// SUCCESS
if (count($response) > 0) {
    $data = $this->renderList($response);

    return response()->json([
        'code' => '200',
        'success' => true,
        'message' => 'Payment Voucher List Found',
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
    
if($TransactionDate !=''){
    
    $response = Paymentvoucher::where(['transactionDate'=>$TransactionDate,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Paymentvoucher::where(['transactionDate'=>$TransactionDate,'company_id'=>$company_id])->count();
    
}else if($PayTo !=''){
    
    $response = Paymentvoucher::where(['payTo'=>$PayTo,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Paymentvoucher::where(['payTo'=>$PayTo,'company_id'=>$company_id])->count();
    
}else if($CompanyId !=''){
    
    $response = Paymentvoucher::where(['companyId'=>$CompanyId,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Paymentvoucher::where(['companyId'=>$CompanyId,'company_id'=>$company_id])->count();
    
}else if($Status !=''){
     $response = Paymentvoucher::where(['status'=>$Status,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
    $count = Paymentvoucher::where(['status'=>$Status,'company_id'=>$company_id])->count();
}else{
    
    $response = Paymentvoucher::where(['company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
    
$count = Paymentvoucher::where(['company_id'=>$company_id])->count();
}
    


// SUCCESS
if (count($response) > 0) {
    $data = $this->renderList($response);

    return response()->json([
        'code' => '200',
        'success' => true,
        'message' => 'Payment Voucher List Found',
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

$Voucherentry = Voucherentry::where(['voucherId'=>$value->id])->get();

$Company = Company::where(['id'=>$value->companyId])->first();

$Glcode = Glcode::where(['id'=>$value->Glid])->first();
$Dimensioncode = Dimensioncode::where(['id'=>$value->dimensionId])->first();

$data2 = [];
$sumAmount = 0;
foreach($Voucherentry as $entryData){
    
    $Banklist = Banklist::where(['id'=>$entryData->bankId])->first();
    
    array_push($data2, (object) [
        "voucherEntryId" => $entryData->id,
        "PayDate" => $entryData->voucherEntryDate,
        "PayDateCoverted" => date("d-m-Y",strtotime($entryData->voucherEntryDate)),
        "payMethod" => $entryData->paymentMethod,
        "CheqPoNo" => $entryData->chequePoNoRefTitle,
        "BankName" => $entryData->bankId,
        "BankNameList" => $Banklist == '' || $Banklist == null ? '' : $Banklist->bankname,
        "PayAmt" => $entryData->voucherAmt,
        "PayAmtConverted" => number_format($entryData->voucherAmt),
    ]);
    
    $sumAmount+=$entryData->voucherAmt;
    
}

array_push($data, (object) [
    "id" => $value->id,
    "created_at" => $value->created_at,
    "updated_at" => $value->updated_at,
    "deleted_at" => $value->deleted_at,
    "voucherNo" => $value->voucherNo,
    "companyId" => $value->companyId,
    "Glid" => $Glcode == null || $Glcode == '' ? '' : $Glcode->id,
    "GlCode" => $Glcode == null || $Glcode == '' ? '' : $Glcode->code,
    "glDesc" => $Glcode == null || $Glcode == '' ? '' : $Glcode->glDesc,
    "dimensionId" => $Dimensioncode == null || $Dimensioncode == '' ? '' : $Dimensioncode->id,
    "dimensionCode" => $Dimensioncode == null || $Dimensioncode == '' ? '' : $Dimensioncode->code,
    "dimensionDesc" => $Dimensioncode == null || $Dimensioncode == '' ? '' : $Dimensioncode->dimensionDesc,
    "transactionDate" => date("d-m-Y",strtotime($value->transactionDate)),
    "transactionDates" => $value->transactionDate,
    "payTo" => $value->payTo,
    "paymentType" => $value->paymentType,
    "PayDesc" => $value->PayDesc,
    "voucherDate" => date("d-m-Y",strtotime($value->created_at)),
    "status" => $value->status,
    "Voucherentry"=>json_encode($data2),
    "company_name" =>$Company == '' || $Company == null ? '' : $Company->company_name,
    "address" =>$Company == '' || $Company == null ? '' : $Company->address,
    "contact_number" =>$Company == '' || $Company == null ? '' : $Company->contact_number,
    "sumAmount"=>number_format($sumAmount),
    
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
public function store(PaymentvoucherRequest $request)
{

$companInfo = Company::where(['id'=>$request->companyId])->first();

$companyShot = $companInfo == '' || $companInfo == null ? '' : $companInfo->company_abbr;
$getYear = date("Y");
$getMonth = date("m");

$getmonthVoucher = Paymentvoucher::where(['companyId'=>$request->companyId])->where(DB::raw('MONTH(created_at)'),$getMonth)->get();
// $monthCountX = array_count_values($getmonthVoucher);

$getyearVoucher = Paymentvoucher::where(['companyId'=>$request->companyId])->where(DB::raw('YEAR(created_at)'),$getYear)->get();

if(empty($getmonthVoucher)){
$monthCOunt = 1;
}else{
$monthCOunt = count($getmonthVoucher) + 1;
}


if(empty($getyearVoucher)){
$yearCOunt = 1;
}else{
$yearCOunt = count($getyearVoucher) + 1;
}

$monthCount = str_pad($monthCOunt,3,'0',STR_PAD_LEFT);
$yearCount = str_pad($yearCOunt,3,'0',STR_PAD_LEFT);

$voucherNOData = $companyShot."/".$getYear."/".$getMonth."/".$monthCount."/".$yearCount;

$response = Paymentvoucher::create([
'voucherNo' =>  $voucherNOData,
'companyId' =>  $request->companyId,
'Glid' =>  $request->GLDescription == '' ? '0' : $request->GLDescription,
'dimensionId' =>  $request->DimensionDesc == '' ? '0' : $request->DimensionDesc,
'transactionDate' =>  $request->transactionDate,
'payTo' =>  $request->payTo,
'paymentType' =>  $request->paymentType,
'PayDesc' =>  $request->PayDesc,
'company_id'=>$request->company_id,
]);

$paymentVoucherId = $response->id;


$paymentMethodJson = json_decode($request->inputList,JSON_UNESCAPED_SLASHES);

$dataPay = [];

foreach($paymentMethodJson as $myPayment){

$PayDate = $myPayment['PayDate'];
$BankName = $myPayment['BankName'];
$CheqPoNo = $myPayment['CheqPoNo'];
$PayAmt = $myPayment['PayAmt'];
$payMethod = $myPayment['payMethod'];

Voucherentry::create([
    'voucherId' =>  $paymentVoucherId,
    'paymentMethod' =>  $payMethod,
    'chequePoNoRefTitle' => $CheqPoNo == '' || $CheqPoNo == null ? '0' : $CheqPoNo,
    'voucherEntryDate' => $PayDate,
    'bankId' => $BankName == '' || $BankName == null ? '0' : $BankName,
    'voucherAmt' => $PayAmt,
]);


}


return response()->json([
'message' => "Data saved successfully!",
'data' =>$response,
'code'=>'200'
], 200);


}

/**
* Display the specified resource.
*
* @param  \App\Models\Employee  $employee
* @return \Illuminate\Http\Response
*/
public function show(Paymentvoucher $paymentvoucher)
{
$response = Paymentvoucher::where(['id'=>$paymentvoucher->id])->get();

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
public function update(PaymentvoucherRequest $request, Paymentvoucher $paymentvoucher,$id="")
{
$paymentvoucher->update([
'companyId' =>  $request->companyId,
'Glid' =>  $request->GLDescription,
'dimensionId' =>  $request->DimensionDesc,
'transactionDate' =>  $request->transactionDate,
'payTo' =>  $request->payTo,
'paymentType' =>  $request->paymentType,
'PayDesc' =>  $request->PayDesc,
'status' =>  $request->status,
'company_id'=>$request->company_id,
]);


$paymentMethodJson = json_decode($request->inputList,JSON_UNESCAPED_SLASHES);

$dataPay = [];

foreach($paymentMethodJson as $myPayment){
    
$voucherEntryId = $myPayment['voucherEntryId'];

$PayDate = $myPayment['PayDate'];
$BankName = $myPayment['BankName'];
$CheqPoNo = $myPayment['CheqPoNo'];
$PayAmt = $myPayment['PayAmt'];
$payMethod = $myPayment['payMethod'];

if($voucherEntryId == '' || $voucherEntryId == null){
  
    Voucherentry::create([
        'voucherId' =>   $paymentvoucher->id,
        'paymentMethod' =>  $payMethod,
        'chequePoNoRefTitle' => $CheqPoNo == '' || $CheqPoNo == null ? '0' : $CheqPoNo,
        'voucherEntryDate' => $PayDate,
        'bankId' => $BankName == '' || $BankName == null ? '0' : $BankName,
        'voucherAmt' => $PayAmt,
    ]);
  
    
}else{
   
    Voucherentry::where("id",$voucherEntryId)->update([
        'paymentMethod' =>  $payMethod,
        'chequePoNoRefTitle' => $CheqPoNo,
        'voucherEntryDate' => $PayDate,
        'bankId' => $BankName,
        'voucherAmt' => $PayAmt,    
    ]);

}





}

return response()->json([
'message' => "Data updated successfully!",
'data' => $paymentvoucher,
'code'=>'200'
], 200);
}

/**
* Remove the specified resource from storage.
*
* @param  \App\Models\Employee  $employee
* @return \Illuminate\Http\Response
*/
public function destroy(PaymentvoucherRequest $request, Paymentvoucher $paymentvoucher)
{
$paymentvoucher->delete($request->all());

return response()->json([
'message' => "Data deleted successfully!",
'data' => $paymentvoucher,
'code'=>'200'
], 200);
}


public function PaymentVoucherFilters (Request $request){
    
    
    $DateOfTransactionFrom = $request->DateOfTransactionFrom ?? "";
    $DateOfTransactionTo = $request->DateOfTransactionTo ?? "";
    
    $VoucherDateFrom = $request->VoucherDateFrom ?? "";
    $VoucherDateTo = $request->VoucherDateTo ?? "";
    
    $AmountFrom = $request->AmountFrom ?? "";
    $AmountTo = $request->AmountTo ?? "";
    
    $PaymentTypeFrom = $request->PaymentTypeFrom ?? "";
    $PaymentTypeTo = $request->PaymentTypeTo ?? "";
    
    $CompanyFrom = $request->CompanyFrom ?? "";
    $CompanyTo = $request->CompanyTo ?? "";
    
    $PayToFrom = $request->PayToFrom ?? "";
    $PayToTo = $request->PayToTo ?? "";
    
    if($DateOfTransactionFrom != '' && $DateOfTransactionTo !='' && $VoucherDateFrom == '' && $VoucherDateTo == '' && $AmountFrom == '' && $AmountTo == '' && $PaymentTypeFrom == '' && $PaymentTypeTo == '' && $CompanyFrom == '' && $CompanyTo == '' && $PayToFrom == '' && $PayToTo == ''){
   
        $response = Paymentvoucher::whereBetween('transactionDate', array($DateOfTransactionFrom,$DateOfTransactionTo))->get();
        // $count = Paymentvoucher::whereBetween('transactionDate', array($DateOfTransactionFrom,$DateOfTransactionTo))->count();
        
         $count = Paymentvoucher::join('voucherEntry', 'paymentVoucher.id', '=', 'voucherEntry.voucherId')->whereBetween('transactionDate', array($DateOfTransactionFrom,$DateOfTransactionTo))->select(DB::raw("SUM(voucherEntry.voucherAmt) as AmounTotal"))
                ->first();
        
    }else if($DateOfTransactionFrom == '' && $DateOfTransactionTo =='' && $VoucherDateFrom != '' && $VoucherDateTo != '' && $AmountFrom == '' && $AmountTo == '' && $PaymentTypeFrom == '' && $PaymentTypeTo == '' && $CompanyFrom == '' && $CompanyTo == '' && $PayToFrom == '' && $PayToTo == ''){
        
        $response = Paymentvoucher::whereBetween(DB::raw('DATE(created_at)'), array($VoucherDateFrom,$VoucherDateTo))->get();
        // $count = Paymentvoucher::whereBetween(DB::raw('DATE(created_at)'), array($VoucherDateFrom,$VoucherDateTo))->count();
        
        $count = Paymentvoucher::join('voucherEntry', 'paymentVoucher.id', '=', 'voucherEntry.voucherId')->whereBetween(DB::raw('DATE(created_at)'), array($VoucherDateFrom,$VoucherDateTo))->select(DB::raw("SUM(voucherEntry.voucherAmt) as AmounTotal"))
                ->first();
        
    }else if($DateOfTransactionFrom == '' && $DateOfTransactionTo =='' && $VoucherDateFrom == '' && $VoucherDateTo == '' && $AmountFrom != '' && $AmountTo != '' && $PaymentTypeFrom == '' && $PaymentTypeTo == '' && $CompanyFrom == '' && $CompanyTo == '' && $PayToFrom == '' && $PayToTo == ''){
        
        // $response = Paymentvoucher::whereBetween('transactionDate', array($AmountFrom,$AmountTo))->get();
        // $count = Paymentvoucher::whereBetween('transactionDate', array($AmountFrom,$AmountTo))->count();
        
    }else if($DateOfTransactionFrom == '' && $DateOfTransactionTo =='' && $VoucherDateFrom == '' && $VoucherDateTo == '' && $AmountFrom == '' && $AmountTo == '' && $PaymentTypeFrom != '' && $PaymentTypeTo != '' && $CompanyFrom == '' && $CompanyTo == '' && $PayToFrom == '' && $PayToTo == ''){
        
        $response = Paymentvoucher::whereBetween('paymentType', array($PaymentTypeFrom,$PaymentTypeTo))->get();
        // $count = Paymentvoucher::whereBetween('paymentType', array($PaymentTypeFrom,$PaymentTypeTo))->count();
        
        $count = Paymentvoucher::join('voucherEntry', 'paymentVoucher.id', '=', 'voucherEntry.voucherId')->whereBetween('paymentType', array($PaymentTypeFrom,$PaymentTypeTo))->select(DB::raw("SUM(voucherEntry.voucherAmt) as AmounTotal"))
                ->first();
        
    }else if($DateOfTransactionFrom == '' && $DateOfTransactionTo =='' && $VoucherDateFrom == '' && $VoucherDateTo == '' && $AmountFrom == '' && $AmountTo == '' && $PaymentTypeFrom == '' && $PaymentTypeTo == '' && $CompanyFrom != '' && $CompanyTo != '' && $PayToFrom == '' && $PayToTo == ''){
        
        $response = Paymentvoucher::whereBetween('companyId', array($CompanyFrom,$CompanyTo))->get();
        // $count = Paymentvoucher::whereBetween('companyId', array($CompanyFrom,$CompanyTo))->count();
        
        $count = Paymentvoucher::join('voucherEntry', 'paymentVoucher.id', '=', 'voucherEntry.voucherId')->whereBetween('companyId', array($CompanyFrom,$CompanyTo))->select(DB::raw("SUM(voucherEntry.voucherAmt) as AmounTotal"))
                ->first();
        
    }else if($DateOfTransactionFrom != '' && $DateOfTransactionTo !='' && $VoucherDateFrom != '' && $VoucherDateTo != '' && $AmountFrom == '' && $AmountTo == '' && $PaymentTypeFrom == '' && $PaymentTypeTo == '' && $CompanyFrom == '' && $CompanyTo == '' && $PayToFrom == '' && $PayToTo == ''){
        
        $response = Paymentvoucher::whereBetween('transactionDate', array($DateOfTransactionFrom,$DateOfTransactionTo))->whereBetween(DB::raw('DATE(created_at)'), array($VoucherDateFrom,$VoucherDateTo))->get();
        // $count = Paymentvoucher::whereBetween('transactionDate', array($DateOfTransactionFrom,$DateOfTransactionTo))->whereBetween(DB::raw('DATE(created_at)'), array($VoucherDateFrom,$VoucherDateTo))->count();
        
        $count = Paymentvoucher::join('voucherEntry', 'paymentVoucher.id', '=', 'voucherEntry.voucherId')->whereBetween('transactionDate', array($DateOfTransactionFrom,$DateOfTransactionTo))->whereBetween(DB::raw('DATE(created_at)'), array($VoucherDateFrom,$VoucherDateTo))->select(DB::raw("SUM(voucherEntry.voucherAmt) as AmounTotal"))
                ->first();
        
    }else if($DateOfTransactionFrom == '' && $DateOfTransactionTo =='' && $VoucherDateFrom != '' && $VoucherDateTo != '' && $AmountFrom == '' && $AmountTo == '' && $PaymentTypeFrom != '' && $PaymentTypeTo != '' && $CompanyFrom == '' && $CompanyTo == '' && $PayToFrom == '' && $PayToTo == ''){
        
        $response = Paymentvoucher::whereBetween(DB::raw('DATE(created_at)'), array($VoucherDateFrom,$VoucherDateTo))->whereBetween('paymentType', array($PaymentTypeFrom,$PaymentTypeTo))->get();
        
        // $count = Paymentvoucher::whereBetween(DB::raw('DATE(created_at)'), array($VoucherDateFrom,$VoucherDateTo))->whereBetween('paymentType', array($PaymentTypeFrom,$PaymentTypeTo))->count();
        
        $count = Paymentvoucher::join('voucherEntry', 'paymentVoucher.id', '=', 'voucherEntry.voucherId')->whereBetween(DB::raw('DATE(created_at)'), array($VoucherDateFrom,$VoucherDateTo))->whereBetween('paymentType', array($PaymentTypeFrom,$PaymentTypeTo))->select(DB::raw("SUM(voucherEntry.voucherAmt) as AmounTotal"))
                ->first();
        
    }else if($DateOfTransactionFrom != '' && $DateOfTransactionTo !='' && $VoucherDateFrom == '' && $VoucherDateTo == '' && $AmountFrom == '' && $AmountTo == '' && $PaymentTypeFrom != '' && $PaymentTypeTo != '' && $CompanyFrom == '' && $CompanyTo == ''){
        
        $response = Paymentvoucher::whereBetween('transactionDate', array($DateOfTransactionFrom,$DateOfTransactionTo))->whereBetween('paymentType', array($PaymentTypeFrom,$PaymentTypeTo))->get();
        
        // $count = Paymentvoucher::whereBetween('transactionDate', array($DateOfTransactionFrom,$DateOfTransactionTo))->whereBetween('paymentType', array($PaymentTypeFrom,$PaymentTypeTo))->count();
        
        $count = Paymentvoucher::join('voucherEntry', 'paymentVoucher.id', '=', 'voucherEntry.voucherId')->whereBetween('transactionDate', array($DateOfTransactionFrom,$DateOfTransactionTo))->whereBetween('paymentType', array($PaymentTypeFrom,$PaymentTypeTo))->select(DB::raw("SUM(voucherEntry.voucherAmt) as AmounTotal"))
                ->first();
        
    }else if($DateOfTransactionFrom != '' && $DateOfTransactionTo !='' && $VoucherDateFrom == '' && $VoucherDateTo == '' && $AmountFrom == '' && $AmountTo == '' && $PaymentTypeFrom != '' && $PaymentTypeTo != '' && $CompanyFrom != '' && $CompanyTo != '' && $PayToFrom == '' && $PayToTo == ''){
        
        $response = Paymentvoucher::whereBetween('transactionDate', array($DateOfTransactionFrom,$DateOfTransactionTo))->whereBetween('paymentType', array($PaymentTypeFrom,$PaymentTypeTo))->whereBetween('companyId', array($CompanyFrom,$CompanyTo))->get();
        
        // $count = Paymentvoucher::whereBetween('transactionDate', array($DateOfTransactionFrom,$DateOfTransactionTo))->whereBetween('paymentType', array($PaymentTypeFrom,$PaymentTypeTo))->whereBetween('companyId', array($CompanyFrom,$CompanyTo))->count();
        
        $count = Paymentvoucher::join('voucherEntry', 'paymentVoucher.id', '=', 'voucherEntry.voucherId')->whereBetween('transactionDate', array($DateOfTransactionFrom,$DateOfTransactionTo))->whereBetween('paymentType', array($PaymentTypeFrom,$PaymentTypeTo))->whereBetween('companyId', array($CompanyFrom,$CompanyTo))->select(DB::raw("SUM(voucherEntry.voucherAmt) as AmounTotal"))
                ->first();
        
    }else if($DateOfTransactionFrom == '' && $DateOfTransactionTo =='' && $VoucherDateFrom == '' && $VoucherDateTo == '' && $AmountFrom == '' && $AmountTo == '' && $PaymentTypeFrom != '' && $PaymentTypeTo != '' && $CompanyFrom != '' && $CompanyTo != '' && $PayToFrom == '' && $PayToTo == ''){
   
        $response = Paymentvoucher::whereBetween('paymentType', array($PaymentTypeFrom,$PaymentTypeTo))->whereBetween('companyId', array($CompanyFrom,$CompanyTo))->get();
        
        // $count = Paymentvoucher::whereBetween('paymentType', array($PaymentTypeFrom,$PaymentTypeTo))->whereBetween('companyId', array($CompanyFrom,$CompanyTo))->count();
        
         $count = Paymentvoucher::join('voucherEntry', 'paymentVoucher.id', '=', 'voucherEntry.voucherId')->whereBetween('paymentType', array($PaymentTypeFrom,$PaymentTypeTo))->whereBetween('companyId', array($CompanyFrom,$CompanyTo))->select(DB::raw("SUM(voucherEntry.voucherAmt) as AmounTotal"))
                ->first();
        
    }else if($DateOfTransactionFrom == '' && $DateOfTransactionTo =='' && $VoucherDateFrom == '' && $VoucherDateTo == '' && $AmountFrom == '' && $AmountTo == '' && $PaymentTypeFrom == '' && $PaymentTypeTo == '' && $CompanyFrom == '' && $CompanyTo == '' && $PayToFrom != '' && $PayToTo != ''){
   
        $response = Paymentvoucher::whereBetween('payTo', array($PayToFrom,$PayToTo))->get();
        $count = Paymentvoucher::whereBetween('payTo', array($PayToFrom,$PayToTo))->count();
        
        $count = Paymentvoucher::join('voucherEntry', 'paymentVoucher.id', '=', 'voucherEntry.voucherId')->whereBetween('payTo', array($PayToFrom,$PayToTo))->select(DB::raw("SUM(voucherEntry.voucherAmt) as AmounTotal"))
                ->first(); 
        
    }else if($DateOfTransactionFrom == '' && $DateOfTransactionTo =='' && $VoucherDateFrom == '' && $VoucherDateTo == '' && $AmountFrom == '' && $AmountTo == '' && $PaymentTypeFrom == '' && $PaymentTypeTo == '' && $CompanyFrom != '' && $CompanyTo != '' && $PayToFrom != '' && $PayToTo != ''){
   
        $response = Paymentvoucher::whereBetween('payTo', array($PayToFrom,$PayToTo))->whereBetween('companyId', array($CompanyFrom,$CompanyTo))->get();
        // $count = Paymentvoucher::whereBetween('payTo', array($PayToFrom,$PayToTo))->whereBetween('companyId', array($CompanyFrom,$CompanyTo))->count();
        
         $count = Paymentvoucher::join('voucherEntry', 'paymentVoucher.id', '=', 'voucherEntry.voucherId')->whereBetween('payTo', array($PayToFrom,$PayToTo))->whereBetween('companyId', array($CompanyFrom,$CompanyTo))->select(DB::raw("SUM(voucherEntry.voucherAmt) as AmounTotal"))
                ->first();
        
    }else{
        $response = Paymentvoucher::all();
        $count = Paymentvoucher::count();
    }
    
   
         
        
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);
                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Payment Voucher Filters Found',
                    'data' => $data,
                    'orderBy' => '',
                    'orderType' => '',
                    'perPage' => '',
                    'pageNo' => '',
                    'TotalCount'=>$count,
                    'TotalCountLogic'=>$count == '' || $count == null ? 0 : number_format($count->AmounTotal),
                ]);
            }

            // FAIL
            else {
                return app(CommonController::class)->getEmptyListResponse();
            }
    
}




}