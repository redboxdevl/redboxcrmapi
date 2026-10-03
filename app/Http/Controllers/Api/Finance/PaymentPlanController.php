<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\PaymentplanRequest;
use App\Models\Finance\Paymentplan;
use App\Models\Finance\Paymentreciptmode;
use App\Models\Finance\Paymentreciept;

use App\Models\Inventory\Project;
use App\Models\HR\Company;
use App\Models\HR\Employee;
use App\Models\Inventory\Projectinventory;
use App\Models\Finance\Paymentplandata;
use App\Models\AffiliateProgram\AffiliateLeadsModule;
use App\Models\Inventory\Inventorysold;



use App\Models\ListMyLead;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;

class PaymentPlanController extends Controller
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

$agentId = $request->agentId;

if (is_null($agentId)) {

$response = Paymentplan::orderBy($orderBy, $orderType)->paginate($perPage);
$count = Paymentplan::count();
// SUCCESS
if (count($response) > 0) {
$data = $this->renderList($response);

return response()->json([
    'code' => '200',
    'success' => true,
    'message' => 'Payment Plan Found',
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

$customerSoldData = Inventorysold::where(['customer_id'=>$value->CustomerId])->first();

if($value->customerStatus == 'affiliates'){
    $customerData = AffiliateLeadsModule::where(['id'=>$value->CustomerId])->first();
}else{
    $customerData = ListMyLead::where(['id'=>$value->CustomerId])->first();
}

$paymentplandata = Paymentplandata::where(['planId'=>$value->id])->get();

$payplandata=[];

foreach($paymentplandata as $payplandataxs){
    
    
    $payestatement = Paymentreciptmode::where(['statementId'=>$payplandataxs->id])->get();
    
    $idarry = [];
     
     foreach($payestatement as $xs){
         $idarry[]=$xs->recieptId;
     }
     
     $Paymentreciept = Paymentreciept::whereIn('id',$idarry)->get();
     $PaymentrecieptTotal = Paymentreciept::whereIn('id',$idarry)->sum('RecievedAmount');
     
    array_push($payplandata, (object)[
        'id'=>$payplandataxs == '' ? '' : $payplandataxs->id,
        'planId'=>$payplandataxs == '' ? '' : $payplandataxs->planId,
        'accountHead'=>$payplandataxs == '' ? '' : $payplandataxs->accountHead,
        'dueAmount'=>$payplandataxs == '' ? '' : $payplandataxs->dueAmount,
        'dueDate'=>$payplandataxs == '' ? '' : $payplandataxs->dueDate,
        'created_at'=>$payplandataxs == '' ? '' : $payplandataxs->created_at,
        'updated_at'=>$payplandataxs == '' ? '' : $payplandataxs->updated_at,
        'payestatement'=>$payestatement,
        'Paymentreciept'=>$Paymentreciept,
        'PaymentrecieptTotal'=>$PaymentrecieptTotal,
    ]);
    
}

$Project = Project::where(['id'=>$value->ProjectId])->first();




array_push($data, (object) [
"id" => $value->id,
"created_at" => $value->created_at,
"updated_at" => $value->updated_at,
"deleted_at" => $value->deleted_at,
"Size"=>$value->Size,
"PropertyNo"=>$value->PropertyNo,
"BlockName" => $value->BlockName,
"Category" => $value->Category,
"bookingDate"=>date("d M Y",strtotime($value->bookingDate)),
"clientName"=>$customerData == '' ? '' : $customerData->clientName,
"cnic" => $customerSoldData == '' ? '' : $customerSoldData->cnic,
"client_address"=> $customerSoldData == '' ? '' : $customerSoldData->client_address,
"mobileNo"=>$customerData == '' ? '' : $customerData->mobileNo,
"paymentplandata"=>$payplandata,
"project_name" => $Project == '' ? '' : $Project->project_name,

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
public function store(PaymentplanRequest $request)
{
$monthNoOfPayment = $request->MonthlyNoOfPayment;
$quarterlyNoPayment = $request->QuarterlyNoOfPayment;
$HalfYearNoOfPayment = $request->HalfYearNoOfPayment;
$YearlyNoofPayment = $request->YearlyNoofPayment;
$bookingDate = strtotime($request->bookingDate);

$noOfMonth = [];

$MonthlyBooking = [];

$months = date("Y-m-d",strtotime("+3 month", $bookingDate));

$bookingDates = strtotime($months);

array_push($noOfMonth,[
"status" => 'Booking',
"dueAmount"=>$request->Booking,
"dueDate"=>date("Y-m-d",strtotime("+1 month", $bookingDate)),
]);

array_push($noOfMonth,[
"status" => 'Allocation',
"dueAmount"=>$request->Allocation,
"dueDate"=>date("Y-m-d",strtotime("+2 month", $bookingDate)),
]);

array_push($noOfMonth,[
"status" => 'Conformation',
"dueAmount"=>$request->COnfirmation,
"dueDate"=>date("Y-m-d",strtotime("+3 month", $bookingDate)),
]);

$installFInaldate = date("Y-m-d",strtotime("+3 month", $bookingDate));

$dataFinal = [];

for ($i = 0; $i < $monthNoOfPayment; $i++){

$final = $i+1;

$month = date("Y-m-d",strtotime("+$final month", $bookingDates));

if($final % 6 === 0){

array_push($noOfMonth,[
    "status" => 'half yearly '.$final,
    "dueAmount"=>$request->HalfYearAmount,
    "dueDate"=>$month,
]);

array_push($noOfMonth,[
    "status" => 'Installement '.$final,
    "dueAmount"=>$request->MonthlyAmount,
    "dueDate"=>$month,
]);

array_push($noOfMonth,[
    "status" => 'Quarterly '.$final,
    "dueAmount"=>$request->QuarterlyAmount,
    "dueDate"=>$month,
]);

}else if($final % 3 === 0){

array_push($noOfMonth,[
    "status" => 'Quarterly '.$final,
    "dueAmount"=>$request->QuarterlyAmount,
    "dueDate"=>$month,
]);

array_push($noOfMonth,[
    "status" => 'Monthly '.$final,
    "dueAmount"=>$request->MonthlyAmount,
    "dueDate"=>$month,
]);

}else{

array_push($noOfMonth,[
    "status" => 'monthly '.$final,
    "dueAmount"=>$request->MonthlyAmount,
    "dueDate"=>$month,
]);

}

}

$customerIdRes = explode("-",$request->CustomerId);

$response = Paymentplan::create([
'bookingDate' => $request->bookingDate,
'CompanyId' => $request->CompanyId,
'ProjectId' => $request->ProjectId,
'TypeId' => $request->TypeId,
'BlockName' => $request->BlockName,
'PropertyNo' => $request->PropertyNo,
'Size' => $request->Size,
'Category' => $request->Category,
'CustomerId' => $customerIdRes[0],
'ScheduleType' => $request->ScheduleType,
'SchedulePlan' => $request->SchedulePlan,
'Booking' => $request->Booking,
'Allocation' => $request->Allocation,
'COnfirmation' => $request->COnfirmation,
'Demarcation' => $request->Demarcation,
'Possession' => $request->Possession,
'MonthlyAmount' => $request->MonthlyAmount,
'QuarterlyAmount' => $request->QuarterlyAmount,
'HalfYearAmount' => $request->HalfYearAmount,
'YearlyAmount' => $request->YearlyAmount,
'TotalSalesAmount' => $request->TotalSalesAmount,
'DiscountPrice' => $request->DiscountPrice == null ? '0' : $request->DiscountPrice,
'NetSalesPrice' => $request->NetSalesPrice,
'TotalScheduleAmount' => $request->TotalScheduleAmount,
'BalanaceScheduleAmount' => $request->BalanaceScheduleAmount,
'MonthlyNoOfPayment' => $request->MonthlyNoOfPayment,
'QuarterlyNoOfPayment' => $request->QuarterlyNoOfPayment,
'HalfYearNoOfPayment' => $request->HalfYearNoOfPayment,
'YearlyNoofPayment' => $request->YearlyNoofPayment,
'customerStatus' => $customerIdRes[1]
]);

$planId = $response->id;

foreach($noOfMonth as $datax){

$status = $datax['status'];
$dueAmount = $datax['dueAmount'];
$dueDate = $datax['dueDate'];

$Paymentplandata = new Paymentplandata();

$Paymentplandata->planId = $planId;
$Paymentplandata->accountHead = $status;
$Paymentplandata->dueAmount = $dueAmount;
$Paymentplandata->dueDate = $dueDate;

$Paymentplandata->save();

}




return response()->json([
'message' => "Plan Generated successfully!",
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
public function show($id="")
{

$response = Paymentplan::where(['id'=>$id])->get();

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
public function update(PaymentrecieptRequest $request, Paymentreciept $paymentreciept,$id="")
{

// $checkFiles = Paymentreciept::findOrFail($id);

// SecurityFiles::where(['id'=>$id])->update([
//     'docTypeId' =>  $docTypeIds,
//     'proTypeId' =>  $ProPType,
//     'docRef' =>  $request->docRef,
//     'docStatus' =>  $request->docStatus,
//     'releaseName' =>  $request->releaseName,
//     'releaseDate' =>  $request->releaseDate,
//     'Attachement' =>  $image_name,
//     'status' =>  $request->status,
//     'uploadedBy' => $request->uploadedBy,
//     'docNature' => $request->docNature,
//     'ownerShipTitle' => $request->ownerShipTitle,
//     'docDesc' => $request->docDesc,
//     'issuingauthorityId'=>$IssUingPType,
//     'CompaniesId' => $request->CompaniesId,
//     'PurposeId' => $PurposeTypeId,
// ]);

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
public function destroy(PaymentrecieptRequest $request, Paymentreciept $paymentreciept)
{
$paymentreciept->delete($request->all());

return response()->json([
'message' => "Data deleted successfully!",
'code'=>'200'
], 200);
}




}