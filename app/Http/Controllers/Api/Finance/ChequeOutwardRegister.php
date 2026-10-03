<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\CheckoutwardregisterRequest;
use App\Models\Finance\Checkoutwardregister;
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

class ChequeOutwardRegister extends Controller
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
        $IssueDate = $request->IssueDate ?? "";
        $ChequeDate = $request->ChequeDate ?? "";
        $ChequeNo = $request->ChequeNo ?? "";
        $ChequeStatus = $request->ChequeStatus ?? "";
        $AccountNo = $request->AccountNo ?? "";
        $ChequeAmount = $request->ChequeAmount ?? "";
        $Title = $request->Title ?? "";
        $company_id = $request->company_id ?? "";
        

        if (is_null($agentId)) {
            
            if($IssueDate != ''){
                $response = Checkoutwardregister::where(['issueDate'=>$IssueDate,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = Checkoutwardregister::where(['issueDate'=>$IssueDate,'company_id'=>$company_id])->count();
            }else if($ChequeDate != ''){
                $response = Checkoutwardregister::where(['chequeDate'=>$ChequeDate,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = Checkoutwardregister::where(['chequeDate'=>$ChequeDate,'company_id'=>$company_id])->count();
            }else if($ChequeNo != ''){
                $response = Checkoutwardregister::where(['chequeNoId'=>$ChequeNo,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = Checkoutwardregister::where(['chequeNoId'=>$ChequeNo,'company_id'=>$company_id])->count();
            }else if($ChequeStatus != ''){
                
                if($AccountNo !='' && $ChequeStatus !=''){
                    
                    $response = Checkoutwardregister::join('ChequeList', 'ChequeList.id', '=', 'ChequeOutwardRegister.chequeNoId')->where('ChequeList.cheqStatus', 'LIKE', '%'.$ChequeStatus.'%')->where(['accountId'=>$AccountNo,'company_id'=>$company_id])->select('ChequeOutwardRegister.*')->paginate($perPage);
                
                    $count = Checkoutwardregister::join('ChequeList', 'ChequeList.id', '=', 'ChequeOutwardRegister.chequeNoId')->where('ChequeList.cheqStatus', 'LIKE', '%'.$ChequeStatus.'%')->select('ChequeOutwardRegister.*')->where(['accountId'=>$AccountNo,'company_id'=>$company_id])->count();
                    
                }else if($Title !='' && $ChequeStatus !=''){
                    
                    $response = Checkoutwardregister::join('ChequeList', 'ChequeList.id', '=', 'ChequeOutwardRegister.chequeNoId')->where('ChequeList.cheqStatus', 'LIKE', '%'.$ChequeStatus.'%')->join('Banklist', 'Banklist.id', '=', 'ChequeOutwardRegister.accountId')->where('ChequeList.cheqStatus', 'LIKE', '%'.$ChequeStatus.'%')->where('Banklist.title', 'LIKE', '%'.$Title.'%')->where(['company_id'=>$company_id])->select('ChequeOutwardRegister.*')->paginate($perPage);
                    
                    $count = Checkoutwardregister::join('ChequeList', 'ChequeList.id', '=', 'ChequeOutwardRegister.chequeNoId')->where('ChequeList.cheqStatus', 'LIKE', '%'.$ChequeStatus.'%')->join('Banklist', 'Banklist.id', '=', 'ChequeOutwardRegister.accountId')->where('ChequeList.cheqStatus', 'LIKE', '%'.$ChequeStatus.'%')->where('Banklist.title', 'LIKE', '%'.$Title.'%')->where(['company_id'=>$company_id])->select('ChequeOutwardRegister.*')->count();
                    
                }else{
                    
                    $response = Checkoutwardregister::join('ChequeList', 'ChequeList.id', '=', 'ChequeOutwardRegister.chequeNoId')->where('ChequeList.cheqStatus', 'LIKE', '%'.$ChequeStatus.'%')->where(['company_id'=>$company_id])->paginate($perPage);
                
                    $count = Checkoutwardregister::join('ChequeList', 'ChequeList.id', '=', 'ChequeOutwardRegister.chequeNoId')->where('ChequeList.cheqStatus', 'LIKE', '%'.$ChequeStatus.'%')->where(['company_id'=>$company_id])->count();
                    
                }
                
                
                
            }else if($AccountNo != ''){
                $response = Checkoutwardregister::where(['accountId'=>$AccountNo,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = Checkoutwardregister::where(['accountId'=>$AccountNo,'company_id'=>$company_id])->count();
            }else if($ChequeAmount != ''){
                $response = Checkoutwardregister::where(['chequeAmount'=>$ChequeAmount,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = Checkoutwardregister::where(['chequeAmount'=>$ChequeAmount,'company_id'=>$company_id])->count();
            }else if($Title != ''){
                     
                
                    $response = Checkoutwardregister::join('Banklist', 'Banklist.id', '=', 'ChequeOutwardRegister.accountId')->where('Banklist.title', 'LIKE', '%'.$Title.'%')->where(['company_id'=>$company_id])->paginate($perPage);
                    
                    $count = Checkoutwardregister::join('Banklist', 'Banklist.id', '=', 'ChequeOutwardRegister.accountId')->where('Banklist.title', 'LIKE', '%'.$Title.'%')->where(['company_id'=>$company_id])->count();
               
                
            }else{
                $response = Checkoutwardregister::where(['company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = Checkoutwardregister::where(['company_id'=>$company_id])->count();
            }

           
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Cheque Outward Register List Found',
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
            
            $response = Checkoutwardregister::where(['company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
            $count = Checkoutwardregister::where(['company_id'=>$company_id])->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Cheque Outward Register List Found',
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
        
        $MyDate="";

        foreach ($apiResponse as $key => $value) {
                
            $Banklist = Banklist::where(['id'=>$value->accountId])->first();
            $Chequelist = Chequelist::where(['id'=>$value->chequeNoId])->first();
            
            if(empty($Chequelist)){
                $statuSx="";
                $MyDate="";
            }else{
                
                $statuSx = $Chequelist->cheqStatus;
                if($statuSx == 'Released'){
                    $MyDate = "";
                }else if($statuSx == 'Cancelled'){
                     $MyDate = $value->cancelledDate;
                }else if($statuSx == 'Cleared'){
                     $MyDate = $value->clearedDate;
                }else if($statuSx == 'Hold'){
                     $MyDate = $value->holdDate;
                }else if($statuSx == 'Stop'){
                     $MyDate = $value->stopDate;
                }
                
            }
            
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "issueDate" => $value->issueDate,
                "chequeDate" => $value->chequeDate,
                "issueDateC" => date("d M Y",strtotime($value->issueDate)),
                "chequeDateC" => date("d M Y",strtotime($value->chequeDate)),
                "chequeAmount" => $value->chequeAmount,
                "ChequeAmountInWord" => $value->ChequeAmountInWord,
                "chequeNoId" => $value->chequeNoId,
                "accountId" => $value->accountId,
                "Purpose" => $value->Purpose,
                "IssuedTo" => $value->IssuedTo,
                "clearedDate" =>$MyDate,
                "stopDate" => $value->stopDate,
                "stopReason" => $value->stopReason,
                "cancelledDate" => $value->cancelledDate,
                "cancelledReason" => $value->cancelledReason,
                "holdDate" => $value->holdDate,
                "holdReason" => $value->holdReason,
                "Banklist"=>$Banklist,
                "Chequelist"=>$Chequelist,
                'voucherNo'=>$value->voucherNo,
                "currentDate" => date("d M Y",strtotime($value->created_at)),
                
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
    public function store(CheckoutwardregisterRequest $request)
    {
        
        $bankInfo = Banklist::where(['id'=>$request->accountId])->first();
        
        $bankShort = $bankInfo == '' || $bankInfo == null ? '' : $bankInfo->ShortTitle;
        $getYear = date("Y");
        $getMonth = date("m");
        
        $getmonthVoucher = Checkoutwardregister::where(['accountId'=>$request->accountId,'company_id'=>$request->company_id])->where(DB::raw('MONTH(created_at)'),$getMonth)->get();
        // $monthCountX = array_count_values($getmonthVoucher);
        
        $getyearVoucher = Checkoutwardregister::where(['accountId'=>$request->accountId,'company_id'=>$request->company_id])->where(DB::raw('YEAR(created_at)'),$getYear)->get();
        
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
        
        $voucherNOData = $bankShort."/"."BPV"."/".$getYear."/".$getMonth."/".$monthCount."/".$yearCount;
        
        
        $response = Checkoutwardregister::create([
            'chequeNoId' =>  $request->chequeNoId,
            'voucherNo' => $voucherNOData,
            'accountId' =>  $request->accountId,
            'issueDate' =>  $request->issueDate,
            'chequeDate' =>  $request->chequeDate,
            'chequeAmount' =>  $request->chequeAmount,
            'ChequeAmountInWord' =>  $request->ChequeAmountInWord,
            'Purpose' =>  $request->Purpose,
            'IssuedTo' =>  $request->IssuedTo,
            'company_id'=>$request->company_id
        ]);
        
        $outwardId = $response->id;
        
        Outwardvoucher::create([
            'outwardId' =>  $outwardId,
            'VoucherNo' => $voucherNOData,
            'VoucherDate' =>  date("Y-m-d"),
            'IssueDate' =>  $request->issueDate,
            'IssueTo' =>  $request->IssuedTo,
            'ChequeDate' =>  $request->chequeDate,
            'ChequeNo' =>  $request->chequeNoId,
            'BankName' =>  $request->accountId,
            'Amount' =>  $request->chequeAmount,
        ]);

        
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
    public function show(Checkoutwardregister $checkoutwardregister, $id='')
    {
        $response = Checkoutwardregister::where(['id'=>$id])->get();
            
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
    public function update(CheckoutwardregisterRequest $request, Checkoutwardregister $checkoutwardregister,$id="")
    {
        $checkWardData = Checkoutwardregister::where(['id'=>$id,'company_id'=>$request->company_id])->get();
        $checkWard = Checkoutwardregister::where(['id'=>$id])->update([
            'chequeNoId' =>  $request->chequeNoId,
            'accountId' =>  $request->accountId,
            'issueDate' =>  $request->issueDate,
            'chequeDate' =>  $request->chequeDate,
            'chequeAmount' =>  $request->chequeAmount,
            'ChequeAmountInWord' =>  $request->ChequeAmountInWord,
            'Purpose' =>  $request->Purpose,
            'IssuedTo' =>  $request->IssuedTo,
            'clearedDate' =>  $request->clearedDate == '' || $request->clearedDate == '0000-00-00' ? null : $request->clearedDate,
            'stopDate' =>  $request->stopDate,
            'stopReason' =>  $request->stopReason,
            'cancelledDate' =>  $request->cancelledDate,
            'cancelledReason' =>  $request->cancelledReason,
            'holdDate' =>  $request->holdDate,
            'holdReason' =>  $request->holdReason,
            'company_id'=>$request->company_id
        ]);
        
        $chequeList = Chequelist::where('id','=',$request->chequeNoId)->first();
        $chequeList->cheqStatus = $request->cheqStatus;
        $chequeList->update();
      
        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $checkWardData,
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
            $checkoutwardregister = Checkoutwardregister::where(['id'=>$id])->delete();
        return response()->json([
            'message' => "Data deleted successfully!",
            'data'=>$checkoutwardregister,
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