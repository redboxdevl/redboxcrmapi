<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\BankDepositRequest;
use App\Models\Finance\Bankdeposit;
use App\Models\Finance\Banklist;
use App\Models\Finance\Chequelist;
use App\Models\Finance\Outwardvoucher;


use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;
use DateTime;
use DB;

class BankDepositController extends Controller
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
     
        if (!empty($agentId)) {
            
            $response = Bankdeposit::with(['getAccountdetails','getcompanyDetails' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Bankdeposit::where(['company_id'=>$company_id])->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Bank Deposit List Found',
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
                "depositDate"=>$value->depositDate,
                "depositAmount"=>$value->depositAmount,
                "modeofdeposit"=>$value->modeofdeposit,
                "transactionDetails"=>$value->transactionDetails,
                "recievedFrom"=>$value->recievedFrom,
                "bankdepositId"=>$value->bankdepositId,
                "referenceNo"=>$value->referenceNo,
                "accountId"=>$value->accountId,
                "amountinwords"=>$value->amountinwords,
                "getAccountdetails"=>$value->getAccountdetails,
                'depositAmountConv' =>  number_format($value->depositAmount),
                "CompanyId"=>$value->CompanyId,
                "ChequeNo"=>$value->ChequeNo,
                "ChequeDate"=>$value->ChequeDate,
                "BankId"=>$value->BankId,
                "getcompanyDetails"=>$value->getcompanyDetails,
                // "issueDate" => $value->issueDate,
                // "chequeDate" => $value->chequeDate,
                // "issueDateC" => date("d M Y",strtotime($value->issueDate)),
                // "chequeDateC" => date("d M Y",strtotime($value->chequeDate)),
                // "chequeAmount" => $value->chequeAmount,
                // "ChequeAmountInWord" => $value->ChequeAmountInWord,
                // "chequeNoId" => $value->chequeNoId,
                // "accountId" => $value->accountId,
                // "Purpose" => $value->Purpose,
                // "IssuedTo" => $value->IssuedTo,
                // "clearedDate" =>$MyDate,
                // "stopDate" => $value->stopDate,
                // "stopReason" => $value->stopReason,
                // "cancelledDate" => $value->cancelledDate,
                // "cancelledReason" => $value->cancelledReason,
                // "holdDate" => $value->holdDate,
                // "holdReason" => $value->holdReason,
                // "Banklist"=>$Banklist,
                // "Chequelist"=>$Chequelist,
                // 'voucherNo'=>$value->voucherNo,
                "currentDate" => date("d M Y",strtotime($value->created_at)),
                "status" =>$value->status,
                "statusTitle" => $value->status == 0 ? 'Pending' : 'Approved',
                
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
    public function store(BankDepositRequest $request)
    {
        
        $bankInfo = Banklist::where(['id'=>$request->accountId])->first();
        
        $bankShort = $bankInfo == '' || $bankInfo == null ? '' : $bankInfo->ShortTitle;
        $getYear = date("Y");
        $getMonth = date("m");
        
        $getmonthVoucher = Bankdeposit::where(['accountId'=>$request->accountId])->where(DB::raw('MONTH(created_at)'),$getMonth)->get();
        // $monthCountX = array_count_values($getmonthVoucher);
        
        $getyearVoucher = Bankdeposit::where(['accountId'=>$request->accountId])->where(DB::raw('YEAR(created_at)'),$getYear)->get();
        
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
        
        $voucherNOData = $bankShort."/"."BRV"."/".$getYear."/".$getMonth."/".$monthCount."/".$yearCount;
        
        
        $response = Bankdeposit::create([
            'bankdepositId' =>  $voucherNOData,
            'accountId' => $request->accountId,
            'depositDate' =>  $request->depositDate,
            'depositAmount' =>  $request->depositAmount,
            'referenceNo' =>  $request->referenceNo,
            'modeofdeposit' =>  $request->modeofdeposit,
            'amountinwords' =>  $request->amountinwords,
            'transactionDetails' =>  $request->transactionDetails,
            'recievedFrom' =>  $request->recievedFrom,
            'CompanyId' =>  $request->CompanyId,
            'ChequeNo' =>  $request->ChequeNo,
            'ChequeDate' =>  $request->ChequeDate == null ? date("Y-m-d") : $request->ChequeDate,
            'BankId' =>  $request->BankId,
            'status' =>  $request->status,
            'company_id' => $request->company_id,
        ]);
        
        // $outwardId = $response->id;
        
        // Outwardvoucher::create([
        //     'outwardId' =>  $outwardId,
        //     'VoucherNo' => $voucherNOData,
        //     'VoucherDate' =>  date("Y-m-d"),
        //     'IssueDate' =>  $request->issueDate,
        //     'IssueTo' =>  $request->IssuedTo,
        //     'ChequeDate' =>  $request->chequeDate,
        //     'ChequeNo' =>  $request->chequeNoId,
        //     'BankName' =>  $request->accountId,
        //     'Amount' =>  $request->chequeAmount,
        // ]);

        return response()->json([
            'message' => "Data saved successfully!",
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
    public function show($id='')
    {
        $response = Bankdeposit::where(['id'=>$id])->get();
            
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
    public function update(BankDepositRequest $request,$id="")
    {
        $Bankdeposit = Bankdeposit::where(['id'=>$id])->get();
       
        Bankdeposit::where(['id'=>$id])->update([
            'accountId' => $request->accountId,
            'depositDate' =>  $request->depositDate,
            'depositAmount' =>  $request->depositAmount,
            'referenceNo' =>  $request->referenceNo,
            'modeofdeposit' =>  $request->modeofdeposit,
            'amountinwords' =>  $request->amountinwords,
            'transactionDetails' =>  $request->transactionDetails,
            'recievedFrom' =>  $request->recievedFrom,
            'status' =>  $request->status,
            'CompanyId' =>  $request->CompanyId,
            'ChequeNo' =>  $request->ChequeNo,
            'ChequeDate' =>  $request->ChequeDate,
            'BankId' =>  $request->BankId,
            'company_id' => $request->company_id,
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
            $Bankdeposit = Bankdeposit::where(['id'=>$id])->delete();
        return response()->json([
            'message' => "Data deleted successfully!",
            'data'=>$Bankdeposit,
            'code'=>'200'
        ], 200);
    }
    
    
    public function ChequeoutwardFilters (Request $request){
        
        $agentId = $request->agentId;
        
        $IssueDateFrom = $request->IssueDateFrom ?? "";
        $IssueDateTo = $request->IssueDateTo ?? "";
        
        $ChequeDateFrom = $request->ChequeDateFrom ?? "";
        $ChequeeDateTo = $request->ChequeeDateTo ?? "";
        
        $AccountNoFrom = $request->AccountNoFrom ?? "";
        $AccountNoTo = $request->AccountNoTo ?? "";
        
        $ChequeNoFrom = $request->ChequeNoFrom ?? "";
        $ChequeNoTo = $request->ChequeNoTo ?? "";
        
        $StatusFrom = $request->StatusFrom ?? "";
        $StatusTo = $request->StatusTo ?? "";
        
        
        if (is_null($agentId) && $agentId == '') {

           
            // if($IssueDateFrom != '' && $IssueDateTo !=''){
                
            //     $response = Checkoutwardregister::whereBetween('issueDate', array($IssueDateFrom,$IssueDateTo))->get();
            //     $count = Checkoutwardregister::whereBetween('issueDate', array($IssueDateFrom,$IssueDateTo))->count();
            
            // }else if($ChequeDateFrom != '' && $ChequeeDateTo !=''){
                
            //     $response = Checkoutwardregister::whereBetween('chequeDate', array($ChequeDateFrom,$ChequeeDateTo))->get();
            //     $count = Checkoutwardregister::whereBetween('chequeDate', array($ChequeDateFrom,$ChequeeDateTo))->count();
            
            // }else if($AccountNoFrom != '' && $AccountNoTo !=''){
                
            //   if($AccountNoFrom != '' && $AccountNoTo !='' && $StatusFrom != '' && $StatusTo !=''){
                
            //         $response = Checkoutwardregister::join('ChequeList', 'ChequeList.id', '=', 'ChequeOutwardRegister.chequeNoId')->whereBetween('ChequeList.cheqStatus', array($StatusFrom,$StatusTo))->whereBetween('accountId', array($AccountNoFrom,$AccountNoTo))->get();
            //         $count = Checkoutwardregister::join('ChequeList', 'ChequeList.id', '=', 'ChequeOutwardRegister.chequeNoId')->whereBetween('ChequeList.cheqStatus', array($StatusFrom,$StatusTo))->whereBetween('accountId', array($AccountNoFrom,$AccountNoTo))->count();
                
            //     }else{
            //          $response = Checkoutwardregister::whereBetween('accountId', array($AccountNoFrom,$AccountNoTo))->get();
            //         $count = Checkoutwardregister::whereBetween('accountId', array($AccountNoFrom,$AccountNoTo))->count();
            //     }
                
               
                
                
                
            // }else if($StatusFrom != '' && $StatusTo !=''){
                
            //     $response = Checkoutwardregister::join('ChequeList', 'ChequeList.id', '=', 'ChequeOutwardRegister.chequeNoId')->whereBetween('ChequeList.cheqStatus', array($StatusFrom,$StatusTo))->get();
                
            //     $count = Checkoutwardregister::join('ChequeList', 'ChequeList.id', '=', 'ChequeOutwardRegister.chequeNoId')->whereBetween('ChequeList.cheqStatus', array($StatusFrom,$StatusTo))->count();
                
            
            // }else{
               
            //     $response = Checkoutwardregister::orderBy('chequeDate','asc')->get();
            //     $count = Checkoutwardregister::count();
            // }
            
             if($IssueDateFrom !='' && $IssueDateTo !='' && $ChequeDateFrom == '' && $ChequeeDateTo == '' && $AccountNoFrom == '' && $AccountNoTo == '' && $ChequeNoFrom == '' && $ChequeNoTo == '' && $StatusFrom == '' && $StatusTo == ''){
        
                $response = Checkoutwardregister::whereBetween('issueDate', array($IssueDateFrom,$IssueDateTo))->get();
                $count = Checkoutwardregister::whereBetween('issueDate', array($IssueDateFrom,$IssueDateTo))->count();
        
            }else if($IssueDateFrom =='' && $IssueDateTo =='' && $ChequeDateFrom != '' && $ChequeeDateTo != '' && $AccountNoFrom == '' && $AccountNoTo == '' && $ChequeNoFrom == '' && $ChequeNoTo == '' && $StatusFrom == '' && $StatusTo == ''){
                
                $response = Checkoutwardregister::whereBetween('chequeDate', array($ChequeDateFrom,$ChequeeDateTo))->get();
                $count = Checkoutwardregister::whereBetween('chequeDate', array($ChequeDateFrom,$ChequeeDateTo))->count();
                
            }else if($IssueDateFrom =='' && $IssueDateTo =='' && $ChequeDateFrom == '' && $ChequeeDateTo == '' && $AccountNoFrom != '' && $AccountNoTo != '' && $ChequeNoFrom == '' && $ChequeNoTo == '' && $StatusFrom == '' && $StatusTo == ''){
                
                $response = Checkoutwardregister::whereBetween('accountId', array($AccountNoFrom,$AccountNoTo))->get();
                $count = Checkoutwardregister::whereBetween('accountId', array($AccountNoFrom,$AccountNoTo))->count();
                
                
            }else if($IssueDateFrom =='' && $IssueDateTo =='' && $ChequeDateFrom == '' && $ChequeeDateTo == '' && $AccountNoFrom != '' && $AccountNoTo != '' && $ChequeNoFrom != '' && $ChequeNoTo != '' && $StatusFrom == '' && $StatusTo == ''){
                
                $response = Checkoutwardregister::join('ChequeList', 'ChequeList.chequeId', '=', 'ChequeOutwardRegister.chequeNoId')->whereBetween('ChequeList.id', array($ChequeNoTo,$ChequeNoTo))->whereBetween('accountId',array($AccountNoFrom,$AccountNoTo))->get();
                
                $count = Checkoutwardregister::join('ChequeList', 'ChequeList.chequeId', '=', 'ChequeOutwardRegister.chequeNoId')->whereBetween('accountId', array($AccountNoFrom,$AccountNoTo))->whereBetween('ChequeOutwardRegister.chequeNoId', array($ChequeNoFrom,$ChequeNoTo))->count();
                
            }else if($IssueDateFrom =='' && $IssueDateTo =='' && $ChequeDateFrom == '' && $ChequeeDateTo == '' && $AccountNoFrom == '' && $AccountNoTo == '' && $ChequeNoFrom == '' && $ChequeNoTo == '' && $StatusFrom != '' && $StatusTo != ''){
                
                $response = Checkoutwardregister::join('ChequeList', 'ChequeList.id', '=', 'ChequeOutwardRegister.chequeNoId')->whereBetween('ChequeList.cheqStatus', array($StatusFrom,$StatusTo))->get();
                $count = Checkoutwardregister::join('ChequeList', 'ChequeList.id', '=', 'ChequeOutwardRegister.chequeNoId')->whereBetween('ChequeList.cheqStatus', array($StatusFrom,$StatusTo))->count();
                
            }else if($IssueDateFrom =='' && $IssueDateTo =='' && $ChequeDateFrom == '' && $ChequeeDateTo == '' && $AccountNoFrom != '' && $AccountNoTo != '' && $ChequeNoFrom == '' && $ChequeNoTo == '' && $StatusFrom != '' && $StatusTo != ''){
                
                $response = Checkoutwardregister::join('ChequeList', 'ChequeList.id', '=', 'ChequeOutwardRegister.chequeNoId')->whereBetween('accountId', array($AccountNoFrom,$AccountNoTo))->whereBetween('ChequeList.cheqStatus', array($StatusFrom,$StatusTo))->get();
                $count = Checkoutwardregister::join('ChequeList', 'ChequeList.id', '=', 'ChequeOutwardRegister.chequeNoId')->whereBetween('accountId', array($AccountNoFrom,$AccountNoTo))->whereBetween('ChequeList.cheqStatus', array($StatusFrom,$StatusTo))->count();
                
            }else if($IssueDateFrom =='' && $IssueDateTo =='' && $ChequeDateFrom != '' && $ChequeeDateTo != '' && $AccountNoFrom != '' && $AccountNoTo != '' && $ChequeNoFrom == '' && $ChequeNoTo == '' && $StatusFrom != '' && $StatusTo != ''){
                
                $response = Checkoutwardregister::join('ChequeList', 'ChequeList.id', '=', 'ChequeOutwardRegister.chequeNoId')->whereBetween('chequeDate', array($ChequeDateFrom,$ChequeeDateTo))->whereBetween('accountId', array($AccountNoFrom,$AccountNoTo))->whereBetween('ChequeList.cheqStatus', array($StatusFrom,$StatusTo))->get();
                $count = Checkoutwardregister::join('ChequeList', 'ChequeList.id', '=', 'ChequeOutwardRegister.chequeNoId')->whereBetween('chequeDate', array($ChequeDateFrom,$ChequeeDateTo))->whereBetween('accountId', array($AccountNoFrom,$AccountNoTo))->whereBetween('ChequeList.cheqStatus', array($StatusFrom,$StatusTo))->count();
                
            }else if($IssueDateFrom =='' && $IssueDateTo =='' && $ChequeDateFrom != '' && $ChequeeDateTo != '' && $AccountNoFrom != '' && $AccountNoTo != '' && $ChequeNoFrom == '' && $ChequeNoTo == '' && $StatusFrom == '' && $StatusTo == ''){
                
                $response = Checkoutwardregister::join('ChequeList', 'ChequeList.id', '=', 'ChequeOutwardRegister.chequeNoId')->whereBetween('chequeDate', array($ChequeDateFrom,$ChequeeDateTo))->whereBetween('accountId', array($AccountNoFrom,$AccountNoTo))->get();
                $count = Checkoutwardregister::join('ChequeList', 'ChequeList.id', '=', 'ChequeOutwardRegister.chequeNoId')->whereBetween('chequeDate', array($ChequeDateFrom,$ChequeeDateTo))->whereBetween('accountId', array($AccountNoFrom,$AccountNoTo))->count();
                
            }else if($IssueDateFrom =='' && $IssueDateTo =='' && $ChequeDateFrom != '' && $ChequeeDateTo != '' && $AccountNoFrom == '' && $AccountNoTo == '' && $ChequeNoFrom == '' && $ChequeNoTo == '' && $StatusFrom != '' && $StatusTo != ''){
                
                $response = Checkoutwardregister::join('ChequeList', 'ChequeList.id', '=', 'ChequeOutwardRegister.chequeNoId')->whereBetween('chequeDate', array($ChequeDateFrom,$ChequeeDateTo))->whereBetween('ChequeList.cheqStatus', array($StatusFrom,$StatusTo))->get();
                $count = Checkoutwardregister::join('ChequeList', 'ChequeList.id', '=', 'ChequeOutwardRegister.chequeNoId')->whereBetween('chequeDate', array($ChequeDateFrom,$ChequeeDateTo))->whereBetween('ChequeList.cheqStatus', array($StatusFrom,$StatusTo))->count();
                
            }else{
                $response = Checkoutwardregister::orderBy('chequeDate','asc')->get();
                $count = Checkoutwardregister::count();
            }
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Cheque Outward Filters Found',
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

        // AGENT ID NOT SELECTED
        else {
            return app(CommonController::class)->getExceptionResponse("Agent Id is required");
        }
        
    }
  
    
}