<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\PettyCashRequest;
use App\Models\Finance\Pettycash;
use App\Models\Finance\Pettycashdetails;
use App\Models\HR\Company;
use App\Models\HR\Employee;
use App\Models\Finance\Banklist;
use App\Models\Finance\BankListData;
use App\Models\Finance\Pettycashvoucher;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;
use DB;

class PettyCashController extends Controller
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
        $company_id = $request->company_id ?? "" ;

        if (is_null($agentId)) {
            
            $response = Pettycash::where(['company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
           
            $count = Pettycash::where(['company_id'=>$company_id])->count();
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Petty Cash List Found',
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
            
                $response = Pettycash::where(['company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
            $count = Pettycash::where(['company_id'=>$company_id])->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Petty Cash List Found',
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
        $data2=[];
        $sumAmount = 0;
        
        foreach ($apiResponse as $key => $value) {
            
            $sumAmount = 0;
            
            $Employee = Employee::where(['id'=>$value->agentId])->first();
            
            $Pettycashdetails = Pettycashdetails::where(['pettycashId'=>$value->id])->get();
            
            if($value->BankId == '' || $value->BankId == 0 || $value->BankId == null){
                $BankListData="";
            }else{
                $BankListData=BankListData::where(['id'=>$value->BankId])->first();
            }
            
            foreach($Pettycashdetails as $entryData){

                array_push($data2, (object) [
                    "payEntryId" => $entryData->id,
                    "PayDate" => date("Y-m-d",strtotime($entryData->payDate)),
                    "PayDetails" =>$entryData->payDetails,
                    "PayAmt" => $entryData->PayAmount,
                    "PayTo" => $entryData->PayTo,
                    "status" => $entryData->status,
                ]);
                
                $sumAmount+=$entryData->PayAmount;
                
                }
            
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "referenceId"=>$value->referenceId,
                "agentId"=>$value->agentId,
                "emp_name"=>$Employee == '' || $Employee == null ? '' : $Employee->emp_name,
                "CashAmount"=>$value->CashAmount,
                "CashAmountFormat"=>number_format($value->CashAmount),
                'dateOfCash'=>$value->dateOfCash,
                'CompanyId'=>$value->CompanyId,
                'ModeOfPayment'=>$value->ModeOfPayment,
                'ChequeNo'=>$value->ChequeNo,
                'ChhequeDate'=>$value->ChhequeDate,
                'BankId'=>$value->BankId,
                "PettyCashEntry"=>json_encode($data2),
                "sumAmount"=>$sumAmount,
                "sumAmountFormat"=>number_format($sumAmount),
                "balanceAmount"=>$value->CashAmount - $sumAmount,
                "balanceAmountFormat"=>number_format($value->CashAmount - $sumAmount),
                "bankName"=>$BankListData == '' || $BankListData == null ? '-' : $BankListData->banktitle,
                "Payee"=>$value->Payee,
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
    public function store(PettyCashRequest $request)
    {
        
        $getYear = date("Y");
        $getMonth = date("m");
        
        $Companys = Company::where(['id'=>$request->CompanyId])->first();
        $companyShort = $Companys == '' || $Companys == null ? '' : $Companys->company_abbr;
        
        $getmonthVoucher = Pettycash::where(['CompanyId'=>$request->CompanyId])->where(DB::raw('MONTH(created_at)'),$getMonth)->get();
        
        $countxs = count($getmonthVoucher);
        if($countxs == null || $countxs == '' || $countxs == 0){
            $monthCountX = 1;
        }else{
            $monthCountX = $countxs + 1;
        }
        
        $getyearVoucher = Pettycash::where(['CompanyId'=>$request->CompanyId])->where(DB::raw('YEAR(created_at)'),$getYear)->get();
        $countYear = count($getyearVoucher);
        
        if(empty($countYear)){
            $yearCOunt = 1;
        }else{
            $yearCOunt = $countYear + 1;
        }
        
        $monthCount = str_pad($monthCountX,4,'0',STR_PAD_LEFT);
        $yearCount = str_pad($yearCOunt,4,'0',STR_PAD_LEFT);
        
        $referenceId = $companyShort."/".$getYear."/".$getMonth."/".$yearCount."/".$monthCount;
        
        
        $response = Pettycash::create([
            'referenceId' =>  $referenceId,
            'dateOfCash' => $request->DateOfCash,
            'CompanyId' =>  $request->CompanyId,
            'agentId' =>  $request->agentId,
            'CashAmount' =>  $request->CashAmount,
            'ModeOfPayment' =>  $request->ModeOfPayment,
            'ChequeNo' =>  $request->ChequeNo,
            'ChhequeDate' =>  $request->ChequeDate,
            'BankId' =>  $request->BankId,
            'Payee'=>$request->Payee,
            'company_id'=>$request->company_id,
        ]);
        
        
        $id = $response->id;
        
        $payDetailsJson = json_decode($request->payDetails,JSON_UNESCAPED_SLASHES);
        
        $dataPay = [];
        
        foreach($payDetailsJson as $myPayment){
            
            $payDate = $myPayment['PayDate'];
            $PayDetails = $myPayment['PayDetails'];
            $PayAmt = $myPayment['PayAmt'];
            $PayTo = $myPayment['PayTo'];
            $status = $myPayment['status'];
           
            $PettyDetails = Pettycashdetails::create([
                'pettycashId' =>  $id,
                'payDate'=>$payDate,
                'payDetails' =>  $PayDetails,
                'PayAmount' => $PayAmt == null || $PayAmt == '' ? 0 : $PayAmt,
                'PayTo' => $PayTo,
                'status'=>$status
            ]);
            
            $pdid = $PettyDetails->id;
            
            $CompanysCash = Company::where(['id'=>$request->CompanyId])->first();
            $companyCashShort = $CompanysCash == '' || $CompanysCash == null ? '' : $CompanysCash->company_abbr;
            
            $getmonthCashVoucher = Pettycashvoucher::where(['pdid'=>$pdid])->where(DB::raw('MONTH(created_at)'),$getMonth)->get();
            
            $countCashxs = count($getmonthCashVoucher);
            if($countCashxs == null || $countCashxs == '' || $countCashxs == 0){
            $monthCountCashX = 1;
            }else{
            $monthCountCashX = $countCashxs + 1;
            }
            
            $getyearCashVoucher = Pettycashvoucher::where(['pdid'=>$pdid])->where(DB::raw('YEAR(created_at)'),$getYear)->get();
            $countCashYear = count($getyearCashVoucher);
            
            if(empty($countCashYear)){
            $yearCashCOunt = 1;
            }else{
            $yearCashCOunt = $countCashYear + 1;
            }
            
            $monthCashCount = str_pad($monthCountCashX,4,'0',STR_PAD_LEFT);
            $yearCashCount = str_pad($yearCashCOunt,4,'0',STR_PAD_LEFT);
            
            $PettyVoucherNo = $companyCashShort."/".$getYear."/".$getMonth."/".$yearCashCount."/".$monthCashCount."/".$pdid;
        
            Pettycashvoucher::create([
                'pettyId' =>  $id,
                'pdid'=>$pdid,
                'PettyVoucherNo' =>  $PettyVoucherNo,
                'VoucherDate' => date("Y-m-d"),
                'TransactionDate' => $payDate,
                'PayTo' => $PayTo,
                'VoucherAmount' => $PayAmt,
                'VoucherDesc' => $PayDetails,
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
    public function show($id="")
    {
        $response = Pettycash::where(['id'=>$id])->get();
            
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
    public function update(PettyCashRequest $request,$id="")
    {
        
        $getYear = date("Y");
        $getMonth = date("m");
        $sumAmount = 0;
        
         Pettycash::where("id",$id)->update([
            'dateOfCash' => $request->DateOfCash,
            'CompanyId' =>  $request->CompanyId,
            'CashAmount' =>  $request->CashAmount,
            'ModeOfPayment' =>  $request->ModeOfPayment,
            'ChequeNo' =>  $request->ChequeNo,
            'ChhequeDate' =>  $request->ChequeDate,
            'BankId' =>  $request->BankId,
            'Payee'=>$request->Payee,
            'company_id'=>$request->company_id,
        ]);
        
       $payDetailsJson = json_decode($request->payDetails,JSON_UNESCAPED_SLASHES);
       
        $Pettycashdetails = Pettycashdetails::where(['pettycashId'=>$id])->get();
        
        foreach($Pettycashdetails as $entryData){
            $sumAmount+=$entryData->PayAmount;
        }
        
        $dataPay = [];
        
        $COndAmount = $request->CashAmount - $sumAmount;
        
        foreach($payDetailsJson as $myPayment){
            
            $payEntryId = $myPayment['payEntryId'];
            $payDate = $myPayment['PayDate'];
            $PayDetails = $myPayment['PayDetails'];
            $PayAmt = $myPayment['PayAmt'];
            $PayTo = $myPayment['PayTo'];
            $status = $myPayment['status'];
            
            if($payEntryId == '' || $payEntryId == null){
                
                if($PayAmt > $COndAmount){
                    
                 $dataPay[]="Balance Amount Not Match !";
                 $code = "203";
                 $msg = "This Amount is greater then Balanace";
                    
                }else{
                
                $code = "200";
                
                $msg="Data updated successfully!";
                
                 $dataPay[]="Insert";
                $PettyDetails = Pettycashdetails::create([
                    'pettycashId' =>  $id,
                    'payDate'=>$payDate,
                    'payDetails' =>  $PayDetails,
                    'PayAmount' => $PayAmt == null || $PayAmt == '' ? 0 : $PayAmt,
                    'PayTo' => $PayTo,
                    'status' => $status,
                ]);
                
                $pdid = $PettyDetails->id;
                
                $CompanysCash = Company::where(['id'=>$request->CompanyId])->first();
                $companyCashShort = $CompanysCash == '' || $CompanysCash == null ? '' : $CompanysCash->company_abbr;
                
                $getmonthCashVoucher = Pettycashvoucher::where(['pdid'=>$pdid])->where(DB::raw('MONTH(created_at)'),$getMonth)->get();
                
                $countCashxs = count($getmonthCashVoucher);
                if($countCashxs == null || $countCashxs == '' || $countCashxs == 0){
                $monthCountCashX = 1;
                }else{
                $monthCountCashX = $countCashxs + 1;
                }
                
                $getyearCashVoucher = Pettycashvoucher::where(['pdid'=>$pdid])->where(DB::raw('YEAR(created_at)'),$getYear)->get();
                $countCashYear = count($getyearCashVoucher);
                
                if(empty($countCashYear)){
                $yearCashCOunt = 1;
                }else{
                $yearCashCOunt = $countCashYear + 1;
                }
                
                $monthCashCount = str_pad($monthCountCashX,4,'0',STR_PAD_LEFT);
                $yearCashCount = str_pad($yearCashCOunt,4,'0',STR_PAD_LEFT);
                
                $PettyVoucherNo = $companyCashShort."/".$getYear."/".$getMonth."/".$yearCashCount."/".$monthCashCount."/".$pdid;
                
                Pettycashvoucher::create([
                'pettyId' =>  $id,
                'pdid'=>$pdid,
                'PettyVoucherNo' =>  $PettyVoucherNo,
                'VoucherDate' => date("Y-m-d"),
                'TransactionDate' => $payDate,
                'PayTo' => $PayTo,
                'VoucherAmount' => $PayAmt,
                'VoucherDesc' => $PayDetails,
                ]);
                    
                }
                
                
                
                
            }else{
                 $code = "200";
                 $msg="Data updated successfully!";
                $PettyDetails = Pettycashdetails::where("id",$payEntryId)->update([
                    'pettycashId' =>  $id,
                    'payDate'=>$payDate,
                    'payDetails' =>  $PayDetails,
                    'PayAmount' => $PayAmt,
                    'PayTo' => $PayTo,
                    'status' => $status,
                ]);
                
                 Pettycashvoucher::where("pdid",$payEntryId)->update([
                    'TransactionDate' => $payDate,
                    'PayTo' => $PayTo,
                    'VoucherAmount' => $PayAmt,
                    'VoucherDesc' => $PayDetails,
                ]);
                
            }
           
        }
        
        return response()->json([
            'message' => $msg,
            'data' => $dataPay,
            'code'=>$code
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
        
        $PettyCash = Pettycash::where(['id'=>$id])->delete();
        Pettycashdetails::where(['pettycashId'=>$id])->delete();
        Pettycashvoucher::where(['pettyId'=>$id])->delete();

        return response()->json([
            'message' => "Data deleted successfully!",
            'data'=>$PettyCash,
            'code'=>'200'
        ], 200);
    }
    
    public function destroy2(Request $request,$id='')
    {
        //$payreciepts->delete($request->all());
        
        // $PettyCash = Pettycash::where(['id'=>$id])->delete();
        Pettycashdetails::where(['id'=>$id])->delete();
        // Pettycashvoucher::where(['pettyId'=>$id])->delete();

        return response()->json([
            'message' => "Data deleted successfully!",
            'data'=>'',
            'code'=>'200'
        ], 200);
    }
    
    public function pettyVoucherResult($id=""){
         
         $response = Pettycashvoucher::where(['pdid'=>$id])->get();
         $data = $this->renderVoucherList($response);
         
          return response()->json([
            'message' => "Data deleted successfully!",
            'data'=>$data,
            'code'=>'200'
        ], 200);
        
    }
    
    
    public function renderVoucherList(Object $apiResponse)
    {
        $data = [];
        
        foreach ($apiResponse as $key => $value) {
            
            $Pettycash = Pettycash::where(['id'=>$value->pettyId])->first();
            $companyDetails = Company::where(['id'=>$Pettycash->CompanyId])->first();
            
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "pettyId"=>$value->pettyId,
                "pdid"=>$value->pdid,
                "PettyVoucherNo"=>$value->PettyVoucherNo,
                "VoucherType"=>$value->VoucherType,
                "VoucherDate"=>$value->VoucherDate,
                "TransactionDate"=>$value->TransactionDate,
                "PayTo"=>$value->PayTo,
                "VoucherAmount"=>$value->VoucherAmount,
                "VoucherDesc"=>$value->VoucherDesc,
                "companyDetails"=>$companyDetails,
                "companyId"=>$companyDetails == '' || $companyDetails == null ? '' : $companyDetails->id,
                "company_name"=>$companyDetails == '' || $companyDetails == null ? '' : $companyDetails->company_name,
                "address"=>$companyDetails == '' || $companyDetails == null ? '' : $companyDetails->address,
                "contact_number"=>$companyDetails == '' || $companyDetails == null ? '' : $companyDetails->contact_number,
            ]);
        }

        return $data;
    }
    
    // public function PaymentRecieptFilters (Request $request){
       
        
    //     $DateOfRecieptFrom = $request->DateOfRecieptFrom ?? "";
    //     $DateOfRecieptTo = $request->DateOfRecieptTo ?? "";
        
    //     $PropertyNoFrom = $request->PropertyNoFrom ?? "";
    //     $PropertyNoTo = $request->PropertyNoTo ?? "";
        
    //     $ProjectFrom = $request->ProjectFrom ?? "";
    //     $ProjectTo = $request->ProjectTo ?? "";
        
    //     $CompanyFrom = $request->CompanyFrom ?? "";
    //     $CompanyTo = $request->CompanyTo ?? "";
        
    //     $PropertyTypeFrom = $request->PropertyTypeFrom ?? "";
    //     $PropertyTypeTo = $request->PropertyTypeTo ?? "";
        
        
    //     if($DateOfRecieptFrom != '' && $DateOfRecieptTo !='' && $PropertyNoFrom == '' && $PropertyNoTo == '' && $ProjectFrom == '' && $ProjectTo == '' && $CompanyFrom == '' && $CompanyTo == '' && $PropertyTypeFrom == '' && $PropertyTypeTo == ''){
            
    //         $response = Payreciepts::whereBetween('dateOfReciept', array($DateOfRecieptFrom,$DateOfRecieptTo))->get();
    //         $count = Payreciepts::whereBetween('dateOfReciept', array($DateOfRecieptFrom,$DateOfRecieptTo))->count();
            
    //     }else if($DateOfRecieptFrom != '' && $DateOfRecieptTo !='' && $PropertyNoFrom != '' && $PropertyNoTo != '' && $ProjectFrom == '' && $ProjectTo == '' && $CompanyFrom == '' && $CompanyTo == '' && $PropertyTypeFrom == '' && $PropertyTypeTo == ''){
            
    //         $response = Payreciepts::whereBetween('dateOfReciept', array($DateOfRecieptFrom,$DateOfRecieptTo))->whereBetween('ProNo', array($PropertyNoFrom,$PropertyNoTo))->get();
    //         $count = Payreciepts::whereBetween('dateOfReciept', array($DateOfRecieptFrom,$DateOfRecieptTo))->whereBetween('ProNo', array($PropertyNoFrom,$PropertyNoTo))->count();
              
            
    //     }else if($DateOfRecieptFrom != '' && $DateOfRecieptTo !='' && $PropertyNoFrom != '' && $PropertyNoTo != '' && $ProjectFrom != '' && $ProjectTo != '' && $CompanyFrom == '' && $CompanyTo == '' && $PropertyTypeFrom == '' && $PropertyTypeTo == ''){
            
    //         $response = Payreciepts::whereBetween('dateOfReciept', array($DateOfRecieptFrom,$DateOfRecieptTo))->whereBetween('ProNo', array($PropertyNoFrom,$PropertyNoTo))->whereBetween('ProjectId', array($ProjectFrom,$ProjectTo))->get();
            
    //         $count = Payreciepts::whereBetween('dateOfReciept', array($DateOfRecieptFrom,$DateOfRecieptTo))->whereBetween('ProNo', array($PropertyNoFrom,$PropertyNoTo))->whereBetween('ProjectId', array($ProjectFrom,$ProjectTo))->count();
            
            
    //     }else if($DateOfRecieptFrom != '' && $DateOfRecieptTo !='' && $PropertyNoFrom != '' && $PropertyNoTo != '' && $ProjectFrom != '' && $ProjectTo != '' && $CompanyFrom != '' && $CompanyTo != '' && $PropertyTypeFrom == '' && $PropertyTypeTo == ''){
            
    //          $response = Payreciepts::whereBetween('dateOfReciept', array($DateOfRecieptFrom,$DateOfRecieptTo))->whereBetween('ProNo', array($PropertyNoFrom,$PropertyNoTo))->whereBetween('ProjectId', array($ProjectFrom,$ProjectTo))->whereBetween('CompanyId', array($CompanyFrom,$CompanyTo))->get();
            
    //         $count = Payreciepts::whereBetween('dateOfReciept', array($DateOfRecieptFrom,$DateOfRecieptTo))->whereBetween('ProNo', array($PropertyNoFrom,$PropertyNoTo))->whereBetween('ProjectId', array($ProjectFrom,$ProjectTo))->whereBetween('CompanyId', array($CompanyFrom,$CompanyTo))->count();
            
            
    //     }else if($DateOfRecieptFrom != '' && $DateOfRecieptTo !='' && $PropertyNoFrom != '' && $PropertyNoTo != '' && $ProjectFrom != '' && $ProjectTo != '' && $CompanyFrom != '' && $CompanyTo != '' && $PropertyTypeFrom != '' && $PropertyTypeTo != ''){
            
    //          $response = Payreciepts::whereBetween('dateOfReciept', array($DateOfRecieptFrom,$DateOfRecieptTo))->whereBetween('ProNo', array($PropertyNoFrom,$PropertyNoTo))->whereBetween('ProjectId', array($ProjectFrom,$ProjectTo))->whereBetween('CompanyId', array($CompanyFrom,$CompanyTo))->whereBetween('PropTypeId', array($PropertyTypeFrom,$PropertyTypeTo))->get();
            
    //         $count = Payreciepts::whereBetween('dateOfReciept', array($DateOfRecieptFrom,$DateOfRecieptTo))->whereBetween('ProNo', array($PropertyNoFrom,$PropertyNoTo))->whereBetween('ProjectId', array($ProjectFrom,$ProjectTo))->whereBetween('CompanyId', array($CompanyFrom,$CompanyTo))->whereBetween('PropTypeId', array($PropertyTypeFrom,$PropertyTypeTo))->count();
            
    //     }else if($DateOfRecieptFrom == '' && $DateOfRecieptTo =='' && $PropertyNoFrom == '' && $PropertyNoTo == '' && $ProjectFrom != '' && $ProjectTo != '' && $CompanyFrom == '' && $CompanyTo == '' && $PropertyTypeFrom == '' && $PropertyTypeTo == ''){
            
    //          $response = Payreciepts::whereBetween('ProjectId', array($ProjectFrom,$ProjectTo))->get();
            
    //         $count = Payreciepts::whereBetween('ProjectId', array($ProjectFrom,$ProjectTo))->count();
            
    //     }else if($DateOfRecieptFrom == '' && $DateOfRecieptTo =='' && $PropertyNoFrom == '' && $PropertyNoTo == '' && $ProjectFrom != '' && $ProjectTo != '' && $CompanyFrom != '' && $CompanyTo != '' && $PropertyTypeFrom == '' && $PropertyTypeTo == ''){
            
    //          $response = Payreciepts::whereBetween('ProjectId', array($ProjectFrom,$ProjectTo))->whereBetween('CompanyId', array($CompanyFrom,$CompanyTo))->get();
            
    //         $count = Payreciepts::whereBetween('ProjectId', array($ProjectFrom,$ProjectTo))->whereBetween('CompanyId', array($CompanyFrom,$CompanyTo))->count();
            
    //     }else if($DateOfRecieptFrom == '' && $DateOfRecieptTo =='' && $PropertyNoFrom == '' && $PropertyNoTo == '' && $ProjectFrom == '' && $ProjectTo == '' && $CompanyFrom != '' && $CompanyTo != '' && $PropertyTypeFrom == '' && $PropertyTypeTo == ''){
            
    //          $response = Payreciepts::whereBetween('CompanyId', array($CompanyFrom,$CompanyTo))->get();
            
    //         $count = Payreciepts::whereBetween('CompanyId', array($CompanyFrom,$CompanyTo))->count();
            
    //     }else if($DateOfRecieptFrom != '' && $DateOfRecieptTo !='' && $PropertyNoFrom == '' && $PropertyNoTo == '' && $ProjectFrom != '' && $ProjectTo != '' && $CompanyFrom == '' && $CompanyTo == '' && $PropertyTypeFrom == '' && $PropertyTypeTo == ''){
            
    //          $response = Payreciepts::whereBetween('dateOfReciept', array($DateOfRecieptFrom,$DateOfRecieptTo))->whereBetween('ProjectId', array($ProjectFrom,$ProjectTo))->get();
            
    //         $count = Payreciepts::whereBetween('dateOfReciept', array($DateOfRecieptFrom,$DateOfRecieptTo))->whereBetween('ProjectId', array($ProjectFrom,$ProjectTo))->count();
            
    //     }else{
            
    //         $response = Payreciepts::all();
    //         $count = Payreciepts::count();

    //     }
        
       
        
        
    //         // SUCCESS
    //         if (count($response) > 0) {
    //             $data = $this->renderList($response);
    //             return response()->json([
    //                 'code' => '200',
    //                 'success' => true,
    //                 'message' => 'Payment Reciept Filters Found',
    //                 'data' => $data,
    //                 'orderBy' => '',
    //                 'orderType' => '',
    //                 'perPage' => '',
    //                 'pageNo' => '',
    //                 'TotalCount'=>$count,
    //             ]);
    //         }

    //         // FAIL
    //         else {
    //             return app(CommonController::class)->getEmptyListResponse();
    //         }
    // }
    
    
    
}