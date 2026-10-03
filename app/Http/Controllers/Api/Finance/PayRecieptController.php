<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\PayRecieptRequest;
use App\Models\Finance\Payreciepts;
use App\Models\Finance\Payrecieptlist;
use App\Models\Finance\Banklist;
use App\Models\Finance\BankListData;

use App\Models\HR\Company;
use App\Models\HR\Employee;
use App\Models\Inventory\Project;
use App\Models\propertyType;
use App\Models\Inventory\Projectasset;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;
use DB;

class PayRecieptController extends Controller
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
        
        $ProjectId = $request->ProjectId;
        $CompanyId = $request->CompanyId;
        $PropertyNo = $request->PropertyNo;
        $Reference = $request->Reference;
        $PropertyType = $request->PropertyType;
        $company_id = $request->company_id ?? "";
        
        
        if(!empty($ProjectId) && empty($CompanyId) && empty($PropertyNo) && empty($Reference) && empty($PropertyType)){
            $response = Payreciepts::where(['ProjectId'=>$ProjectId,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Payreciepts::where(['ProjectId'=>$ProjectId,'company_id'=>$company_id])->count();
        }else if(empty($ProjectId) && !empty($CompanyId) && empty($PropertyNo) && empty($Reference) && empty($PropertyType)){
            $response = Payreciepts::where(['CompanyId'=>$CompanyId,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Payreciepts::where(['CompanyId'=>$CompanyId,'company_id'=>$company_id])->count();
        }else if(empty($ProjectId) && empty($CompanyId) && !empty($PropertyNo) && empty($Reference) && empty($PropertyType)){
            $response = Payreciepts::where(['ProNo'=>$PropertyNo,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Payreciepts::where(['ProNo'=>$PropertyNo,'company_id'=>$company_id])->count();
        }else if(empty($ProjectId) && empty($CompanyId) && empty($PropertyNo) && !empty($Reference) && empty($PropertyType)){
            $response = Payreciepts::where(['Reference'=>$Reference,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Payreciepts::where(['Reference'=>$Reference,'company_id'=>$company_id])->count();
        }else if(empty($ProjectId) && empty($CompanyId) && empty($PropertyNo) && empty($Reference) && !empty($PropertyType)){
            $response = Payreciepts::where(['PropTypeId'=>$PropertyType,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Payreciepts::where(['PropTypeId'=>$PropertyType,'company_id'=>$company_id])->count();
        }else{
            $response = Payreciepts::where(['company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Payreciepts::where(['company_id'=>$company_id])->count();
        }
        
        
        
        
        
        // SUCCESS
        if (count($response) > 0) {
        $data = $this->renderList($response);
        
        return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Payment Reciepts List Found',
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
            
            $PayRecieptsentry = Payrecieptlist::where(['payrecieptId'=>$value->id])->get();
            
            $PayRecieptTodaysentry = Payrecieptlist::where(['payrecieptId'=>$value->id])->latest()->limit(1)->get();
            
            $Project = Project::where(['id'=>$value->ProjectId])->first();
            
            $propertyType = propertyType::where(['id'=>$value->PropTypeId])->first();
            $Companys = Company::where(['id'=>$value->CompanyId])->first();
            
            if($Project == null || $Project == ''){
                $Projectasset="";
            }else{
                 $Projectasset = Projectasset::where(['project_id'=>$Project->id])->first();
            }
           
            
            $Employee = Employee::where(['id'=>$value->PostedBy])->first();
            
            $data2 = [];
            $data3 = [];
            $sumAmount = 0;
            $recivedAmt = 0;
            $totalSumAmount = 0;
            $countx = 0;
            $recivedAmtToday=0;
            $DueAm = 0;
            foreach($PayRecieptsentry as $entryData){
                
                
                $countx++;
                
                $Banklist = BankListData::where(['id'=>$entryData->BankName])->first();
                $Company = Company::where(['id'=>$entryData->ReferenceTitle])->first();
                
                $Banklistings = Banklist::where(['id'=>$entryData->TransferTo])->first();
                
                
                if($entryData->AmountPay == '' || $entryData->AmountPay == null){
                    $recivedAmt  += 0;
                }else{
                    $recivedAmt  += str_replace(',', '',$entryData->AmountPay == null || $entryData->AmountPay == '' ? 0 : $entryData->AmountPay);
                }
                
                
                $DueAm = str_replace(',', '',$value->SaleAmount == null || $value->SaleAmount == '' ? 0 : $value->SaleAmount) - str_replace(',', '',$entryData->AmountPay == null || $entryData->AmountPay == '' ? 0 : $entryData->AmountPay);
                
                $DueAm++;
                
                array_push($data2, (object) [
                    "payRecieptEntryId" => $entryData->id,
                    "PayDate" => date("Y-m-d",strtotime($entryData->payDate)),
                    "PayDates" => date("d-m-Y",strtotime($entryData->payDate)),
                    "payMethod" => $entryData->paymentMethod,
                    "CheqPoNo" => $entryData->ChequePoReference,
                    "BankName" => $Banklist == '' || $Banklist == null ? '' : $entryData->BankName,
                    "BankTitle" => $Banklist == '' || $Banklist == null ? '' : $Banklist->banktitle,
                    "ReferenceId" => $entryData->ReferenceTitle,
                    "referenceName" => $Company == '' || $Company == null ? '' : $Company->company_name,
                    "AuthorizedBy" => $Company == '' || $Company == null ? '' : $Company->ownerTitle,
                    "TransferTo" => $entryData->TransferTo,
                    "PayAmt" => $entryData->AmountPay == null || $entryData->AmountPay == '' ? 0 : $entryData->AmountPay,
                    "ProjectTitle" => $Project == '' || $Project == null ? '' : $Project->project_name,
                    "Transferbankname"=>$Banklistings == '' || $Banklistings == null ? '' : $Banklistings->accountno,
                    "DueAmount" =>  number_format(str_replace(',', '',$value->SaleAmount) - str_replace(',', '',$value->Discount))."asd".$countx,
                    "Payamount" => number_format(str_replace(',', '',$entryData->AmountPay == null || $entryData->AmountPay == '' ? 0 : $entryData->AmountPay)),
                    "Payamount2" => str_replace(',', '',$entryData->AmountPay == null || $entryData->AmountPay == '' ? 0 : $entryData->AmountPay),
                    "RemainingAmount"=> str_replace(',', '',intval($value->SaleAmount)) - intval($entryData->AmountPay),
                    // "RemainingAmount"=> $entryData->AmountPay,
                    'finalTotalAmt'=>$DueAm,
                   
                ]);
                
                $sumAmount+= str_replace(',', '',$entryData->AmountPay == null || $entryData->AmountPay == '' ? 0 : $entryData->AmountPay);
                
            }
            
            
            foreach($PayRecieptTodaysentry as $entryData){
                
                $Banklist = BankListData::where(['id'=>$entryData->BankName])->first();
                $Company = Company::where(['id'=>$entryData->ReferenceTitle])->first();
                
                $Banklisting = BankListData::where(['id'=>$entryData->TransferTo])->first();
                
                $Banklistings = Banklist::where(['id'=>$entryData->TransferTo])->first();
                
                $recivedAmtToday  += str_replace(',', '',$entryData->AmountPay == null || $entryData->AmountPay == '' ? 0 : $entryData->AmountPay);
                
                array_push($data3, (object) [
                    "payRecieptEntryId" => $entryData->id,
                    "PayDate" => date("Y-m-d",strtotime($entryData->payDate)),
                    "PayDates" => date("d-m-Y",strtotime($entryData->payDate)),
                    "payMethod" => $entryData->paymentMethod,
                    "CheqPoNo" => $entryData->ChequePoReference,
                    "BankName" => $entryData->BankName,
                    "BankTitle" => $Banklist == '' || $Banklist == null ? '' : $Banklist->banktitle,
                    "ReferenceId" => $entryData->ReferenceTitle,
                    "referenceName" => $Company == '' || $Company == null ? '' : $Company->company_name,
                    "AuthorizedBy" => $Company == '' || $Company == null ? '' : $Company->ownerTitle,
                    "TransferTo" => $entryData->TransferTo,
                    "Transferbankname"=>$Banklistings == '' || $Banklistings == null ? '' : $Banklistings->accountno,
                    "PayAmt" => number_format(str_replace(',', '',$entryData->AmountPay == null || $entryData->AmountPay == '' ? 0 : $entryData->AmountPay)),
                    "Payamount" => $entryData->AmountPay == null || $entryData->AmountPay == '' ? 0 : $entryData->AmountPay,
                ]);
                
                $sumAmount+= str_replace(',', '',$entryData->AmountPay == null || $entryData->AmountPay == '' ? 0 : $entryData->AmountPay);
                
            }
            
            $minusxs = str_replace(',', '',$value->SaleAmount) - str_replace(',', '',$value->Discount);
            
            $RemainingAmount = str_replace(',', '',$value->SaleAmount) - $recivedAmt;
         
            
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "RecieptNo" => $value->RecieptNo,
                "dateOfReciept" => $value->dateOfReciept,
                "dateOfRecieptCov" => date("d-m-Y",strtotime($value->dateOfReciept)),
                "ProjectId" => $value->ProjectId,
                "ProjectTitle" => $Project == '' || $Project == null ? '' : $Project->project_name,
                "ProjectLogo" => $Projectasset == '' || $Projectasset == null ? '' : "public/storage/projectLogo/".$Projectasset->asset_name,
                "CompanyId" => $value->CompanyId,
                "PropTypeId" => $value->PropTypeId,
                "type_name" => $propertyType == '' || $propertyType == null ? '' : $propertyType->type_name,
                "ProNo" => $value->ProNo,
                "BlockNo" => $value->BlockNo,
                "Size" => $value->Size,
                "CustomerName" => $value->CustomerName,
                "Cnic" => $value->Cnic,
                "ContactNo" => $value->ContactNo,
                "Reference" => $value->Reference,
                "ReferenceContactNo" => $value->ReferenceContactNo,
                "SaleAmount" => number_format(str_replace(',', '',$value->SaleAmount)),
                "SaleAmountDa" => number_format($RemainingAmount),
                "Discount" => number_format(str_replace(',', '',$value->Discount)),
                "Remarks" => $value->Remarks,
                "sumAmount"=>number_format(str_replace(',', '',$sumAmount)),
                "sumAmxs" => str_replace(',', '',$value->SaleAmount) - str_replace(',','',$value->Discount) == $recivedAmt ? '0' : number_format($minusxs - $recivedAmt),
                "PayRecieptsentry"=>json_encode($data2),
                "PayRecieptHistory"=>$data2,
                "PayRecieptToday"=>$data3,
                "recivedAmt"=>number_format($minusxs - $recivedAmt),
                "TotalSumAmounts"=>number_format($recivedAmt),
                "TotalSumAmountsFormat"=>$recivedAmt,
                "TotalSumAmountsToday"=>number_format($recivedAmtToday),
                "TotalSumAmountsFormaTodayt"=>$recivedAmtToday,
                "emp_name"=>$Employee == '' || $Employee == null ? '' : $Employee->emp_name,
                "AuthorizedByPerson" => $Companys->ownerTitle,
                "RemainingAmountData" => str_replace(',', '',$value->SaleAmount) - str_replace(',', '',$value->Discount) - $recivedAmt,
                'finalTotalAmt'=>str_replace(',', '',$value->SaleAmount) - str_replace(',','',$value->Discount),
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
    public function store(PayRecieptRequest $request)
    {
        
         $getYear = date("Y");
         $getMonth = date("m");
         
        $Project = Project::where(['id'=>$request->ProjectId])->first();
        $propertyType = propertyType::where(['id'=>$request->PropTypeId])->first();
        $Companys = Company::where(['id'=>$request->CompanyId])->first();
         
        $companyShort = $Companys == '' || $Companys == null ? '' : $Companys->company_abbr;
        $projectShort = $Project == '' || $Project == null ? '' : $Project->project_short_name;
        $propertyTypeShort = $propertyType == '' || $propertyType == null ? '' : $propertyType->shortName;
        $sizeShort = $request->Size;
        
        $getmonthVoucher = Payreciepts::where(['CompanyId'=>$request->CompanyId,'ProjectId'=>$request->ProjectId,'PropTypeId'=>$request->PropTypeId,'Size'=>$request->Size])->where(DB::raw('MONTH(created_at)'),$getMonth)->get();
        
        $countxs = count($getmonthVoucher);
        if($countxs == null || $countxs == '' || $countxs == 0){
            $monthCountX = 1;
        }else{
            $monthCountX = $countxs + 1;
        }
        
        $getyearVoucher = Payreciepts::where(['CompanyId'=>$request->CompanyId,'ProjectId'=>$request->ProjectId,'PropTypeId'=>$request->PropTypeId,'Size'=>$request->Size])->where(DB::raw('YEAR(created_at)'),$getYear)->get();
        $countYear = count($getyearVoucher);
        
        if(empty($countYear)){
            $yearCOunt = 1;
        }else{
            $yearCOunt = $countYear + 1;
        }
        
        $monthCount = str_pad($monthCountX,4,'0',STR_PAD_LEFT);
        $yearCount = str_pad($yearCOunt,4,'0',STR_PAD_LEFT);
        
        $recieptVoucherNo = $companyShort."/".$projectShort."/".$propertyTypeShort."/".$sizeShort."/".$getYear."/".$getMonth."/".$monthCount."/".$yearCount;
        
        $response = Payreciepts::create([
            'dateOfReciept' =>  $request->dateOfReciept,
            'RecieptNo' => $recieptVoucherNo,
            'ProjectId' =>  $request->ProjectId,
            'CompanyId' =>  $request->CompanyId,
            'PropTypeId' =>  $request->PropTypeId,
            'ProNo' =>  $request->ProNo,
            'BlockNo' =>  $request->BlockNo,
            'Size' =>  $request->Size,
            'CustomerName' =>  $request->CustomerName,
            'Cnic' =>  $request->Cnic,
            'ContactNo' =>  $request->ContactNo,
            'Reference' =>  $request->Reference,
            'ReferenceContactNo' =>  $request->ReferenceContactNo,
            'SaleAmount' =>  $request->SaleAmount,
            'Discount' =>  $request->Discount,
            'Remarks' =>  $request->Remarks,
            'PostedBy' => $request->PostedBy,
            'company_id'=>$request->company_id,
        ]);
        
        $paymentRecieptsId = $response->id;
        
        
        $paymentMethodJson = json_decode($request->inputList,JSON_UNESCAPED_SLASHES);
        
        $dataPay = [];
        
        foreach($paymentMethodJson as $myPayment){
            
            $paymentMethod = $myPayment['payMethod'];
            $ChequePoReference = $myPayment['CheqPoNo'];
            $payDate = $myPayment['PayDate'];
            $BankName = $myPayment['BankName'];
            $ReferenceTitle = $myPayment['ReferenceId'];
            $TransferTo = $myPayment['TransferTo'];
            $AmountPay = $myPayment['PayAmt'];
          
           
            Payrecieptlist::create([
                'payrecieptId' =>  $paymentRecieptsId,
                'RemainingAmount'=>$request->SaleAmount - $request->Discount,
                'paymentMethod' =>  $paymentMethod,
                'ChequePoReference' => $ChequePoReference == '' || $ChequePoReference == null ? '0' : $ChequePoReference,
                'payDate' => $payDate,
                'BankName' => $BankName == '' || $BankName == null ? '0' : $BankName,
                'ReferenceTitle' => $ReferenceTitle,
                'TransferTo' => $TransferTo,
                'AmountPay' => $AmountPay,
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
    public function show(Payreciepts $payreciepts,$id="")
    {
        $response = Payreciepts::where(['id'=>$id])->get();
            
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
    public function update(PayRecieptRequest $request, Payreciepts $payreciepts,$id="")
    {
        Payreciepts::where("id",$id)->update([
            'dateOfReciept' =>  $request->dateOfReciept,
            'ProjectId' =>  $request->ProjectId,
            'CompanyId' =>  $request->CompanyId,
            'PropTypeId' =>  $request->PropTypeId,
            'ProNo' =>  $request->ProNo,
            'BlockNo' =>  $request->BlockNo,
            'Size' =>  $request->Size,
            'CustomerName' =>  $request->CustomerName,
            'Cnic' =>  $request->Cnic,
            'ContactNo' =>  $request->ContactNo,
            'Reference' =>  $request->Reference,
            'ReferenceContactNo' =>  $request->ReferenceContactNo,
            'SaleAmount' =>  $request->SaleAmount,
            'Discount' =>  $request->Discount,
            'Remarks' =>  $request->Remarks,
            'company_id'=>$request->company_id,
        ]);
        
        
        $paymentMethodJson = json_decode($request->inputList,JSON_UNESCAPED_SLASHES);
        
        $dataPay = [];
        
        $checkRemainingAmount = Payrecieptlist::where("payrecieptId",$id)->get()->last(); 
        
        foreach($paymentMethodJson as $myPayment){
            
            $payrecieptEntryId = $myPayment['payRecieptEntryId'];
            
            $paymentMethod = $myPayment['payMethod'];
            $ChequePoReference = $myPayment['CheqPoNo'];
            $payDate = $myPayment['PayDate'];
            $BankName = $myPayment['BankName'];
            $ReferenceTitle = $myPayment['ReferenceId'];
            $TransferTo = $myPayment['TransferTo'];
            $AmountPay = $myPayment['PayAmt'];
            
            
            if($payrecieptEntryId == '' || $payrecieptEntryId == null){
                
                $PayRec = Payrecieptlist::create([
                    'payrecieptId'=>$id,
                    'paymentMethod' =>  $paymentMethod,
                    'ChequePoReference' => $ChequePoReference == '' || $ChequePoReference == null ? '0' : $ChequePoReference,
                    'payDate' => $payDate,
                    'BankName' => $BankName == '' || $BankName == null ? '0' : $BankName,
                    'ReferenceTitle' => $ReferenceTitle,
                    'TransferTo' => $TransferTo,
                    'AmountPay' => $AmountPay,
                ]);
                
                $lastId = $PayRec->id;
                
                Payrecieptlist::where("id",$lastId)->update([
                    'RemainingAmount' =>  $checkRemainingAmount->RemainingAmount - $AmountPay,
                ]);
                
                
            }else{
                
                Payrecieptlist::where("id",$payrecieptEntryId)->update([
                    'paymentMethod' =>  $paymentMethod,
                    'ChequePoReference' => $ChequePoReference == '' || $ChequePoReference == null ? '0' : $ChequePoReference,
                    'payDate' => $payDate,
                    'BankName' => $BankName == '' || $BankName == null ? '0' : $BankName,
                    'ReferenceTitle' => $ReferenceTitle,
                    'TransferTo' => $TransferTo,
                    'AmountPay' => $AmountPay,
                ]);
                
            }
           
            
                     
        }
        
        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $payreciepts,
            'dataPay'=>$checkRemainingAmount,
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
        
        $Payreciepts = Payreciepts::where(['id'=>$id])->delete();
        $Payrecieptlist = Payrecieptlist::where(['payrecieptId'=>$id])->delete();

        return response()->json([
            'message' => "Data deleted successfully!",
            'data'=>$Payrecieptlist,
            'code'=>'200'
        ], 200);
    }
    
    public function PaymentRecieptFilters (Request $request){
       
        
        $DateOfRecieptFrom = $request->DateOfRecieptFrom ?? "";
        $DateOfRecieptTo = $request->DateOfRecieptTo ?? "";
        
        $PropertyNoFrom = $request->PropertyNoFrom ?? "";
        $PropertyNoTo = $request->PropertyNoTo ?? "";
        
        $ProjectFrom = $request->ProjectFrom ?? "";
        $ProjectTo = $request->ProjectTo ?? "";
        
        $CompanyFrom = $request->CompanyFrom ?? "";
        $CompanyTo = $request->CompanyTo ?? "";
        
        $PropertyTypeFrom = $request->PropertyTypeFrom ?? "";
        $PropertyTypeTo = $request->PropertyTypeTo ?? "";
        
        
        if($DateOfRecieptFrom != '' && $DateOfRecieptTo !='' && $PropertyNoFrom == '' && $PropertyNoTo == '' && $ProjectFrom == '' && $ProjectTo == '' && $CompanyFrom == '' && $CompanyTo == '' && $PropertyTypeFrom == '' && $PropertyTypeTo == ''){
            
            $response = Payreciepts::whereBetween('dateOfReciept', array($DateOfRecieptFrom,$DateOfRecieptTo))->get();
            $count = Payreciepts::whereBetween('dateOfReciept', array($DateOfRecieptFrom,$DateOfRecieptTo))->count();
            
        }else if($DateOfRecieptFrom != '' && $DateOfRecieptTo !='' && $PropertyNoFrom != '' && $PropertyNoTo != '' && $ProjectFrom == '' && $ProjectTo == '' && $CompanyFrom == '' && $CompanyTo == '' && $PropertyTypeFrom == '' && $PropertyTypeTo == ''){
            
            $response = Payreciepts::whereBetween('dateOfReciept', array($DateOfRecieptFrom,$DateOfRecieptTo))->whereBetween('ProNo', array($PropertyNoFrom,$PropertyNoTo))->get();
            $count = Payreciepts::whereBetween('dateOfReciept', array($DateOfRecieptFrom,$DateOfRecieptTo))->whereBetween('ProNo', array($PropertyNoFrom,$PropertyNoTo))->count();
              
            
        }else if($DateOfRecieptFrom != '' && $DateOfRecieptTo !='' && $PropertyNoFrom != '' && $PropertyNoTo != '' && $ProjectFrom != '' && $ProjectTo != '' && $CompanyFrom == '' && $CompanyTo == '' && $PropertyTypeFrom == '' && $PropertyTypeTo == ''){
            
            $response = Payreciepts::whereBetween('dateOfReciept', array($DateOfRecieptFrom,$DateOfRecieptTo))->whereBetween('ProNo', array($PropertyNoFrom,$PropertyNoTo))->whereBetween('ProjectId', array($ProjectFrom,$ProjectTo))->get();
            
            $count = Payreciepts::whereBetween('dateOfReciept', array($DateOfRecieptFrom,$DateOfRecieptTo))->whereBetween('ProNo', array($PropertyNoFrom,$PropertyNoTo))->whereBetween('ProjectId', array($ProjectFrom,$ProjectTo))->count();
            
            
        }else if($DateOfRecieptFrom != '' && $DateOfRecieptTo !='' && $PropertyNoFrom != '' && $PropertyNoTo != '' && $ProjectFrom != '' && $ProjectTo != '' && $CompanyFrom != '' && $CompanyTo != '' && $PropertyTypeFrom == '' && $PropertyTypeTo == ''){
            
             $response = Payreciepts::whereBetween('dateOfReciept', array($DateOfRecieptFrom,$DateOfRecieptTo))->whereBetween('ProNo', array($PropertyNoFrom,$PropertyNoTo))->whereBetween('ProjectId', array($ProjectFrom,$ProjectTo))->whereBetween('CompanyId', array($CompanyFrom,$CompanyTo))->get();
            
            $count = Payreciepts::whereBetween('dateOfReciept', array($DateOfRecieptFrom,$DateOfRecieptTo))->whereBetween('ProNo', array($PropertyNoFrom,$PropertyNoTo))->whereBetween('ProjectId', array($ProjectFrom,$ProjectTo))->whereBetween('CompanyId', array($CompanyFrom,$CompanyTo))->count();
            
            
        }else if($DateOfRecieptFrom != '' && $DateOfRecieptTo !='' && $PropertyNoFrom != '' && $PropertyNoTo != '' && $ProjectFrom != '' && $ProjectTo != '' && $CompanyFrom != '' && $CompanyTo != '' && $PropertyTypeFrom != '' && $PropertyTypeTo != ''){
            
             $response = Payreciepts::whereBetween('dateOfReciept', array($DateOfRecieptFrom,$DateOfRecieptTo))->whereBetween('ProNo', array($PropertyNoFrom,$PropertyNoTo))->whereBetween('ProjectId', array($ProjectFrom,$ProjectTo))->whereBetween('CompanyId', array($CompanyFrom,$CompanyTo))->whereBetween('PropTypeId', array($PropertyTypeFrom,$PropertyTypeTo))->get();
            
            $count = Payreciepts::whereBetween('dateOfReciept', array($DateOfRecieptFrom,$DateOfRecieptTo))->whereBetween('ProNo', array($PropertyNoFrom,$PropertyNoTo))->whereBetween('ProjectId', array($ProjectFrom,$ProjectTo))->whereBetween('CompanyId', array($CompanyFrom,$CompanyTo))->whereBetween('PropTypeId', array($PropertyTypeFrom,$PropertyTypeTo))->count();
            
        }else if($DateOfRecieptFrom == '' && $DateOfRecieptTo =='' && $PropertyNoFrom == '' && $PropertyNoTo == '' && $ProjectFrom != '' && $ProjectTo != '' && $CompanyFrom == '' && $CompanyTo == '' && $PropertyTypeFrom == '' && $PropertyTypeTo == ''){
            
             $response = Payreciepts::whereBetween('ProjectId', array($ProjectFrom,$ProjectTo))->get();
            
            $count = Payreciepts::whereBetween('ProjectId', array($ProjectFrom,$ProjectTo))->count();
            
        }else if($DateOfRecieptFrom == '' && $DateOfRecieptTo =='' && $PropertyNoFrom == '' && $PropertyNoTo == '' && $ProjectFrom != '' && $ProjectTo != '' && $CompanyFrom != '' && $CompanyTo != '' && $PropertyTypeFrom == '' && $PropertyTypeTo == ''){
            
             $response = Payreciepts::whereBetween('ProjectId', array($ProjectFrom,$ProjectTo))->whereBetween('CompanyId', array($CompanyFrom,$CompanyTo))->get();
            
            $count = Payreciepts::whereBetween('ProjectId', array($ProjectFrom,$ProjectTo))->whereBetween('CompanyId', array($CompanyFrom,$CompanyTo))->count();
            
        }else if($DateOfRecieptFrom == '' && $DateOfRecieptTo =='' && $PropertyNoFrom == '' && $PropertyNoTo == '' && $ProjectFrom == '' && $ProjectTo == '' && $CompanyFrom != '' && $CompanyTo != '' && $PropertyTypeFrom == '' && $PropertyTypeTo == ''){
            
             $response = Payreciepts::whereBetween('CompanyId', array($CompanyFrom,$CompanyTo))->get();
            
            $count = Payreciepts::whereBetween('CompanyId', array($CompanyFrom,$CompanyTo))->count();
            
        }else if($DateOfRecieptFrom != '' && $DateOfRecieptTo !='' && $PropertyNoFrom == '' && $PropertyNoTo == '' && $ProjectFrom != '' && $ProjectTo != '' && $CompanyFrom == '' && $CompanyTo == '' && $PropertyTypeFrom == '' && $PropertyTypeTo == ''){
            
             $response = Payreciepts::whereBetween('dateOfReciept', array($DateOfRecieptFrom,$DateOfRecieptTo))->whereBetween('ProjectId', array($ProjectFrom,$ProjectTo))->get();
            
            $count = Payreciepts::whereBetween('dateOfReciept', array($DateOfRecieptFrom,$DateOfRecieptTo))->whereBetween('ProjectId', array($ProjectFrom,$ProjectTo))->count();
            
        }else{
            
            $response = Payreciepts::all();
            $count = Payreciepts::count();

        }
        
       
        
        
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);
                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Payment Reciept Filters Found',
                    'data' => $data,
                    'orderBy' => '',
                    'orderType' => '',
                    'perPage' => '',
                    'pageNo' => '',
                    'TotalCount'=>$count,
                ]);
            }

            // FAIL
            else {
                return app(CommonController::class)->getEmptyListResponse();
            }
    }
    
    
    
}