<?php

namespace App\Http\Controllers\Api\Inventory;


use App\Http\Requests\Inventory\InventorySoldRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Models\Inventory\Inventorysold;
use App\Models\Inventory\Projectinventory;
use App\Models\Inventory\Installmentplan;
use App\Models\AffiliateProgram\AffiliateLeadsModule;
use App\Models\ListMyLead;
use App\Models\Inventory\Project;
use App\Models\Inventory\Projectasset;
use App\Models\Inventory\InventoryCancelled;
use App\Models\Finance\Occupationtbl;
use App\Exports\ExportSold;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class InventorySoldController extends Controller
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
        $perPage = $request->perPage ?? "10";
        $page = $request->page ?? "1";
        
        $agentId = $request->agentId;
        $PlotNo = $request->PlotNo ?? "";
        $BlockName = $request->BlockName ?? "";
        $Size = $request->Size ?? "";
        $Project = $request->Project ?? "";
        $Category = $request->Category ?? "";
        $cusid = $request->cusid ?? "";
        $InvStatus = $request->InvStatus ?? "";
        $teamObj = $request->teamObj ?? "";
        $company_id = $request->company_id ?? "";

        if (!empty($agentId) && $agentId !='all' && empty($cusid) && empty($teamObj)) {
        
        $response = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['employee_id'=>$agentId,'companyId'=>$company_id])->whereNotIn('status',['Cancelled'])->orderBy($orderBy, $orderType)->paginate($perPage);
        $count = Inventorysold::where(['employee_id'=>$agentId,'companyId'=>$company_id])->whereNotIn('status',['Cancelled'])->count();
        
        // SUCCESS
        if (count($response) > 0) {
            $data = $this->renderList($response);

            return response()->json([
                'code' => '200',
                'success' => true,
                'message' => 'Record Found',
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
        
        
        }else if ($agentId == 'all' && empty($cusid) && empty($teamObj)) {
       
        $response = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
            
        $count = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->count();
        
        // SUCCESS
        if (count($response) > 0) {
            $data = $this->renderList($response);

            return response()->json([
                'code' => '200',
                'success' => true,
                'message' => 'Record Found',
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
        
        
        }else if ($agentId == 'all' && empty($cusid) && !empty($teamObj)) {
         
            $myArray2 = explode(',', $teamObj);
            $myArray = str_replace(array('[',']'),'',$myArray2);
       
        $response = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->whereIn('employee_id',$myArray)->where(['companyId'=>$company_id])->whereNotIn('status',['Cancelled'])->orderBy($orderBy, $orderType)->paginate($perPage);
            
        $count = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->whereIn('employee_id',$myArray)->where(['companyId'=>$company_id])->whereNotIn('status',['Cancelled'])->count();
        
        // SUCCESS
        if (count($response) > 0) {
            $data = $this->renderList($response);

            return response()->json([
                'code' => '200',
                'success' => true,
                'message' => 'Record Found',
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
        
        
        }else if (!empty($cusid) && empty($agentId) && empty($teamObj)) {

        $response = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['customer_id'=>$cusid])->where(['companyId'=>$company_id])->whereNotIn('status',['Cancelled'])->orderBy($orderBy, $orderType)->paginate($perPage);
            
        $count = Inventorysold::where(['customer_id'=>$cusid])->where(['companyId'=>$company_id])->whereNotIn('status',['Cancelled'])->count();
        
        // SUCCESS
        if (count($response) > 0) {
            $data = $this->renderList($response);

            return response()->json([
                'code' => '200',
                'success' => true,
                'message' => 'Record Found',
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
        
        
        }else{
            
            //  $response = Projectinventory::with(['Holdinventories','ProjectData' => function ($query){
            //     $query->orderBy('id', 'desc')->get();
            // } ,])->where('project_id',$proid)->where('jsonFormat->property_no', $q)
            //     ->get();
            
            if($PlotNo !='' && $BlockName == '' && $Size == '' && $Project == '' && $Category == '' && $InvStatus == ''){
                
                 $response = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($PlotNo) {
                    $q->where('jsonFormat->property_no', $PlotNo);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
                $count = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($PlotNo) {
                    $q->where('jsonFormat->property_no', $PlotNo);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->count();
                
            }else if($PlotNo == '' && $BlockName != '' && $Size == '' && $Project == '' && $Category == '' && $InvStatus == ''){
                
                $response = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($BlockName) {
                    $q->where('jsonFormat->block', $BlockName);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
                $count = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($BlockName) {
                    $q->where('jsonFormat->block', $BlockName);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->count();
                
            }else if($PlotNo == '' && $BlockName == '' && $Size != '' && $Project == '' && $Category == '' && $InvStatus == ''){
                
                $response = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($Size) {
                    $q->where('jsonFormat->size', $Size);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
                $count = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($Size) {
                    $q->where('jsonFormat->size', $Size);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->count();
                
            }else if($PlotNo == '' && $BlockName == '' && $Size == '' && $Project != '' && $Category == '' && $InvStatus == ''){
                
                 $response = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($Project) {
                    $q->where('project_id', $Project);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
                $count = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($Project) {
                    $q->where('project_id', $Project);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->count();
                
            }else if($PlotNo == '' && $BlockName == '' && $Size == '' && $Project == '' && $Category != '' && $InvStatus == ''){
                
                $response = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($Category) {
                    $q->where('jsonFormat->property_category', $Category);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
                $count = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($Category) {
                    $q->where('jsonFormat->property_category', $Category);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->count();
                
            }else if($PlotNo != '' && $BlockName == '' && $Size == '' && $Project != '' && $Category == '' && $InvStatus == ''){
                
                $response = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($Project,$PlotNo) {
                   $q->where('project_id', $Project)->where('jsonFormat->property_no', $PlotNo);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
                $count = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($Project,$PlotNo) {
                    $q->where('project_id', $Project)->where('jsonFormat->property_no', $PlotNo);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->count();
                
            }else if($PlotNo == '' && $BlockName != '' && $Size == '' && $Project != '' && $Category == '' && $InvStatus == ''){
                
                 $response = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($Project,$BlockName) {
                   $q->where('project_id', $Project)->where('jsonFormat->block', $BlockName);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
                $count = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($Project,$BlockName) {
                    $q->where('project_id', $Project)->where('jsonFormat->block', $BlockName);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->count();
                
            }else if($PlotNo == '' && $BlockName == '' && $Size != '' && $Project != '' && $Category == '' && $InvStatus == ''){
                
                  $response = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($Project,$Size) {
                   $q->where('project_id', $Project)->where('jsonFormat->size', $Size);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
                $count = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($Project,$Size) {
                    $q->where('project_id', $Project)->where('jsonFormat->size', $Size);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->count();
                
            }else if($PlotNo == '' && $BlockName == '' && $Size == '' && $Project != '' && $Category != '' && $InvStatus == ''){
                
                 $response = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($Project,$Category) {
                   $q->where('project_id', $Project)->where('jsonFormat->property_category', $Category);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
                $count = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($Project,$Category) {
                    $q->where('project_id', $Project)->where('jsonFormat->property_category', $Category);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->count();
                
            }else if($PlotNo != '' && $BlockName != '' && $Size == '' && $Project != '' && $Category == '' && $InvStatus == ''){
                
                 $response = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($Project,$BlockName,$PlotNo) {
                   $q->where('project_id', $Project)->where('jsonFormat->block', $BlockName)->where('jsonFormat->property_no', $PlotNo);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
                $count = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($Project,$BlockName,$PlotNo) {
                    $q->where('project_id', $Project)->where('jsonFormat->block', $BlockName)->where('jsonFormat->property_no', $PlotNo);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->count();
                
            }else if($PlotNo == '' && $BlockName != '' && $Size != '' && $Project != '' && $Category == '' && $InvStatus == ''){
                
                 $response = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($Project,$BlockName,$Size) {
                   $q->where('project_id', $Project)->where('jsonFormat->block', $BlockName)->where('jsonFormat->size', $Size);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
                $count = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($Project,$BlockName,$Size) {
                    $q->where('project_id', $Project)->where('jsonFormat->block', $BlockName)->where('jsonFormat->size', $Size);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->count();
                
            }else if($PlotNo == '' && $BlockName != '' && $Size == '' && $Project != '' && $Category != '' && $InvStatus == ''){
                
                 $response = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($Project,$BlockName,$Category) {
                   $q->where('project_id', $Project)->where('jsonFormat->block', $BlockName)->where('jsonFormat->property_category', $Category);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
                $count = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($Project,$BlockName,$Category) {
                    $q->where('project_id', $Project)->where('jsonFormat->block', $BlockName)->where('jsonFormat->property_category', $Category);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->count();
                
            }else if($PlotNo == '' && $BlockName == '' && $Size == '' && $Project == '' && $Category == '' && $InvStatus != ''){
                
                 $response = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->where(['status'=>$InvStatus])->orderBy($orderBy, $orderType)->paginate($perPage);
                
                $count = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->where(['status'=>$InvStatus])->count();
                
            }else if($PlotNo == '' && $BlockName == '' && $Size == '' && $Project != '' && $Category == '' && !$InvStatus == ''){
                
                 $response = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($Project) {
                    $q->where('project_id', $Project);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->where(['status'=>$InvStatus])->orderBy($orderBy, $orderType)->paginate($perPage);
                
                $count = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                
                } ,])->whereHas('project_inventories', function($q) use ($Project) {
                    $q->where('project_id', $Project);
                })->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->where(['status'=>$InvStatus])->count();
                
            }else{
                
                $response = Inventorysold::with(['customer_leads_tbls','project_inventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = Inventorysold::whereNotIn('status',['Cancelled'])->where(['companyId'=>$company_id])->count();
            }
            
            
        
        
        // SUCCESS
        if (count($response) > 0) {
            $data = $this->renderList($response);

            return response()->json([
                'code' => '200',
                'success' => true,
                'message' => 'Record Found',
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
    }

    // PREPARE LIST
    public function renderList(Object $apiResponse)
    {
        $data = [];

        foreach ($apiResponse as $key => $value) {
           
           if($value->Mystatus == 'affiliate'){
               $customerDetails = AffiliateLeadsModule::where(['id'=>$value->customer_id])->first();
           }else{
               $customerDetails = ListMyLead::where(['id'=>$value->customer_id])->first();
           }
           
           if($value->project_inventories == '' || $value->project_inventories == null){
               $projectMaterial="";
               $Projectasset="";
           }else{
               $projectMaterial = Project::where(['id'=>$value->project_inventories['project_id']])->first();
               
               $Projectasset = Projectasset::where(['project_id'=>$projectMaterial->id])->first();
           }
           
           $Occupationtbl = Occupationtbl::where(['id'=>$value->OccupationId])->first();

            array_push($data, (object) [
                "id" => $value->id,
                "inventory_id" => $value->inventory_id,
                "customer_id" => $value->customer_id,
                "employee_id" => $value->employee_id,
                "father_name" => $value->father_name,
                "date_of_birth" => $value->date_of_birth,
                "client_address" => $value->client_address,
                "client_image" => "public/storage/clientImage/".$value->client_image, //picture
                "cnic" => $value->cnic,
                "clientName"=>$customerDetails == '' || $customerDetails == null ? '' : $customerDetails->clientName,
                "mobileNo"=>$customerDetails == '' || $customerDetails == null ? '' : $customerDetails->mobileNo,
                 "jsonFormat"=>json_decode($value->project_inventories == '' ? '' : $value->project_inventories['jsonFormat']),
                "cnic_front_image" => $value->cnic_front_image, //picture
                "cnic_back_image" => $value->cnic_back_image, //picture
                "relationship_title" => $value->relationship_title,
                "nominee_name" => $value->nominee_name,
                "nomineeFatherHusbandName" => $value->nomineeFatherHusbandName,
                "nominee_image" => "public/storage/nomineeImage/".$value->nominee_image, //picture
                
                "nominee_cnic" => $value->nominee_cnic,
                "nominee_cell" => $value->nominee_cell,
                "nominee_cnic_front" => $value->nominee_cnic_front, //picture
                "nominee_cnic_back" => $value->nominee_cnic_back, //picture
                "nominee_address" => $value->nominee_address,
                "discount_amount" => $value->discount_amount,
                "after_discount_amount" => $value->after_discount_amount,
                "received_amount" => $value->received_amount,
                "payment_type" => $value->payment_type,
                "cheque_no" => $value->cheque_no,
                "mode_of_payment" => $value->mode_of_payment,
                "reference_type" => $value->reference_type,
                "bank_name" => $value->bank_name,
                "Mystatus"=>$value->Mystatus,
                "project_name"=>$projectMaterial == null || $projectMaterial == '' ? '' : $projectMaterial->project_name,
                "project_asset_path"=>$Projectasset == '' || $Projectasset == null ? '' : $Projectasset->project_asset_path,
                'OccupationId'=>$value->OccupationId,
                'OccupationDetails'=>$Occupationtbl,
                'Nationality'=>$value->Nationality,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                 "email"=>$customerDetails == '' || $customerDetails == null ? '' : $customerDetails->email,
                 "status"=>$value->status,
                
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
    public function store(InventorySoldRequest $request)
    {
        
        $Inventorysold = Inventorysold::where(['customer_id'=>$request->post('customer_id')])->first();
       
        if($request->hasFile("client_image")){
            $image_Client_name = $request->file("client_image");
            $ext = $image_Client_name->extension();
            $Client_final=time().'.'.$ext;
            $image_Client_name->storeAs('/public/clientImage/',$Client_final);
        }else{
            $Client_final = $Inventorysold->client_image;
        }
       

        if($request->hasFile("cnic_front_image")){
            $image_cnic_front_name = $request->file("cnic_front_image");
            $ext = $image_cnic_front_name->extension();
            $cnic_front_name=time().'.'.$ext;
            $image_cnic_front_name->storeAs('/public/cnicFront/',$cnic_front_name);
        }else{
            $cnic_front_name = $Inventorysold->cnic_front_image;
        }
       

        if($request->hasFile("cnic_back_image")){
            $image_cnic_back_name = $request->file("cnic_back_image");
            $ext = $image_cnic_back_name->extension();
            $cnic_back_name=time().'.'.$ext;
            $image_cnic_back_name->storeAs('/public/cnicBack/',$cnic_back_name);
        }else{
            $cnic_back_name=$Inventorysold->cnic_back_image;
        }
        
        if($request->hasFile("nominee_image")){
            $image_nominee_image_name = $request->file("nominee_image");
            $ext = $image_nominee_image_name->extension();
            $nominee_image_name=time().'.'.$ext;
            $image_nominee_image_name->storeAs('/public/nomineeImage/',$nominee_image_name);
        }else{
            $nominee_image_name=$Inventorysold->nominee_image;
        }
       
        if($request->hasFile("nominee_cnic_front")){
            $image_nominee_cnic_front_name = $request->file("nominee_cnic_front");
            $ext = $image_nominee_cnic_front_name->extension();
            $nominee_cnic_front_name=time().'.'.$ext;
            $image_nominee_cnic_front_name->storeAs('/public/nomineeCnincFront/',$nominee_cnic_front_name);
        }else{
            $nominee_cnic_front_name=$Inventorysold->nominee_cnic_front;
        }
        
       

        if($request->hasFile("nominee_cnic_back")){
            $image_nominee_cnic_back_name = $request->file("nominee_cnic_back");
            $ext = $image_nominee_cnic_back_name->extension();
            $nominee_cnic_back_name=time().'.'.$ext;
            $image_nominee_cnic_back_name->storeAs('/public/nomineeCnincBack/',$nominee_cnic_back_name);
        }else{
            $nominee_cnic_back_name=$Inventorysold->nominee_cnic_back;
        }
        
        
        $OccupationId=0;
        
        if($request->Occupation == 'Other'){
            
            $Occupationtbl = Occupationtbl::create([
                'OccuptationTitle' =>  $request->OtherOccupation,
                'status' =>  1,
            ]);
            
            $lastId = $Occupationtbl->id;
            $OccupationId = $lastId;
            
        }else{
            $OccupationId = $request->Occupation;
        }
        
        
        $Projectinventory = Projectinventory::where(['id'=>$request->post('inventory_id')])->first();
        
        $decode = json_decode($Projectinventory->jsonFormat,JSON_UNESCAPED_SLASHES);
        
        $cnic = $request->post('cnic');
        
        $block = $decode['tower'] == null || $decode['tower'] == '' ? str_replace('-',' ',strtoupper($decode['block'])) : str_replace('-',' ',strtoupper($decode['tower']));
        $dob = date("d-m-Y",strtotime($request->post('date_of_birth')));
        $plotNo=$decode['property_no'];
        
        $checkPros = strpos($cnic,"-");
        
        
        if($checkPros == ''){

        $sCnic = substr($cnic, 2,1);
        $mCnic = substr($cnic, 9,1);
        $lCnic = substr($cnic, 12,1);
        
        }else{
           
        $sCnic = substr($cnic, 2,1);
        $mCnic = substr($cnic, 9,1);
        $lCnic = substr($cnic, 14,1);
        }
        
        $title = $decode['tower'] == null || $decode['tower'] == '' ? $decode['block'] : $decode['tower'];
        $title_characters = (mb_substr($title, 0, 1, 'UTF8')) . (mb_substr($title, -1, 1, 'UTF8'));
        
        $sdob = substr($dob, 1,1);
        $mdob = substr($dob, 4,1);
        $ldob = substr($dob, 9,1);
        
        if($plotNo < 100){
            $PlotNot = "00".$plotNo;
        }else{
            $PlotNot=$plotNo;
        }
        
        $dobMerger = $sdob."".$mdob."".$ldob;
        $cnicMerger = $sCnic."".$mCnic."".$lCnic;
        $TrackingId = strtoupper($title_characters)."".$cnicMerger."".$PlotNot."".$dobMerger;
        
        $response = Inventorysold::create([
        'inventory_id'=>$request->post('inventory_id'),
        'customer_id'=>$request->post('customer_id'),
        'employee_id'=>$request->post('employee_id'),
        'trackingId'=>$TrackingId,
        'father_name'=>$request->post('father_name'),
        'date_of_birth'=>$request->post('date_of_birth'),
        'client_address'=>$request->post('client_address'),
        'client_image'=>$Client_final,
        'cnic'=>$request->post('cnic'),
        'cnic_front_image'=>$cnic_front_name,
        'cnic_back_image'=>$cnic_back_name,
        'relationship_title'=>$request->post('relationship_title'),
        'nominee_name'=>$request->post('nominee_name'),
        'nomineeFatherHusbandName'=>$request->post('nomineeFatherHusbandName'),
        'nominee_image'=>$nominee_image_name,
        'nominee_cnic'=>$request->post('nominee_cnic'),
        'nominee_cell'=>$request->post('nominee_cell'),
        'nominee_cnic_front'=>$nominee_cnic_front_name,
        'nominee_cnic_back'=>$nominee_cnic_back_name,
        'nominee_address'=>$request->post('nominee_address'),
        'OccupationId'=>$OccupationId,
        'Nationality'=>$request->Nationality,
        'Mystatus'=>$request->post('myStatus'),
        ]);
        
        $id = $response->id;
        $invid = $request->post('inventory_id');
        
        if($response){
            
             Projectinventory::where('id', $invid)
            ->update([
            'status' => 'sold',
            ]);
            
            return response()->json([
                'message' => "Data saved successfully!",
                'data'=>$decode,
                'code' => '200'
            ], 200);
            
        }
        
       
    }
    
   

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inventory\Inventorysold  $inventorysold
     * @return \Illuminate\Http\Response
     */
    public function show(Inventorysold $inventorysold)
    {
        $data = [];
        
        $value = Inventorysold::findOrFail($inventorysold->id);
        
        if($value->Mystatus == 'affiliate'){
               $customerDetails = AffiliateLeadsModule::where(['id'=>$value->customer_id])->first();
           }else{
               $customerDetails = ListMyLead::where(['id'=>$value->customer_id])->first();
           }
           
           if($value->project_inventories == '' || $value->project_inventories == null){
               $projectMaterial="";
               $Projectasset="";
           }else{
               $projectMaterial = Project::where(['id'=>$value->project_inventories['project_id']])->first();
               
               $Projectasset = Projectasset::where(['project_id'=>$projectMaterial->id])->first();
           }
           
           
           $Projectinventory = Projectinventory::where(['id'=>$value->inventory_id])->first();
           
            $Occupationtbl = Occupationtbl::where(['id'=>$value->OccupationId])->first();

        
        $decode = json_decode($Projectinventory->jsonFormat,JSON_UNESCAPED_SLASHES);
        
        $cnic = $value->cnic;
        $block = str_replace('-',' ',strtoupper($decode['block']));
        $dob = date("d-m-Y",strtotime($value->date_of_birth));
        $plotNo=$decode['property_no'];
        
        $checkPros = strpos($cnic,"-");
        
        if($plotNo < 100){
            $PlotNot = "00".$plotNo;
        }else{
            $PlotNot=$plotNo;
        }
        
        
        if($checkPros == ''){
        
        $sCnic = substr($cnic, 2,1);
        $mCnic = substr($cnic, 9,1);
        $lCnic = substr($cnic, 12,1);
        
        }else{
           
        $sCnic = substr($cnic, 2,1);
        $mCnic = substr($cnic, 9,1);
        $lCnic = substr($cnic, 14,1);
        }
        
        
        $title = $decode['block'];
        $title_characters = (mb_substr($title, 0, 1, 'UTF8')) . (mb_substr($title, -1, 1, 'UTF8'));
        
        $sdob = substr($dob, 1,1);
        $mdob = substr($dob, 4,1);
        $ldob = substr($dob, 9,1);
        
        $dobMerger = $sdob."".$mdob."".$ldob;
        $cnicMerger = $sCnic."".$mCnic."".$lCnic;
        $TrackingId = strtoupper($title_characters)."".$cnicMerger."".$PlotNot."".$dobMerger;

            array_push($data, (object) [
                "id" => $value->id,
                "TrackingId"=>$TrackingId,
                "plotNo"=>$plotNo,
                "inventory_id" => $value->inventory_id,
                "customer_id" => $value->customer_id,
                "employee_id" => $value->employee_id,
                "father_name" => $value->father_name,
                "date_of_birth" => date("d-m-Y",strtotime($value->date_of_birth)),
                "client_address" => $value->client_address,
                "client_image" => "public/storage/clientImage/".$value->client_image, //picture
                "cnic" => $value->cnic,
                "clientName"=>$customerDetails == '' || $customerDetails == null ? '' : $customerDetails->clientName,
                "mobileNo"=>$customerDetails == '' || $customerDetails == null ? '' : $customerDetails->mobileNo,
                 "jsonFormat"=>json_decode($value->project_inventories == '' ? '' : $value->project_inventories['jsonFormat']),
                "cnic_front_image" => $value->cnic_front_image, //picture
                "cnic_back_image" => $value->cnic_back_image, //picture
                "relationship_title" => $value->relationship_title,
                "nominee_name" => $value->nominee_name,
                "nomineeFatherHusbandName"=>$value->nomineeFatherHusbandName,
                "nominee_image" => "public/storage/nomineeImage/".$value->nominee_image, //picture
                "nominee_cnic" => $value->nominee_cnic,
                "nominee_cell" => $value->nominee_cell,
                "nominee_cnic_front" => $value->nominee_cnic_front, //picture
                "nominee_cnic_back" => $value->nominee_cnic_back, //picture
                "nominee_address" => $value->nominee_address,
                "discount_amount" => $value->discount_amount,
                "after_discount_amount" => $value->after_discount_amount,
                "received_amount" => $value->received_amount,
                "payment_type" => $value->payment_type,
                "cheque_no" => $value->cheque_no,
                "mode_of_payment" => $value->mode_of_payment,
                "reference_type" => $value->reference_type,
                "bank_name" => $value->bank_name,
                "Mystatus"=>$value->Mystatus,
                "project_name"=>$projectMaterial == null || $projectMaterial == '' ? '' : $projectMaterial->project_name,
                "project_asset_path"=>$Projectasset == '' || $Projectasset == null ? '' : $Projectasset->project_asset_path,
                "updated_at" => $value->updated_at,
                "created_at" =>date("Y-m-d",strtotime($value->created_at)),
                "deleted_at" => $value->deleted_at,
                "email"=>$customerDetails == '' || $customerDetails == null ? '' : $customerDetails->email,
                'OccupationId'=>$value->OccupationId,
                'OccupationDetails'=>$Occupationtbl,
                'OccuptationTitle'=>$Occupationtbl == '' || $Occupationtbl == null ? '' : $Occupationtbl->OccuptationTitle,
                'Nationality'=>$value->Nationality,
            ]);

            return response()->json([
                'code' => '200',
                'success' => true,
                'message' => 'Record Found',
                'data' => $data,
            ],200);
        // return response()->json([
        //     'message' => "Data retrieved successfully!",
        //     'data' => $response
        // ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inventory\Inventorysold  $inventorysold
     * @return \Illuminate\Http\Response
     */
    public function update(InventorySoldRequest $request, Inventorysold $inventorysold)
    {
        $inventorysold->update($request->all());

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $inventorysold
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inventory\Inventorysold  $inventorysold
     * @return \Illuminate\Http\Response
     */
    public function destroy(InventoryHoldRequest $request, Inventorysold $inventorysold)
    {
        $inventorysold->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
        ], 200);
    }
    
    public function InventorySoldTest (){
        
       $Projectinventory = Projectinventory::where(['id'=>'3473'])->first();
       
       $decode = json_decode($Projectinventory->jsonFormat,JSON_UNESCAPED_SLASHES);
      
       $cnic = "42101-1392005-9";
       $block = str_replace('-',' ',strtoupper($decode['block']));
       $dob = "04-09-1981";
       $plotNo=$decode['property_no'];
       
       $checkPros = strpos($cnic,"-");
       
       if($checkPros == ''){
           
           $sCnic = substr($cnic, 2,1);
           $mCnic = substr($cnic, 10,1);
           $lCnic = substr($cnic, 14,1);
           
       }else{
           $sCnic = substr($cnic, 2,1);
           $mCnic = substr($cnic, 9,1);
           $lCnic = substr($cnic, 14,1);
       }
        
        $title = $decode['block'];
        $title_characters = (mb_substr($title, 0, 1, 'UTF8')) . (mb_substr($title, -1, 1, 'UTF8'));
        
       $sdob = substr($dob, 1,1);
       $mdob = substr($dob, 4,1);
       $ldob = substr($dob, 9,1);
     
        $TrackingId = strtoupper($title_characters)."".$sCnic."".$mCnic."".$lCnic."".$plotNo."".$sdob."".$mdob."".$ldob;
       
        echo $TrackingId;
       
    }
    
    
    public function FileStatus(Request $request){
        
        $FileStatus = $request->FileStatus;
        $dealId = $request->dealId;
        
        $Inventorysold = Inventorysold::find($dealId);
        $Inventorysold->status = $FileStatus;
        $Inventorysold->save();
        
         return response()->json([
            'message' => "Data saved successfully!",
            'data' => $Inventorysold,
            'code'=>'200'
        ], 200);
        
    }
    
    public function inventoryUnhold(Request $request){
        
        $inventory_id = $request->inventory_id;
        
        Projectinventory::where('id', $inventory_id)
            ->update([
            'status' => null,
        ]);
        
        return response()->json([
            'message' => "Data saved Updated!",
            'data' => $request->all(),
            'code'=>'200'
        ], 200);
        
    }
    
      public function inventoryCancelled(Request $request){
        
        $inventory_id = $request->inventory_id;
        $soldId = $request->soldId;
        
        $Inventorysold = Inventorysold::where(['inventory_id'=>$soldId])->first();
        
        if(empty($Inventorysold)){
            
        }else{
            
           $customerId = $Inventorysold->customer_id;
           $employeeId = $Inventorysold->employee_id;
           $CancelledDate = $request->CancelledDate;
           $CancelledNote = $request->CancelledNote;
           
        //   $get = Inventorysold::where('inventory_id', $soldId)->first();
            
           
            
            $response = InventoryCancelled::create([
            'inventoryId'=>$soldId,
            'customerId'=>$customerId,
            'employeeId'=>$employeeId,
            'cancelledReason'=>$CancelledNote,
            'cancelledDate'=>$CancelledDate,
            'status'=>'approved',
            ]);
            
             Inventorysold::where('inventory_id', $soldId)
            ->update([
            'status' => 'Cancelled',
            ]);
            
            Projectinventory::where('id', $soldId)
            ->update([
            'status' => null,
            ]);
        
            return response()->json([
            'message' => "Data saved Updated!",
            'data' => $response,
            'code'=>'200'
            ], 200);
            
        }
        
        
        
    }
    
    public function ListInventoriesSoldExport (Request $request) {
    
    //  ini_set('memory_limit','-1');
 
    $proid = $request->proid;
    
    return Excel::download(new ExportSold($proid), 'InventorySoldList.xlsx');
   
    
    // $data = Excel::store(new AssignLeadsExport($agentId), 'users.xlsx');
    
    return response()->json([
        'message' => "Data Retrieve successfully!",
        'data' => $proid,
        'code'=>'200'
    ], 200);
    
}
    
    
}
