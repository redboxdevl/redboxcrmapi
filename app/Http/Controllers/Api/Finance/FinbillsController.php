<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\BillsRequest;
use App\Models\Finance\Finbills;
use App\Models\Finance\Billstype;
use App\Models\Finance\AssetRegister;
use App\Models\Finance\AssetType;
use App\Models\HR\Company;
use App\Models\HR\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Assetlocation;

use Validator;
use Mail;
use DateTime;
use DB;

class FinbillsController extends Controller
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
        
        $BillAmount = $request->BillAmount ?? "";
        $PaidAmount = $request->PaidAmount ?? "";
        $DueDate = $request->DueDate ?? "";
        $BillType = $request->BillType ?? "";
        $Beneficiary = $request->Beneficiary ?? "";
        $BillMonth = $request->BillMonth ?? "";
        $Lastpaymentdate = $request->Lastpaymentdate ?? "";
        $company_id = $request->company_id ?? "";
        
     
        if (is_null($agentId) && empty($BillAmount) && empty($PaidAmount) && empty($DueDate) && empty($BillType) && empty($Beneficiary) && empty($BillMonth) && empty($Lastpaymentdate) ) {
            
            $response = Finbills::where(['company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Finbills::where(['company_id'=>$company_id])->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Fin Bills List Found',
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
        }   else if ($agentId == 'all' && empty($BillAmount) && empty($PaidAmount) && empty($DueDate) && empty($BillType) && empty($Beneficiary) && empty($BillMonth)  && empty($Lastpaymentdate) ) {
            
           
            $response = Finbills::where(['company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Finbills::where(['company_id'=>$company_id])->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Fin Bills List Found',
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
            
        } else if ($agentId == 'all' && !empty($BillAmount) && empty($PaidAmount) && empty($DueDate) && empty($BillType) && empty($Beneficiary) && empty($BillMonth)  && empty($Lastpaymentdate) ) {
            
           
            $response = Finbills::where(['company_id'=>$company_id])->where('BillAmount','LIKE','%'.$BillAmount.'%')->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Finbills::where(['company_id'=>$company_id])->where('BillAmount','LIKE','%'.$BillAmount.'%')->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Fin Bills List Found',
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
            
        } else if ($agentId == 'all' && empty($BillAmount) && !empty($PaidAmount) && empty($DueDate) && empty($BillType) && empty($Beneficiary) && empty($BillMonth)  && empty($Lastpaymentdate) ) {
            
           
            $response = Finbills::where(['company_id'=>$company_id])->where('PaidAmount','LIKE','%'.$BillAmount.'%')->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Finbills::where(['company_id'=>$company_id])->where('PaidAmount','LIKE','%'.$BillAmount.'%')->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Fin Bills List Found',
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
            
        } else if ($agentId == 'all' && empty($BillAmount) && empty($PaidAmount) && !empty($DueDate) && empty($BillType) && empty($Beneficiary) && empty($BillMonth)  && empty($Lastpaymentdate) ) {
            
           
            $response = Finbills::where(['company_id'=>$company_id])->where(['dueDate'=>$DueDate])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Finbills::where(['company_id'=>$company_id])->where(['dueDate'=>$DueDate])->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Fin Bills List Found',
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
            
        } else if ($agentId == 'all' && empty($BillAmount) && empty($PaidAmount) && empty($DueDate) && !empty($BillType) && empty($Beneficiary) && empty($BillMonth)  && empty($Lastpaymentdate) ) {
            
           
            $response = Finbills::where(['company_id'=>$company_id])->where(['billTypeId'=>$BillType])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Finbills::where(['company_id'=>$company_id])->where(['billTypeId'=>$BillType])->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Fin Bills List Found',
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
            
        } else if ($agentId == 'all' && empty($BillAmount) && empty($PaidAmount) && empty($DueDate) && empty($BillType) && !empty($Beneficiary) && empty($BillMonth)  && empty($Lastpaymentdate) ) {
            
           
            $response = Finbills::where(['company_id'=>$company_id])->where('Beneficiary','LIKE','%'.$Beneficiary.'%')->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Finbills::where(['company_id'=>$company_id])->where('Beneficiary','LIKE','%'.$Beneficiary.'%')->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Fin Bills List Found',
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
            
        }else if ($agentId == 'all' && empty($BillAmount) && empty($PaidAmount) && empty($DueDate) && empty($BillType) && empty($Beneficiary) && !empty($BillMonth)  && empty($Lastpaymentdate) ) {
            
           
            $response = Finbills::where(['company_id'=>$company_id])->where(['BillMonth'=>$BillMonth])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Finbills::where(['company_id'=>$company_id])->where(['BillMonth'=>$BillMonth])->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Fin Bills List Found',
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
            
        }else if ($agentId == 'all' && empty($BillAmount) && empty($PaidAmount) && empty($DueDate) && empty($BillType) && empty($Beneficiary) && empty($BillMonth)  && !empty($Lastpaymentdate) ) {
            
           
            $response = Finbills::where(['company_id'=>$company_id])->where(['LastpaymentDate'=>$Lastpaymentdate])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Finbills::where(['company_id'=>$company_id])->where(['LastpaymentDate'=>$Lastpaymentdate])->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Fin Bills List Found',
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
            
            if($value->status == 0){
                $infoStatus = "Pending";
            }else if($value->status == 1){
                $infoStatus = "Paid";
            }else if($value->status == 2){
                $infoStatus = "Partial Paid";
            }else{
                $infoStatus ="Pending";
            }
            
            $Billstype = Billstype::where(['id'=>$value->billTypeId])->first();
            
            $Company = Company::where(['id'=>$value->companyId])->first();    
            
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "billId"=>$value->billId,
                "companyId"=>$value->companyId,
                "billTypeId"=>$value->billTypeId,
                "Beneficiary"=>$value->Beneficiary,
                "billDate"=>$value->billDate,
                "billDateFormat"=>date("d-m-Y",strtotime($value->billDate)),
                "billNo"=>$value->billNo,
                "dueDate"=>$value->dueDate,
                "dueDateFromat"=>date("d-m-Y",strtotime($value->dueDate)),
                "BillAmount"=>$value->BillAmount,
                "billattachement"=>$value->billattachement,
                "status"=>$value->status,
                'BillMonth'=>$value->BillMonth,
                'statusInfo'=>$infoStatus,
                'Billstype'=>$Billstype,
                "typeTitle"=>$Billstype == '' || $Billstype == null ? '' : $Billstype->typeTitle,
                'Company'=>$Company,
                'company_name'=>$Company == '' || $Company == null ? '' : $Company->company_name,
                'PaidAmount'=>$value->PaidAmount,
                'balanaceAmount'=>$value->BillAmount - $value->PaidAmount,
                'LastpaymentDate'=>$value->LastpaymentDate,
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
    public function store(BillsRequest $request)
    {
       
        $getYear = date("Y");
        $getMonth = date("m");
        
        $getmonthVoucher = Finbills::where(DB::raw('MONTH(created_at)'),$getMonth)->get();
        // $monthCountX = array_count_values($getmonthVoucher);
        
        $getyearVoucher = Finbills::where(DB::raw('YEAR(created_at)'),$getYear)->get();
        
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
        
        $voucherNOData = "BILLS"."/".$getYear."/".$getMonth."/".$monthCount."/".$yearCount;
        
        
        $otherbillTypeId=0;
        
        if($request->billTypeId == 'others'){
            
            $billTypLoca = Billstype::create([
                'typeTitle' =>  $request->otherbillTypeId,
                'status' =>  1,
            ]);
            
            $lastId = $billTypLoca->id;
            $otherbillTypeId = $lastId;
            
        }else{
            $otherbillTypeId = $request->billTypeId;
        }
        
        if($request->hasFile("AssetImage1")){
            $image = $request->file("AssetImage1");
            $ext = $image->extension();
            $image_name="FINBILLATTACHEMENT-".time().'.'.$ext;
            $image->storeAs('/public/AssetImages/',$image_name);
             $FileData = "public/storage/AssetImages/".$image_name;
            
            }else{
            $FileData="";
            }
        
        $response = Finbills::create([
            'billId' =>  $voucherNOData,
            'companyId' =>  $request->companyId,
            'billTypeId' =>  $otherbillTypeId,
            'Beneficiary' =>  $request->Beneficiary,
            'billDate' =>  $request->billDate,
            'billNo' =>  $request->billNo,
            'dueDate' =>  $request->dueDate,
            'BillAmount' => $request->BillAmount == null || $request->BillAmount == '' ? 0 : $request->BillAmount,
            'billattachement' => $FileData,
            'BillMonth'=>$request->BillMonth,
            'status'=>$request->status,
            'PaidAmount'=>$request->PaidAmount == null || $request->PaidAmount == '' ? 0 : $request->PaidAmount,
            'LastpaymentDate'=>$request->LastpaymentDate == null || $request->LastpaymentDate == '' ? '-' : $request->LastpaymentDate,
            'company_id'=>$request->company_id,
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
    public function show($id="")
    {
        $response = Finbills::where(['id'=>$id])->get();
            
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
    public function update(BillsRequest $request, $id="")
    {
        
        
        $checkFiles = Finbills::findOrFail($id);
        
        
        $otherbillTypeId=0;
        
        if($request->billTypeId == 'others'){
            
            $billTypLoca = Billstype::create([
                'typeTitle' =>  $request->otherbillTypeId,
                'status' =>  1,
            ]);
            
            $lastId = $billTypLoca->id;
            $otherbillTypeId = $lastId;
            
        }else{
            $otherbillTypeId = $request->billTypeId;
        }
        
        if($request->hasFile("AssetImage1")){
            $image = $request->file("AssetImage1");
            $ext = $image->extension();
            $image_name="FINBILLATTACHEMENT-".time().'.'.$ext;
            $image->storeAs('/public/AssetImages/',$image_name);
            $FileData = "public/storage/AssetImages/".$image_name;
        }else{
            $FileData=$checkFiles->billattachement;
        }
        
        
        $FinBillsDa = Finbills::where(['id'=>$id])->update([
            'companyId' =>  $request->companyId,
            'billTypeId' =>  $otherbillTypeId,
            'Beneficiary' =>  $request->Beneficiary,
            'billDate' =>  $request->billDate,
            'billNo' =>  $request->billNo,
            'dueDate' =>  $request->dueDate,
            'BillAmount' => $request->BillAmount == null || $request->BillAmount == '' ? 0 : $request->BillAmount,
            'billattachement' => $FileData,
            'status'=>$request->status,
            'BillMonth'=>$request->BillMonth,
            'PaidAmount'=>$request->PaidAmount == null || $request->PaidAmount == '' ? 0 : $request->PaidAmount,
            'LastpaymentDate'=>$request->LastpaymentDate == null || $request->LastpaymentDate == '' ? '-' : $request->LastpaymentDate,
            'company_id'=>$request->company_id,
        ]);
        
        
        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $FinBillsDa,
            'code'=>'200'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(BillsRequest $request, Finbills $finbills,$id="")
    {
        $finbills->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code'=>'200'
        ], 200);
    }
    
    
    public function payablebillsfilters(Request $request){
        
        $Duedatefrom = $request->Duedatefrom ?? "";
        $Duedateto = $request->Duedateto ?? "";
        
        $Billingmonthfrom = $request->Billingmonthfrom ?? "";
        $Billingmonthto = $request->Billingmonthto ?? "";
        
        $Ballanceamountfrom = $request->Ballanceamountfrom ?? "";
        $Ballanceamountto = $request->Ballanceamountto ?? "";
        
        $Billnofrom = $request->Billnofrom ?? "";
        $Billnoto = $request->Billnoto ?? "";
       

        if($Duedatefrom != '' && $Duedateto !='' && $Billingmonthfrom == '' && $Billingmonthto == '' && $Ballanceamountfrom == '' && $Ballanceamountto == '' && $Billnofrom == '' && $Billnoto == ''){
           
           $response = Finbills::whereBetween('dueDate', array($Duedatefrom,$Duedateto))->orderBy('id', 'desc')->get();
           $count = Finbills::whereBetween('dueDate', array($Duedatefrom,$Duedateto))->count();
        
        }else if($Duedatefrom == '' && $Duedateto =='' && $Billingmonthfrom != '' && $Billingmonthto !='' && $Ballanceamountfrom == '' && $Ballanceamountto == '' && $Billnofrom == '' && $Billnoto == '' ){
            
            $response = Finbills::whereBetween('BillMonth', array($Billingmonthfrom,$Billingmonthto))->orderBy('id', 'desc')->get();
           $count = Finbills::whereBetween('BillMonth', array($Billingmonthfrom,$Billingmonthto))->count();
            
        }else if($Duedatefrom == '' && $Duedateto =='' && $Billingmonthfrom == '' && $Billingmonthto =='' && $Ballanceamountfrom == '' && $Ballanceamountto == '' && $Billnofrom != '' && $Billnoto != '' ){
            
            $response = Finbills::whereBetween('billNo', array($Billnofrom,$Billnoto))->orderBy('id', 'desc')->get();
           $count = Finbills::whereBetween('billNo', array($Billnofrom,$Billnoto))->count();
            
        }else{
           
         $response = Finbills::orderBy('id','desc')->get();
         $count = Finbills::count();
                
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