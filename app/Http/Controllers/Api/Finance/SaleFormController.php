<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\SaleslistRequest;
use App\Models\Finance\Salesform;
use App\Models\Finance\Coagent;
use App\Models\Finance\Saleformreciept;
use App\Models\Finance\BankListData;
use App\Models\Finance\Banklist;


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

class SaleFormController extends Controller
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
            
            if(empty($q)){
                $response = Salesform::where(['company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
            }else{
                $response = Salesform::where(['accountno'=>$q,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
            }
            
            $count = Salesform::count();
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Sales Form List Found',
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
            
          $response = Salesform::where(['company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
            $count = Salesform::where(['company_id'=>$company_id])->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Sales Form List Found',
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
            
        }else if(!empty($agentId) && $agentId !='all'){
            
            $response = Salesform::where(['agentId'=>$agentId,'status'=>'pending','company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
            $count = Salesform::where(['agentId'=>$agentId,'status'=>'pending','company_id'=>$company_id])->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Sales Form List Found',
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
            
            $Project = Project::where(['id'=>$value->projectId])->first();
            
            $propertyType = propertyType::where(['id'=>$value->typeId])->first();
            $Companys = Company::where(['id'=>$value->CompanyId])->first();
            
            $Projectasset = Projectasset::where(['project_id'=>$Project->id])->first();
            
            $CoagentlistEntry = Coagent::where(['saleformId'=>$value->id])->get();
            
            $Employee = Employee::where(['id'=>$value->agentId])->first();
            
            $CommissionDetailssentry = Saleformreciept::where(['saleformId'=>$value->id])->get();
            
             $data2 = [];
             $countx = 0;
             $sumPercentage = 0;
             $agentCommission = 0;
              foreach($CoagentlistEntry as $entryData){
                $countx++;
                $sumPercentage+= $entryData->coagentpercentage == '' || $entryData->coagentpercentage == null ? '0' : $entryData->coagentpercentage;
                
                $agentCommission+= $entryData->coagentpercentage == '' || $entryData->coagentpercentage == null ? '0' : $value->totalCommission / 100 * $entryData->coagentpercentage;
                array_push($data2, (object) [
                    "commissionId" => $entryData->id,
                    "agentName" => $entryData->coagentName,
                    "sharePercentage" => $entryData->coagentpercentage,
                    "TotalCommission" => $value->totalCommission,
                    "totalCommissionValxs"=>$entryData->coagentpercentage == null || $entryData->coagentpercentage == '' ? 0 : number_format($value->totalCommission),
                ]);
            }
            
             $data3 = [];
            $sumAmount = 0;
            $recivedAmt = 0;
            $totalSumAmount = 0;
        $countx = 0;
            
            foreach($CommissionDetailssentry as $entryData){
                
                
                $countx++;
                
                $Banklist = BankListData::where(['id'=>$entryData->BankName])->first();
                $Company = Company::where(['id'=>$entryData->ReferenceTitle])->first();
                
                $Banklistings = Banklist::where(['id'=>$entryData->TransferTo])->first();
                
                $recivedAmt  += $entryData->AmountPay == '' || $entryData->AmountPay == null ? 0 : str_replace(',', '',$entryData->AmountPay);
                
                
                array_push($data3, (object) [
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
                    "PayAmt" => $entryData->AmountPay,
                    "ProjectTitle" => $Project == '' || $Project == null ? '' : $Project->project_name,
                    "Transferbankname"=>$Banklistings == '' || $Banklistings == null ? '' : $Banklistings->accountno,
                    "DueAmount" =>  $value->Discount == '' || $value->Discount == null || $value->SaleAmount == null || $value->SaleAmount == '' ? 0 : number_format(str_replace(',', '',$value->SaleAmount) - str_replace(',', '',$value->Discount)),
                    "Payamount" => $entryData->AmountPay == '' || $entryData->AmountPay == null ? 0 : number_format(str_replace(',', '',$entryData->AmountPay)),
                    "RemainingAmount"=>number_format($entryData->RemainingAmount),
                   
                ]);
                
                $sumAmount+= $entryData->AmountPay == '' || $entryData->AmountPay == null ? 0 : str_replace(',', '',$entryData->AmountPay);
                
            }
            
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "ProjectId" => $value->projectId,
                "ProjectTitle" => $Project == '' || $Project == null ? '' : $Project->project_name,
                "ProjectLogo" => $Projectasset == '' || $Projectasset == null ? '' : "public/storage/projectLogo/".$Projectasset->asset_name,
                "CompanyId" => $value->CompanyId,
                "PropTypeId" => $value->typeId,
                "categoryName" => $value->categoryName,
                "type_name" => $propertyType == '' || $propertyType == null ? '' : $propertyType->type_name,
                "ProNo" => $value->propertyNo,
                "BlockName" =>$value->blockName,
                "dateOfSale"=>$value->dateOfSale,
                "Size" => $value->Size,
                "Unit"=>$value->sizeType,
                "CustomerName" => $value->customerName,
                "Cnic" => $value->Cnic,
                "ContactNo" => $value->contactNo,
                "Reference" => '',
                 "SaleAmount" => number_format(str_replace(',', '',$value->SaleAmount)),
                  "SaleAmountVal" => $value->SaleAmount,
                 "Discount" => $value->Discount == null || $value->Discount == '' ? 0 : number_format(str_replace(',', '',$value->Discount)),
                 "DiscountVal" => $value->Discount,
                 "totalCommission"=>$value->totalCommission == '' || $value->totalCommission == null ? '0' : number_format(str_replace(',', '',$value->totalCommission)),
                 "totalCommissionVal"=>$value->totalCommission,
                  "commissionHistory"=>json_encode($data2),
                  "transactionHistory"=>json_encode($data3),
                  "status"=>$value->status,
                  "invoiceDate"=>date("Y-m-d",strtotime($value->created_at)),
                  "invoiceNo"=>$value->saleformVoucherNo,
                  "SaleRecieptAll"=>$data3,
                  "TotalSumAmounts"=>$recivedAmt,
                  "agentCommisionAll"=>$data2,
                  "sumPercentage"=>$sumPercentage,
                  "netSaleAmount"=>number_format($value->SaleAmount - $value->Discount),
                  "balanceAmount" => number_format($value->SaleAmount - $value->Discount - $recivedAmt),
                  "agentCommission"=>number_format($agentCommission),
                  "Employee"=>$Employee,
                  "ReferenceContactNo"=>$value->ReferenceContactNo,
                    "salesConsultant"=>$value->salesConsultant,
                    "emp_name"=>$Employee == null || $Employee == '' ? '' : $Employee->emp_name,
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
    public function store(SaleslistRequest $request)
    {
        
        $getYear = date("Y");
         $getMonth = date("m");
         
        $Project = Project::where(['id'=>$request->projectId])->first();
        $propertyType = propertyType::where(['id'=>$request->typeId])->first();
        $Companys = Company::where(['id'=>$request->CompanyId])->first();
         
        $companyShort = $Companys == '' || $Companys == null ? '' : $Companys->company_abbr;
        $projectShort = $Project == '' || $Project == null ? '' : $Project->project_short_name;
        $propertyTypeShort = $propertyType == '' || $propertyType == null ? '' : $propertyType->shortName;
        $sizeShort = $request->Size;
        
        $getmonthVoucher = Salesform::where(['CompanyId'=>$request->CompanyId,'projectId'=>$request->projectId,'typeId'=>$request->typeId,'Size'=>$request->Size])->where(DB::raw('MONTH(created_at)'),$getMonth)->get();
        
        $countxs = count($getmonthVoucher);
        if($countxs == null || $countxs == '' || $countxs == 0){
            $monthCountX = 1;
        }else{
            $monthCountX = $countxs + 1;
        }
        
        $getyearVoucher = Salesform::where(['CompanyId'=>$request->CompanyId,'projectId'=>$request->projectId,'typeId'=>$request->typeId,'Size'=>$request->Size])->where(DB::raw('YEAR(created_at)'),$getYear)->get();
        $countYear = count($getyearVoucher);
        
        if(empty($countYear)){
            $yearCOunt = 1;
        }else{
            $yearCOunt = $countYear + 1;
        }
        
        $monthCount = str_pad($monthCountX,4,'0',STR_PAD_LEFT);
        $yearCount = str_pad($yearCOunt,4,'0',STR_PAD_LEFT);
        
        $saleformVoucherNo = $companyShort."/".$projectShort."/".$propertyTypeShort."/".$sizeShort."/".$getYear."/".$getMonth."/".$monthCount."/".$yearCount;
        
        $response = Salesform::create([
            'saleformVoucherNo' =>  $saleformVoucherNo,
            'agentId' =>  $request->agentId,
            'projectId' => $request->projectId,
            'typeId' =>  $request->typeId,
            'propertyNo' =>  $request->propertyNo,
            'Size' =>  $request->Size,
            'sizeType' =>  $request->sizeType,
            'categoryName' =>  $request->categoryName,
            'SaleAmount' =>  $request->SaleAmount,
            'Discount' =>  $request->Discount,
            'totalPaymentRecieved' =>  0,
            'status' =>  $request->status,
            'customerName' =>  $request->customerName,
            'Cnic' =>  $request->Cnic,
            'contactNo' =>  $request->contactNo,
            'totalCommission' =>  $request->totalCommission,
            'CompanyId' =>  $request->CompanyId,
            'blockName' =>  $request->blockName,
            'dateOfSale' =>  $request->dateOfSale,
            "ReferenceContactNo"=>$request->ReferenceContactNo,
            "salesConsultant"=>$request->salesConsultant,
        ]);
        
        $salesformId = $response->id;
        
        
        $CommissionMethodJson = json_decode($request->commissionlist,JSON_UNESCAPED_SLASHES);
        
        $dataPay = [];
        
        foreach($CommissionMethodJson as $myCommission){
            
            $agentName = $myCommission['agentName'];
            $sharePercentage = $myCommission['sharePercentage'];
            
            if(empty($agentName)){
                
            }else{
                
                 Coagent::create([
                'saleformId' =>  $salesformId,
                'coagentName'=>$agentName,
                'coagentpercentage' => $sharePercentage,
            ]);
                
            }
            
        }
        
        if($request->transactionlist == ''){
            
        }else{
        
            $paymentMethodJson = json_decode($request->transactionlist,JSON_UNESCAPED_SLASHES);
            
            $dataPays = [];
            
            $checkRemainingAmount = Saleformreciept::where("saleformId",$salesformId)->get()->last(); 
            
            foreach($paymentMethodJson as $myPayment){
                
                $paymentMethod = $myPayment['payMethod'];
                $ChequePoReference = $myPayment['CheqPoNo'];
                $payDate = $myPayment['PayDate'];
                $BankName = $myPayment['BankName'];
                $ReferenceTitle = $myPayment['ReferenceId'];
                $TransferTo = $myPayment['TransferTo'];
                $AmountPay = $myPayment['PayAmt'];
                    
                $PayRec = Saleformreciept::create([
                    'saleformId'=>$salesformId,
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
    public function show(Salesform $salesform,$id="")
    {
       $response = Salesform::where(['id'=>$id])->get();
            
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
    public function update(Request $request,$id="")
    {

        $update = Salesform::where("id",$id)->update([
            'projectId' => $request->projectId,
            'typeId' =>  $request->typeId,
            'propertyNo' =>  $request->propertyNo,
            'Size' =>  $request->Size,
            'sizeType' =>  $request->sizeType,
            'categoryName' =>  $request->categoryName,
            'SaleAmount' =>  $request->SaleAmount,
            'Discount' =>  $request->Discount,
            'status' =>  $request->status,
            'customerName' =>  $request->customerName,
            'Cnic' =>  $request->Cnic,
            'contactNo' =>  $request->contactNo,
            'totalCommission' =>  $request->totalCommission,
            'CompanyId' =>  $request->CompanyId,
            'blockName' =>  $request->blockName,
            'dateOfSale' =>  $request->dateOfSale,
            "ReferenceContactNo"=>$request->ReferenceContactNo,
            "salesConsultant"=>$request->salesConsultant,
        ]);
        
        $CommissionMethodJson = json_decode($request->commissionlist,JSON_UNESCAPED_SLASHES);
        
        $dataCommission = [];
        
        foreach($CommissionMethodJson as $myComm){
            
            $commissionId = $myComm['commissionId'];
            
            $agentName = $myComm['agentName'];
            $sharePercentage = $myComm['sharePercentage'];
            
            if($commissionId == '' || $commissionId == null){
              
                $coAGent = Coagent::create([
                    'saleformId'=>$id,
                    'coagentName' =>  $agentName,
                    'coagentpercentage' => $sharePercentage,
                ]);
                
                $lastId = $coAGent->id;
                
                // Payrecieptlist::where("id",$lastId)->update([
                //     'RemainingAmount' =>  $checkRemainingAmount->RemainingAmount - $AmountPay,
                // ]);
                
                
            }else{
                
                Coagent::where("id",$commissionId)->update([
                    'coagentName' =>  $agentName,
                    'coagentpercentage' => $sharePercentage,
                ]);
                
            }
                 
        }
        
        
        $paymentMethodJson = json_decode($request->transactionlist,JSON_UNESCAPED_SLASHES);
        
        $dataPays = [];
        
        $checkRemainingAmount = Saleformreciept::where("saleformId",$id)->get()->last(); 
        
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
                
                $PayRec = Saleformreciept::create([
                    'saleformId'=>$id,
                    'paymentMethod' =>  $paymentMethod,
                    'ChequePoReference' => $ChequePoReference == '' || $ChequePoReference == null ? '0' : $ChequePoReference,
                    'payDate' => $payDate,
                    'BankName' => $BankName == '' || $BankName == null ? '0' : $BankName,
                    'ReferenceTitle' => $ReferenceTitle,
                    'TransferTo' => $TransferTo,
                    'AmountPay' => $AmountPay,
                ]);
                
                $lastId = $PayRec->id;
                
                if(empty($checkRemainingAmount)){
                    
                }else{
                    Saleformreciept::where("id",$lastId)->update([
                    'RemainingAmount' =>  $checkRemainingAmount->RemainingAmount - $AmountPay,
                ]);
                }
                
                
                
                
            }else{
                
                Saleformreciept::where("id",$payrecieptEntryId)->update([
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
            'data' =>$update,
            'code'=>'200'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(BanklistRequest $request, Banklist $banklist)
    {
        $banklist->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code'=>'200'
        ], 200);
    }
    
    
    
    
}