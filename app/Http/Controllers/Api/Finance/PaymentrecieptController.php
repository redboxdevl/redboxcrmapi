<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\PaymentrecieptRequest;
use App\Models\Finance\Paymentreciept;
use App\Models\Inventory\Project;
use App\Models\HR\Company;
use App\Models\HR\Employee;
use App\Models\Inventory\Projectinventory;
use App\Models\Finance\Paymentreciptmode;


use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;

class PaymentrecieptController extends Controller
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

            $response = Paymentreciept::where(['status'=>'0'])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Paymentreciept::where(['status'=>'0'])->count();
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Payment Reciept Found',
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
            echo 'as';
            $response = Paymentreciept::orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Paymentreciept::count();
           
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Payment Reciept Found',
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
                
                $proName = Project::where(['id'=>$value->projectId])->first();
                
                $Company = Company::where(['id'=>$value->companyId])->first();
                
                $Employee = Employee::where(['id'=>$value->agentId])->first();
                
                
                $ProjectFilter = Projectinventory::where(['project_id'=>$value->projectId,'jsonFormat->property_no'=>$value->PropertyNo,'jsonFormat->block'=>$value->BlockName])->first();
                
                $ModePaymentTitle= "";
                $ModePaymentValue= "";
                
                if($value->ModeOfPayment == 'cash'){
                    $ModePaymentTitle="Cash Voucher No";
                    $ModePaymentValue = $value->cashVoucherNo;
                }else if($value->ModeOfPayment == 'Bank (Online)'){
                    $ModePaymentTitle="Bank Online Name";
                    $ModePaymentValue = $value->bankonlineName;
                }else if($value->ModeOfPayment == 'Cheque'){
                    $ModePaymentTitle="Cheque No";
                     $ModePaymentValue = $value->chequeNo;
                }else if($value->ModeOfPayment == 'Pay Order | DD'){
                    $ModePaymentTitle="Pay order No";
                    $ModePaymentValue = $value->payorderNo;
                }else if($value->ModeOfPayment == 'File (Merging)'){
                    $ModePaymentTitle="File No";
                    $ModePaymentValue = $value->fileNo;
                }
                
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "agentId" => $value->agentId,
                "emp_name" => $Employee->emp_name,
                "recieptDate" => $value->recieptDate,
                "projectId" => $value->projectId,
                "project_name" => $proName == '' || $proName == null ? '' : $proName->project_name,
                "companyId" => $value->companyId,
                "company_name" => $Company == '' || $Company == null ? '' : $Company->company_name,
                "clientName" => $value->clientName,
                "cellNo" => $value->cellNo,
                "CnicNo" => $value->CnicNo,
                "proTypeId" => $value->proTypeId,
                "BlockName" => $value->BlockName,
                "PropertyNo" => $value->PropertyNo,
                "Size" => $value->Size,
                "TotalAmount" => $value->TotalAmount,
                "DiscountAmount" => $value->DiscountAmount,
                "ModeOfPayment" => $value->ModeOfPayment,
                "ModeOfPaymentTitle" => $ModePaymentTitle,
                "ModeOfPaymentValues" => $ModePaymentValue,
                "cashAmount" => $value->cashAmount,
                "cashVoucherNo" => $value->cashVoucherNo,
                "bankonlineAmount" => $value->bankonlineAmount,
                "bankonlineName" => $value->bankonlineName,
                "chequeAmount" => $value->chequeAmount,
                "chequeNo" => $value->chequeNo,
                "ChequeDate" => $value->ChequeDate,
                "ChequeBankName" => $value->ChequeBankName,
                "payorderAmount" => $value->payorderAmount,
                "payorderNo" => $value->payorderNo,
                "payorderDate" => $value->payorderDate,
                "payorderBankName" => $value->payorderBankName,
                "fileAmount" => $value->fileAmount,
                "fileNo" => $value->fileNo,
                "status" => $value->status,
                "ProjectFilter"=>$ProjectFilter == '' || $ProjectFilter == null ? '' : json_decode($ProjectFilter->jsonFormat,JSON_UNESCAPED_SLASHES),
                    
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
    public function store(PaymentrecieptRequest $request)
    {
        
        $cashData=[];
        
        $modeOfPayment = explode(",",$request->ModeOfPayment);
        
        foreach($modeOfPayment as $payMode){
            
            if($payMode == 'cash'){
                
                array_push($cashData,[
                    'mode' => 'cash',
                    'Amount' => $request->cashAmount,
                    'VoucherNo' => $request->cashVoucherNo,
                    'bankName' => null,
                    'TransactionId' => null,
                    'chequeNo' => null,
                    'date' => null,
                    'payorderNo' => null,
                    'fileNo' => null,
                ]);
            }else if($payMode == 'Bank (Online)'){
                
                array_push($cashData,[
                    'mode' => 'Bank (Online)',
                    'Amount' => $request->bankonlineAmount,
                    'bankName' => $request->bankonlineName,
                    'TransactionId' => $request->BankTransactionId,
                    'VoucherNo' => null,
                    'chequeNo' => null,
                    'date' => null,
                    'payorderNo' => null,
                    'fileNo' => null,
                ]);
            }else if($payMode == 'Cheque'){
                
                array_push($cashData,[
                    'mode' => 'Cheque',
                    'Amount' => $request->chequeAmount,
                    'chequeNo' => $request->chequeNo,
                    'date' => $request->ChequeDate,
                    'bankName' => $request->ChequeBankName,
                    'TransactionId' => null,
                    'VoucherNo' => null,
                    'payorderNo' => null,
                    'fileNo' => null,
                ]);
            }else if($payMode == 'Pay Order | DD'){
                
                array_push($cashData,[
                    'mode' => 'Pay Order | DD',
                    'Amount' => $request->payorderAmount,
                    'payorderNo' => $request->payorderNo,
                    'date' => $request->payorderDate,
                    'bankName' => $request->payorderBankName,
                    'TransactionId' => null,
                    'VoucherNo' => null,
                    'chequeNo' => null,
                    'fileNo' => null,
                ]);
            }else if($payMode == 'File (Merging)'){
                
                array_push($cashData,[
                    'mode' => 'File (Merging)',
                    'Amount' => $request->fileAmount,
                    'fileNo' => $request->fileNo,
                    'TransactionId' => null,
                    'VoucherNo' => null,
                    'chequeNo' => null,
                    'bankName' => null,
                    'date' => null,
                    'payorderNo' => null,
                ]);
            }
            
            
            
        }
    
        $response = Paymentreciept::create([
            'agentId' => $request->agentId,
            'recieptDate' =>  $request->recieptDate,
            'RecievedAmount' =>  $request->RecievedAmount,
            'status' =>  '0',
        ]);
        
        $reciepdId = $response->id;
        
        $nsx = [];
        
        foreach($cashData as $newChas){
            
            $mode = $newChas['mode'];
            $Amount = $newChas['Amount'];
            $VoucherNo = $newChas['VoucherNo'];
            $TransactionId = $newChas['TransactionId'];
            $modedate = $newChas['date'];
            $chequeNo = $newChas['chequeNo'];
            $payorderNo = $newChas['payorderNo'];
            $fileNo = $newChas['fileNo'];
            $bankName = $newChas['bankName'];
            
            Paymentreciptmode::create([
                'planId' => $request->planId,
                'statementId' =>  $request->statementId,
                'recieptId' =>  $reciepdId,
                'mode' =>  $mode,
                'amount' =>  $Amount,
                'voucherno' => $VoucherNo,
                'bankname' =>  $bankName,
                'transactionid' => $TransactionId,
                'modeDate' =>  $modedate,
                'payorderno' =>  $payorderNo,
                'fileno' =>  $fileNo,
                'chequeNo' =>  $chequeNo,
            ]);
            
            
        }
            
            
           
        
        
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
    public function show($id="")
    {
        
        $response = Paymentreciept::where(['id'=>$id])->get();
            
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