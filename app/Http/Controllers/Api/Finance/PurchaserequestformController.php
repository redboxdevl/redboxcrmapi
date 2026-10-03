<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\PurchasereqformRequest;
use App\Models\Finance\Purchasereqform;
use App\Models\Finance\Itemdetails;
use App\Models\Finance\Repaymentschedule;
use App\Models\HR\Itemtype;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;
use DateTime;
use DB;

class PurchaserequestformController extends Controller
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
        $TypeId = $request->TypeId ?? "";
        $DepartmentId = $request->DepartmentId ?? "";
        $Status = $request->Status ?? "";
        $User = $request->User ?? "";
        $TotalCost = $request->TotalCost ?? "";
        $DisbursedAmt = $request->DisbursedAmt ?? "";
        $deptId = $request->deptId ?? "";
        $company_id = $request->company_id ?? "";
        

        if (is_null($agentId) && empty($TypeId) && empty($DepartmentId) && empty($Status) && empty($User) && empty($TotalCost) && empty($DisbursedAmt) && empty($deptId)) {
            
                $response = Purchasereqform::with(['getitemsDetails','getRepaymentSchedule','getRequestType','getDepartments','getEmployees' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
                $count = Purchasereqform::with(['getitemsDetails','getRepaymentSchedule','getRequestType','getDepartments','getEmployees' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['company_id'=>$company_id])->count();
           
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Purchase Request List Found',
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
        }else if ($agentId == 'all' && empty($TypeId) && empty($DepartmentId) && empty($Status) && empty($User) && empty($TotalCost) && empty($DisbursedAmt) && empty($deptId)) {
            
           if($Status == 0){
               
                $response = Purchasereqform::with(['getitemsDetails','getRepaymentSchedule','getRequestType','getDepartments','getEmployees' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['company_id'=>$company_id])->where('status','LIKE','%'.$Status.'%')->orderBy($orderBy, $orderType)->paginate($perPage);
                
            $count = Purchasereqform::with(['getitemsDetails','getRepaymentSchedule','getRequestType','getDepartments','getEmployees' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['company_id'=>$company_id])->where('status','LIKE','%'.$Status.'%')->count();
               
           }else{
               
                $response = Purchasereqform::with(['getitemsDetails','getRepaymentSchedule','getRequestType','getDepartments','getEmployees' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
            $count = Purchasereqform::with(['getitemsDetails','getRepaymentSchedule','getRequestType','getDepartments','getEmployees' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['company_id'=>$company_id])->count();
               
           }
           
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Purchase Request List Found',
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
            
        }else if ($agentId == 'all' && empty($TypeId) && empty($DepartmentId) && empty($Status) && empty($User) && empty($TotalCost) && empty($DisbursedAmt) && !empty($deptId)) {
            
            
            
            $response = Purchasereqform::with(['getitemsDetails','getRepaymentSchedule','getRequestType','getDepartments','getEmployees' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['company_id'=>$company_id])->where(['departmentId'=>$deptId])->orderBy($orderBy, $orderType)->paginate($perPage);
                
            $count = Purchasereqform::with(['getitemsDetails','getRepaymentSchedule','getRequestType','getDepartments','getEmployees' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['company_id'=>$company_id])->where(['departmentId'=>$deptId])->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Purchase Request List Found',
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
            
        }else if ($agentId == 'all' && !empty($TypeId) && empty($DepartmentId) && empty($Status) && empty($User) && empty($TotalCost) && empty($DisbursedAmt) && empty($deptId)) {
            
            
            
            $response = Purchasereqform::with(['getitemsDetails','getRepaymentSchedule','getRequestType','getDepartments','getEmployees' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['company_id'=>$company_id])->where(['reqtypeId'=>$TypeId])->orderBy($orderBy, $orderType)->paginate($perPage);
                
            $count = Purchasereqform::with(['getitemsDetails','getRepaymentSchedule','getRequestType','getDepartments','getEmployees' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['company_id'=>$company_id])->where(['reqtypeId'=>$TypeId])->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Purchase Request List Found',
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
            
        }else if ($agentId == 'all' && empty($TypeId) && !empty($DepartmentId) && empty($Status) && empty($User) && empty($TotalCost) && empty($DisbursedAmt) && empty($deptId)) {
            
            
            
            $response = Purchasereqform::with(['getitemsDetails','getRepaymentSchedule','getRequestType','getDepartments','getEmployees' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['company_id'=>$company_id])->where(['departmentId'=>$DepartmentId])->orderBy($orderBy, $orderType)->paginate($perPage);
                
            $count = Purchasereqform::with(['getitemsDetails','getRepaymentSchedule','getRequestType','getDepartments','getEmployees' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['company_id'=>$company_id])->where(['departmentId'=>$DepartmentId])->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Purchase Request List Found',
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
            
        }else if ($agentId == 'all' && empty($TypeId) && empty($DepartmentId) && !empty($Status) && empty($User) && empty($TotalCost) && empty($DisbursedAmt) && empty($deptId)) {
            
            
            $response = Purchasereqform::with(['getitemsDetails','getRepaymentSchedule','getRequestType','getDepartments','getEmployees' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['company_id'=>$company_id])->where('status','LIKE','%'.$Status.'%')->orderBy($orderBy, $orderType)->paginate($perPage);
                
            $count = Purchasereqform::with(['getitemsDetails','getRepaymentSchedule','getRequestType','getDepartments','getEmployees' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['company_id'=>$company_id])->where('status','LIKE','%'.$Status.'%')->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Purchase Request List Found',
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
            
        }else if ($agentId == 'all' && empty($TypeId) && empty($DepartmentId) && empty($Status) && !empty($User) && empty($TotalCost) && empty($DisbursedAmt) && empty($deptId)) {
            
            $response = Purchasereqform::with(['getitemsDetails','getRepaymentSchedule','getRequestType','getDepartments','getEmployees' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['company_id'=>$company_id])->where(['agentId'=>$User])->orderBy($orderBy, $orderType)->paginate($perPage);
                
            $count = Purchasereqform::with(['getitemsDetails','getRepaymentSchedule','getRequestType','getDepartments','getEmployees' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['company_id'=>$company_id])->where(['agentId'=>$User])->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Purchase Request List Found',
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
            
        }else if ($agentId == 'all' && empty($TypeId) && empty($DepartmentId) && empty($Status) && empty($User) && !empty($TotalCost) && empty($DisbursedAmt) && empty($deptId)) {
            
            $response = Purchasereqform::with(['getitemsDetails','getRepaymentSchedule','getRequestType','getDepartments','getEmployees' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
            $count = Purchasereqform::with(['getitemsDetails','getRepaymentSchedule','getRequestType','getDepartments','getEmployees' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['company_id'=>$company_id])->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Purchase Request List Found',
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
            
        }else if ($agentId == 'all' && empty($TypeId) && empty($DepartmentId) && empty($Status) && empty($User) && !empty($TotalCost) && empty($DisbursedAmt) && empty($deptId)) {
            
            $response = Purchasereqform::with(['getitemsDetails','getRepaymentSchedule','getRequestType','getDepartments','getEmployees' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
            $count = Purchasereqform::with(['getitemsDetails','getRepaymentSchedule','getRequestType','getDepartments','getEmployees' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['company_id'=>$company_id])->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Purchase Request List Found',
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
            
        }else if ($agentId == 'all' && empty($TypeId) && empty($DepartmentId) && empty($Status) && empty($User) && empty($TotalCost) && !empty($DisbursedAmt) && empty($deptId)) {
            
            $response = Purchasereqform::with(['getitemsDetails','getRepaymentSchedule','getRequestType','getDepartments','getEmployees' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['company_id'=>$company_id])->where('DisbursedAmt','LIKE','%'.$DisbursedAmt.'%')->orderBy($orderBy, $orderType)->paginate($perPage);
                
            $count = Purchasereqform::with(['getitemsDetails','getRepaymentSchedule','getRequestType','getDepartments','getEmployees' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['company_id'=>$company_id])->where('DisbursedAmt','LIKE','%'.$DisbursedAmt.'%')->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Purchase Request List Found',
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
            
        }else if ($agentId != 'all' && empty($TypeId) && empty($DepartmentId) && empty($Status) && empty($User) && empty($TotalCost) && empty($DisbursedAmt) && empty($deptId)) {
          
            $response = Purchasereqform::with(['getitemsDetails','getRepaymentSchedule','getRequestType','getDepartments','getEmployees' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['company_id'=>$company_id])->where(['agentId'=>$agentId])->orderBy($orderBy, $orderType)->paginate($perPage);
                
            $count = Purchasereqform::with(['getitemsDetails','getRepaymentSchedule','getRequestType','getDepartments','getEmployees' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['company_id'=>$company_id])->where(['agentId'=>$agentId])->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Purchase Request List Found',
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
                
                
            $data2 = [];
            $sumAmount = 0;
            $sumQTY = 0;
            $sumUnitCost = 0;
            foreach($value->getitemsDetails as $entryDetals){
            
                array_push($data2, (object) [
                    "ItemId" => $entryDetals->id,
                    "ItemName" => $entryDetals->itemName,
                    "ItemQty" => $entryDetals->QTy,
                    "ItemCost" => $entryDetals->UnitCost,
                    "TotalCost" => $entryDetals->itemName == '' ? $entryDetals->TotalCost : number_format($entryDetals->QTy * $entryDetals->UnitCost),
                    "status" => $entryDetals->status,
                ]);
                
                $sumAmount += $entryDetals->itemName == '' ? str_replace(",","",$entryDetals->TotalCost) : $entryDetals->QTy * $entryDetals->UnitCost;
                $sumQTY += $entryDetals->itemName == '' ? 0 : $entryDetals->QTy;
                $sumUnitCost += $entryDetals->itemName == '' ? 0 : $entryDetals->UnitCost;
               
            }
            
            
            $data3 = [];
            $sumAmount2 = 0;
            foreach($value->getRepaymentSchedule as $entryRepay){
            
                array_push($data3, (object) [
                    "ScheduleId" => $entryRepay->id,
                    "RepaymentAmount" => $entryRepay->RepaymentAmount,
                    "RepaymentDate" => $entryRepay->RepaymentDate,
                ]);
               
            }
            
            if($value->status == 0){
                $statuFie = "Pending";
            }else if($value->status == 1){
                $statuFie = "Authorized";
            }else if($value->status == 2){
                $statuFie = "Approved";
            }else if($value->status == 3){
                $statuFie = "Disbursed";
            }else if($value->status == 4){
                $statuFie = "Rejected";
            }else{
                $statuFie = "";
            }
                
                
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "agentId" => $value->agentId,
                "reqtypeId" => $value->reqtypeId,
                "dateofReq" => $value->dateofReq,
                "departmentId" => $value->departmentId,
                "Purpose" => $value->Purpose,
                "EstimatedBudget" => $value->EstimatedBudget,
                "RequiredDate" => $value->RequiredDate,
                "CurrentSalary" => $value->CurrentSalary,
                "RequestedAmount" => $value->RequestedAmount,
                "TransferFrom" => $value->TransferFrom,
                "TransferTo" => $value->TransferTo,
                "status" => $value->status,
                "statusField"=>$statuFie,
                'PriorityId'=>$value->PriorityId,
                "getitemsDetails"=>json_encode($data2),
                "data2"=>$data2,
                "getRepaymentSchedule"=>json_encode($data3),
                "data3"=>$data3,
                "typeTitle"=>$value->getRequestType == null || $value->getRequestType == '' ? '' : $value->getRequestType->typeTitle,
                "department_name"=>$value->getDepartments == null || $value->getDepartments == '' ? '' : $value->getDepartments->department_name,
                "emp_name"=>$value->getEmployees == '' || $value->getEmployees == null ? '' : $value->getEmployees->emp_name,
                'sumAmount'=>$sumAmount,
                'sumQTY'=>$sumQTY,
                'sumUnitCost'=>$sumUnitCost,
                'DisbursedAmt'=>$value->DisbursedAmt,
                'AttachementImage'=>$value->AttachementImage,

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
    public function store(PurchasereqformRequest $request)
    {
        
        
        $assetrequestIds=0;
        
        if($request->reqtypeId == 'others'){
        
            $AssetReqTypeD = Itemtype::create([
            'typeTitle' =>  $request->otherrequestTypeId,
            'status' =>  1,
            ]);
        
            $lastId = $AssetReqTypeD->id;
            $assetrequestIds = $lastId;
        
        }else{
         $assetrequestIds = $request->reqtypeId;
        }
        
        
        if($request->hasFile("AttachementImage")){
            $image = $request->file("AttachementImage");
            $ext = $image->extension();
            $image_name="requirementform-".time().'.'.$ext;
            $image->storeAs('/public/AssetImages2/',$image_name);
            $FileData2 = "public/storage/AssetImages2/".$image_name;
            
        }else{
            $FileData2="";
        }
        
        // 

        
        $response = Purchasereqform::create([
            'agentId' =>  $request->agentId,
            'reqtypeId' => $assetrequestIds,
            'dateofReq' =>  $request->dateofReq,
            'departmentId' =>  $request->departmentId,
            'Purpose' =>  $request->Purpose,
            'EstimatedBudget' =>  $request->EstimatedBudget,
            'RequiredDate' =>  $request->RequiredDate,
            'CurrentSalary' =>  $request->CurrentSalary,
            'RequestedAmount' =>  $request->RequestedAmount,
            'TransferFrom' =>  $request->TransferFrom,
            'TransferTo' =>  $request->TransferTo,
            'status' =>  $request->status,
            'PriorityId'=>$request->PriorityId,
            'AttachementImage'=>$FileData2,
            'company_id'=>$request->company_id,
        ]);
        
        $purchaseId = $response->id;
        
        $inputListDetailsJson = json_decode($request->inputList,JSON_UNESCAPED_SLASHES);
        
        if($request->inputList == null){
        }else{
            
            foreach($inputListDetailsJson as $myInputList){
        
                $ItemName = $myInputList['ItemName'];
                $ItemQty = $myInputList['ItemQty'];
                $ItemCost = $myInputList['ItemCost'];
                $TotalCost = $myInputList['TotalCost'];
                
                Itemdetails::create([
                    'purchaseReqId' =>  $purchaseId,
                    'itemName'=>$ItemName,
                    'QTy' =>  $ItemQty,
                    'UnitCost' => $ItemCost,
                    'TotalCost' => $TotalCost,
                ]);
            
            }   
        
        }
        
        $inputListScheduleDetailsJson = json_decode($request->inputListSchedule,JSON_UNESCAPED_SLASHES);
        
        if($request->inputListSchedule == null){
        }else{
            
            foreach($inputListScheduleDetailsJson as $myInputScheduleList){
        
                $RepaymentAmount = $myInputScheduleList['RepaymentAmount'];
                $RepaymentDate = $myInputScheduleList['RepaymentDate'];
               
                Repaymentschedule::create([
                    'purchaseReqId' =>  $purchaseId,
                    'RepaymentAmount'=>$RepaymentAmount,
                    'RepaymentDate' =>  $RepaymentDate,
                ]);
            
            }   
        
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
    public function show( $id='')
    {
        $response = Purchasereqform::where(['id'=>$id])->get();
            
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
    public function update(PurchasereqformRequest $request,$id="")
    {
        $PurchaseData = Purchasereqform::where(['id'=>$id])->first();
        
        $assetrequestIds=0;
        
        if($request->reqtypeId == 'others'){
        
            $AssetReqTypeD = Itemtype::create([
            'typeTitle' =>  $request->otherrequestTypeId,
            'status' =>  1,
            ]);
        
            $lastId = $AssetReqTypeD->id;
            $assetrequestIds = $lastId;
        
        }else{
         $assetrequestIds = $request->reqtypeId;
        }
        
        if($request->hasFile("AttachementImage")){
            $image = $request->file("AttachementImage");
            $ext = $image->extension();
            $image_name="requirementform-".time().'.'.$ext;
            $image->storeAs('/public/AssetImages2/',$image_name);
            $FileData2 = "public/storage/AssetImages2/".$image_name;
            
        }else{
            $FileData2=$PurchaseData->AttachementImage;
        }
        
        
        Purchasereqform::where(['id'=>$id])->update([
            'reqtypeId' => $assetrequestIds,
            'dateofReq' =>  $request->dateofReq,
            'departmentId' =>  $request->departmentId,
            'Purpose' =>  $request->Purpose,
            'EstimatedBudget' =>  $request->EstimatedBudget,
            'RequiredDate' =>  $request->RequiredDate,
            'CurrentSalary' =>  $request->CurrentSalary,
            'RequestedAmount' =>  $request->RequestedAmount,
            'TransferFrom' =>  $request->TransferFrom,
            'TransferTo' =>  $request->TransferTo,
            'status' =>  $request->status,
            'PriorityId'=>$request->PriorityId,
            'DisbursedAmt'=>$request->DisbursedAmt,
            'AttachementImage'=>$FileData2,
            'company_id'=>$request->company_id,
        ]);
        
        $inputListDetailsJson = json_decode($request->inputList,JSON_UNESCAPED_SLASHES);
        
        if($request->inputList == null){
        }else{
            
            foreach($inputListDetailsJson as $myInputList){
        
                $ItemId = $myInputList['ItemId'];
                $ItemName = $myInputList['ItemName'];
                $ItemQty = $myInputList['ItemQty'];
                $ItemCost = $myInputList['ItemCost'];
                $TotalCost = $myInputList['TotalCost'];
                
                if($ItemId == '' || $ItemId == null){
                    
                    Itemdetails::create([
                        'purchaseReqId' =>  $id,
                        'itemName'=>$ItemName,
                        'QTy' =>  $ItemQty,
                        'UnitCost' => $ItemCost,
                        'TotalCost' => $TotalCost,
                    ]);
                    
                }else{
                    
                    Itemdetails::where("id",$ItemId)->update([
                        'purchaseReqId' =>  $id,
                        'itemName'=>$ItemName,
                        'QTy' =>  $ItemQty,
                        'UnitCost' => $ItemCost,
                        'TotalCost' => $TotalCost,
                    ]);
                    
                }
                
                
            
            }   
        
        }
        
        $inputListScheduleDetailsJson = json_decode($request->inputListSchedule,JSON_UNESCAPED_SLASHES);
        
        if($request->inputListSchedule == null){
        }else{
            
            foreach($inputListScheduleDetailsJson as $myInputScheduleList){
                
                $ScheduleId = $myInputScheduleList['ScheduleId'];
                $RepaymentAmount = $myInputScheduleList['RepaymentAmount'];
                $RepaymentDate = $myInputScheduleList['RepaymentDate'];
                
                if($ScheduleId == '' || $ScheduleId == null){
                    
                    Repaymentschedule::create([
                        'purchaseReqId' =>  $id,
                        'RepaymentAmount'=>$RepaymentAmount,
                        'RepaymentDate' =>  $RepaymentDate,
                    ]);
                    
                }else{
                    
                    Repaymentschedule::where("id",$ScheduleId)->update([
                        'purchaseReqId' =>  $id,
                        'RepaymentAmount'=>$RepaymentAmount,
                        'RepaymentDate' =>  $RepaymentDate,
                    ]);
               
                }
               
            }   
        
        }
        
        
        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $request->all(),
            'code'=>'200'
        ], 200);
    }
    
    
    public function itemdetailsDelete(Request $request){
        
        $id = $request->post('ItemId');
        $ItemDetaildelet = Itemdetails::where(['id'=>$id])->delete();
        return response()->json([
            'message' => "Data deleted successfully!",
            'data'=>$ItemDetaildelet,
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