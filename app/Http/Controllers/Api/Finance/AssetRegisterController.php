<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\AssetRegisterRequest;
use App\Models\Finance\AssetLife;
use App\Models\Finance\AssetRegister;
use App\Models\Finance\Getpass;
use App\Models\Finance\AssetType;
use App\Models\HR\Company;
use App\Models\HR\Department;
use App\Models\HR\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Assetlocation;

use Validator;
use Mail;
use DateTime;
use DB;

class AssetRegisterController extends Controller
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
        
        $AssetType = $request->AssetType ?? "";
        $CompanyId = $request->CompanyId ?? "";
        $DepartementId = $request->DepartementId ?? "";
        $PurchaseDate = $request->PurchaseDate ?? "";
        $AssetBookValue = $request->AssetBookValue ?? "";
        $Status = $request->Status ?? "";
        $AssetLocationId = $request->AssetLocationId ?? "";
        $SalesConsul = $request->SalesConsul ?? "";
        $company_id = $request->company_id ?? "";
        
        if (is_null($agentId)) {
            
            if($AssetType !=''){
                $response = AssetRegister::where(['assetTypeId'=>$AssetType,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = AssetRegister::where(['assetTypeId'=>$AssetType,'company_id'=>$company_id])->count();
            }else if($CompanyId !=''){
                $response = AssetRegister::where(['CompanyId'=>$CompanyId,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = AssetRegister::where(['CompanyId'=>$CompanyId,'company_id'=>$company_id])->count();
            }else if($DepartementId !=''){
                $response = AssetRegister::where(['DepartmentId'=>$DepartementId,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = AssetRegister::where(['DepartmentId'=>$DepartementId,'company_id'=>$company_id])->count();
            }else if($PurchaseDate !=''){
                $response = AssetRegister::where(['purchasedDate'=>$PurchaseDate,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = AssetRegister::where(['purchasedDate'=>$PurchaseDate,'company_id'=>$company_id])->count();
            }else if($AssetBookValue !=''){
                $response = AssetRegister::where(['AssetBookValue'=>$AssetBookValue,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = AssetRegister::where(['AssetBookValue'=>$AssetBookValue,'company_id'=>$company_id])->count();
            }else if($Status !=''){
                $response = AssetRegister::where(['status'=>$Status,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = AssetRegister::where(['status'=>$Status,'company_id'=>$company_id])->count();
            }else if($AssetLocationId !=''){
                $response = AssetRegister::where(['AssetLocationId'=>$AssetLocationId,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = AssetRegister::where(['AssetLocationId'=>$AssetLocationId,'company_id'=>$company_id])->count();
            }else if($SalesConsul !=''){
                $response = AssetRegister::where(['agentId'=>$SalesConsul,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = AssetRegister::where(['agentId'=>$SalesConsul,'company_id'=>$company_id])->count();
            }else{
                
                $response = AssetRegister::where(['company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = AssetRegister::where(['company_id'=>$company_id])->count();
            }
            
           
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Asset Register List Found',
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
            
            if($AssetType !=''){
                $response = AssetRegister::where(['assetTypeId'=>$AssetType,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = AssetRegister::where(['assetTypeId'=>$AssetType,'company_id'=>$company_id])->count();
            }else if($CompanyId !=''){
                $response = AssetRegister::where(['CompanyId'=>$CompanyId,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = AssetRegister::where(['CompanyId'=>$CompanyId,'company_id'=>$company_id])->count();
            }else if($DepartementId !=''){
                $response = AssetRegister::where(['DepartmentId'=>$DepartementId,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = AssetRegister::where(['DepartmentId'=>$DepartementId,'company_id'=>$company_id])->count();
            }else if($PurchaseDate !=''){
                $response = AssetRegister::where(['purchasedDate'=>$PurchaseDate,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = AssetRegister::where(['purchasedDate'=>$PurchaseDate,'company_id'=>$company_id])->count();
            }else if($AssetBookValue !=''){
                $response = AssetRegister::where(['AssetBookValue'=>$AssetBookValue,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = AssetRegister::where(['AssetBookValue'=>$AssetBookValue,'company_id'=>$company_id])->count();
            }else if($Status !=''){
                $response = AssetRegister::where(['status'=>$Status,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = AssetRegister::where(['status'=>$Status,'company_id'=>$company_id])->count();
            }else if($AssetLocationId !=''){
                $response = AssetRegister::where(['AssetLocationId'=>$AssetLocationId,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = AssetRegister::where(['AssetLocationId'=>$AssetLocationId,'company_id'=>$company_id])->count();
            }else if($SalesConsul !=''){
                $response = AssetRegister::where(['agentId'=>$SalesConsul,'company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = AssetRegister::where(['agentId'=>$SalesConsul,'company_id'=>$company_id])->count();
            }else{
                $response = AssetRegister::where(['company_id'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = AssetRegister::where(['company_id'=>$company_id])->count();
            }
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Asset Register List Found',
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
                
                $AssetType = AssetType::where(['id'=>$value->assetTypeId])->first();
                 $AssetLife = AssetLife::where(['id'=>$value->assetLifeId])->first();
                 $Company = Company::where(['id'=>$value->CompanyId])->first();
                 $Assetlocation = Assetlocation::where(['id'=>$value->AssetLocationId])->first();
                 $Getpass = Getpass::where(['assetId'=>$value->id])->latest()->first();
                 
                 
                 if($Getpass == null || $Getpass == ''){
                     $Employee="";
                 }else{
                     $Employee = Employee::where(['id'=>$Getpass->agentId])->first();
                 }
                 
                 
                 $Department = Department::where(['id'=>$value->DepartmentId])->first();
                 
                //  $ext = pathinfo($filename, PATHINFO_EXTENSION);
                 
                if($value->AssetImage == null || $value->AssetImage == ''){
                    $extensionAssetImage = "";
                }else{
                    $AssetImageParts = explode('.', $value->AssetImage);
                    $extensionAssetImage = array_pop($AssetImageParts);
                }
                
                if($value->AssetImage2 == null || $value->AssetImage2 == ''){
                    $extensionAssetImage2="";
                }else{
                    $AssetImage2Parts = explode('.', $value->AssetImage2);
                    $extensionAssetImage2 = array_pop($AssetImage2Parts);
                }
                
                if($value->AssetImage3 == null || $value->AssetImage3 == ''){
                    $extensionAssetImage3="";
                }else{
                    $AssetImage3Parts = explode('.', $value->AssetImage3);
                    $extensionAssetImage3 = array_pop($AssetImage3Parts);
                }
                
                $yearCount = $AssetLife == '' ? '' : $AssetLife->LifeTitle;
                
                $yearwiseMonthCount = $yearCount * 12;
                
                $monthData = [];
                
                for($y=1; $y<=$yearwiseMonthCount; $y++)
                {
                    $d=date('t', mktime(0, 0, 0, $y, 1, $yearCount)); 
                    $yearData = date("Y", mktime(0, 0, 0, ($y)));
                    $monthNameData = date("m", mktime(0, 0, 0, ($y)));
                    $monthData[]=$d;
                }
                
                $montSum = array_sum($monthData);
                
                $ExpiryDate = date('Y-m-d', strtotime($value->purchasedDate. ' + '.$montSum.' days'));
                
                $currentDate = new DateTime(date("Y-m-d"));
                $expDate = new DateTime($ExpiryDate);
                $interval = $expDate->diff($currentDate);
                
                $residueLife = $interval->y . " years, " . $interval->m." months, ".$interval->d." days";
                
                
                $assetBookValP = (int) str_replace(',', '',$value->AssetBookValue);
                
                $perDayDeprecation = floor($assetBookValP / $montSum);
                
                //Total No Of Days
               
                $datetime1 = new DateTime($ExpiryDate);
                $datetime2 = new DateTime($value->purchasedDate);
                $interval = $datetime1->diff($datetime2);
                $days = $interval->format('%a');//now do whatever you like with $days
            
                $TotalNoOfDatsxs = $days + 1;
                
                // Days Lapsed
                
                $dateCurrent = date("Y-m-d");
                
                $datetime12 = new DateTime($dateCurrent);
                $datetime23 = new DateTime($value->purchasedDate);
                $intervals = $datetime12->diff($datetime23);
                $daysxs = $intervals->format('%a');//now do whatever you like with $days
                
                $totalDaysLapsed = $daysxs + 1;
                
                // Deprication FOrmula
                
                $depForm = $assetBookValP / $TotalNoOfDatsxs * $totalDaysLapsed;
                
                
                $dataX = "Company Name: ".$value->company_name . " | ". "Asset Type: " .$value->assetType . " | ". "Department: ".$value->department_name. " | "."Book Value: ".$value->AssetBookValue . " | "."Quantity: ".$value->Quantity." | "."Date Of Purchase: ".$value->purchasedDate." | "."Expiry Date: ".$value->ExpiryDate." | "."Depreciation Value: ".round($depForm)." | "."Asset Desc: ".$value->AssetDesc;
                
                $Qrcode = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=".$dataX;
                
                
                if($value->agentId == null || $value->agentId == ''){
                    $Employee = "";
                }else{
                    $Employee = Employee::where(['id'=>$value->agentId])->first();
                }
                
                
               
                
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "TotalNoOfDatsxs"=>$TotalNoOfDatsxs,
                "totalDaysLapsed"=>$totalDaysLapsed,
                'Getpass'=>$Getpass,
                'Employee'=>$Employee,
                "depForm"=>$depForm,
                "rec_id" => $value->rec_id,
                "assetTypeId"=>$value->assetTypeId,
                "assetType"=>$AssetType == '' ? '' : $AssetType->assetType,
                "purchasedDate"=>$value->purchasedDate,
                "perDayDeprecation"=>$perDayDeprecation,
                "ExpiryDate" => $ExpiryDate,
                "residueLife" =>$residueLife,
                "AssetDesc"=>$value->AssetDesc,
                "AssetBookValue"=>$value->AssetBookValue,
                "assetLifeId"=>$value->assetLifeId,
                "LifeTitle"=>$AssetLife == '' ? '' : $AssetLife->LifeTitle,
                'yearwiseMonthCount'=>$montSum,
                "Location"=>$value->Location,
                "AssetLocationId"=>$value->AssetLocationId,
                "CompanyId"=>$value->CompanyId,
                "company_name"=>$Company == '' ? '' : $Company->company_name,
                "DepartmentId"=>$value->DepartmentId,
                "department_name"=>$Department == '' ? '' : $Department->department_name,
                "Quantity"=>$value->Quantity,
                "AssetImage"=>$value->AssetImage,
                "AssetImageExtension" => $extensionAssetImage,
                "AssetImage2"=>$value->AssetImage2,
                "AssetImage2Extension" => $extensionAssetImage2,
                "AssetImage3"=>$value->AssetImage3,
                "AssetImage3Extension" => $extensionAssetImage3,
                "status"=>$value->status,
                "locationName"=>$Assetlocation == '' || $Assetlocation == null ? '' : $Assetlocation->locationName,
                'Qrcode'=>$Qrcode,
                'agentId'=>$value->agentId,
                "emp_name"=>$Employee == null ? '' : $Employee->emp_name
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
    public function store(AssetRegisterRequest $request)
    {
        
        $getYear = date("Y");
        $getMonth = date("m");
        
        if($request->hasFile("AssetImage")){
            $image = $request->file("AssetImage");
            $ext = $image->extension();
            $image_name=time().'.'.$ext;
            $image->storeAs('/public/AssetImages/',$image_name);
             $FileData = "public/storage/AssetImages/".$image_name;
            
        }else{
            $FileData="";
        }
        
        if($request->hasFile("AssetImage1")){
            $image = $request->file("AssetImage1");
            $ext = $image->extension();
            $image_name=time().'.'.$ext;
            $image->storeAs('/public/AssetImages2/',$image_name);
            $FileData2 = "public/storage/AssetImages2/".$image_name;
            
        }else{
            $FileData2="";
        }
        
        if($request->hasFile("AssetImage2")){
            $image = $request->file("AssetImage2");
            $ext = $image->extension();
            $image_name=time().'.'.$ext;
            $image->storeAs('/public/AssetImages3/',$image_name);
            $FileData3 = "public/storage/AssetImages3/".$image_name;
            
        }else{
            $FileData3="";
        }
       
        
        $assetTypeIds=0;
        
        if($request->assetTypeId == 'others'){
            
            $AssetTypeD = AssetType::create([
                'assetType' =>  $request->otherassetTypeId,
                'status' =>  1,
            ]);
            
            $lastId = $AssetTypeD->id;
            $assetTypeIds = $lastId;
            
        }else{
            $assetTypeIds = $request->assetTypeId;
        }
        
        
        $assetOtherLocationId=0;
        
        if($request->AssetLocationId == 'others'){
            
            $AssetOtherLoca = Assetlocation::create([
                'locationName' =>  $request->OtherLocationId,
                'status' =>  1,
            ]);
            
            $lastId = $AssetOtherLoca->id;
            $assetOtherLocationId = $lastId;
            
        }else{
            $assetOtherLocationId = $request->AssetLocationId;
        }
        
        $getmonthVoucher = AssetRegister::where(DB::raw('MONTH(created_at)'),$getMonth)->get();
        // $monthCountX = array_count_values($getmonthVoucher);
        
        $getyearVoucher = AssetRegister::where(DB::raw('YEAR(created_at)'),$getYear)->get();
        
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
        
        $voucherNOData = "FA"."/".$getYear."/".$getMonth."/".$monthCount."/".$yearCount;
        
        $response = AssetRegister::create([
            'assetTypeId' =>  $assetTypeIds,
            'rec_id'=>$voucherNOData,
            'purchasedDate' =>  $request->purchasedDate,
            'AssetBookValue' =>  $request->AssetBookValue,
            'assetLifeId' =>  $request->assetLifeId,
            'CompanyId' =>  $request->CompanyId,
            'Location' =>  $request->Location == '' ||  $request->Location == null ? '-' :  $request->Location,
            'DepartmentId' =>  $request->DepartmentId,
            'Quantity' =>  $request->Quantity,
            'AssetImage' =>  $FileData,
            'AssetImage2' =>  $FileData2,
            'AssetImag3' =>  $FileData3,
            'AssetDesc'=>$request->AssetDesc,
            'uploadBy'=>$request->uploadBy,
            "AssetLocationId"=>$assetOtherLocationId,
            'status' =>  '0',
            'agentId'=>$request->SalesConsul,
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
    public function show(AssetRegister $assetregister)
    {
        $response = AssetRegister::where(['id'=>$assetregister->id])->get();
            
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
    public function update(AssetRegisterRequest $request, AssetRegister $assetregister,$id="")
    {
        
        
        $checkFiles = AssetRegister::findOrFail($assetregister->id);
        
        if($request->hasFile("AssetImage")){
            $image = $request->file("AssetImage");
            $ext = $image->extension();
            $image_name=time().'.'.$ext;
            $image->storeAs('/public/AssetImages/',$image_name);
            $image_name = "public/storage/AssetImages/".$image_name;
        }else{
            $image_name=$checkFiles->AssetImage;
        }
        
        if($request->hasFile("AssetImage1")){
            $image2 = $request->file("AssetImage1");
            $ext = $image2->extension();
            $image_name_2=time().'.'.$ext;
            $image2->storeAs('/public/AssetImages2/',$image_name_2);
            $FileData2 = "public/storage/AssetImages2/".$image_name_2;
            
        }else{
            $FileData2=$checkFiles->AssetImage2;
        }
        
        if($request->hasFile("AssetImage2")){
            $image3 = $request->file("AssetImage2");
            $ext = $image3->extension();
            $image_name_3=time().'.'.$ext;
            $image3->storeAs('/public/AssetImages3/',$image_name_3);
            $FileData3 = "public/storage/AssetImages3/".$image_name_3;
            
        }else{
            $FileData3=$checkFiles->AssetImage3;
        }

        $assetTypeIds=0;
        
        if($request->assetTypeId == 'others'){
            $AssetTypeD = AssetType::create([
                'assetType' =>  $request->otherassetTypeId,
                'status' =>  1,
            ]);
            
            $lastId = $AssetTypeD->id;
            $assetTypeIds = $lastId;
        }else{
            $assetTypeIds = $request->assetTypeId;
        }
        
        
        $assetOtherLocationId=0;
        
        if($request->AssetLocationId == 'others'){
            
            $AssetOtherLoca = Assetlocation::create([
                'locationName' =>  $request->OtherLocationId,
                'status' =>  1,
            ]);
            
            $lastId = $AssetOtherLoca->id;
            $assetOtherLocationId = $lastId;
            
        }else{
            $assetOtherLocationId = $request->AssetLocationId;
        }
        
        
        $assetregister->update([
            'assetTypeId' =>  $assetTypeIds,
            'purchasedDate' =>  $request->purchasedDate,
            'AssetBookValue' =>  $request->AssetBookValue,
            'assetLifeId' =>  $request->assetLifeId,
            'CompanyId' =>  $request->CompanyId,
            'Location' =>  $request->Location == '' ||  $request->Location == null ? '-' :  $request->Location,
            'DepartmentId' =>  $request->DepartmentId,
            'Quantity' =>  $request->Quantity,
            'AssetImage' =>  $image_name,
            'AssetImage2' =>  $FileData2,
            'AssetImage3' =>  $FileData3,
            'AssetDesc'=>$request->AssetDesc,
            'status'=>$request->status,
            "AssetLocationId"=>$assetOtherLocationId,
            'uploadBy'=>$request->uploadBy,
            'agentId'=>$request->SalesConsul,
            'company_id'=>$request->company_id,
        ]);
        
        
        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $assetregister,
            'code'=>'200'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(AssetRegisterRequest $request, AssetRegister $assetregister)
    {
        $assetregister->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code'=>'200'
        ], 200);
    }
    
    
    public function AsssetRegisterFilters(Request $request){
        
        $AssetTypeFrom = $request->AssetTypeFrom ?? "";
        $AssetTypeTo = $request->AssetTypeTo ?? "";
        
        $PurchasedDateFrom = $request->PurchasedDateFrom ?? "";
        $PurchasedDateTo = $request->PurchasedDateTo ?? "";
        
        $DepartmentIdFrom = $request->DepartmentIdFrom ?? "";
        $DepartmentIdTo = $request->DepartmentIdTo ?? "";
        
        $CompanyIdFrom = $request->CompanyIdFrom ?? "";
        $CompanyIdTo = $request->CompanyIdTo ?? "";
        
        $AssetBookValueFrom = $request->AssetBookValueFrom ?? "";
        $AssetBookValueTo = $request->AssetBookValueTo ?? "";

        if($AssetTypeFrom != '' && $AssetTypeTo !='' && $CompanyIdFrom == '' && $CompanyIdTo == '' && $PurchasedDateFrom == '' && $PurchasedDateTo == '' && $DepartmentIdFrom == '' && $DepartmentIdTo == '' && $AssetBookValueFrom == '' && $AssetBookValueTo == ''){
           
           $response = AssetRegister::whereBetween('assetTypeId', array($AssetTypeFrom,$AssetTypeTo))->orderBy('id', 'desc')->get();
           $count = AssetRegister::whereBetween('assetTypeId', array($AssetTypeFrom,$AssetTypeTo))->count();
        
        }else if($AssetTypeFrom != '' && $AssetTypeTo !='' && $CompanyIdFrom != '' && $CompanyIdTo !='' && $DepartmentIdFrom == '' && $DepartmentIdTo == '' && $PurchasedDateFrom == '' && $PurchasedDateTo == '' && $AssetBookValueFrom == '' && $AssetBookValueTo ==''){
            
            $response = AssetRegister::whereBetween('assetTypeId', array($AssetTypeFrom,$AssetTypeTo))->whereBetween('CompanyId', array($CompanyIdFrom,$CompanyIdTo))->orderBy('id', 'desc')->get();
           $count = AssetRegister::whereBetween('assetTypeId', array($AssetTypeFrom,$AssetTypeTo))->whereBetween('CompanyId', array($CompanyIdFrom,$CompanyIdTo))->count();
            
        }else if($AssetTypeFrom != '' && $AssetTypeTo !='' && $CompanyIdFrom != '' && $CompanyIdTo !='' && $DepartmentIdFrom !='' && $DepartmentIdTo !='' && $PurchasedDateFrom == '' && $PurchasedDateTo == '' && $AssetBookValueFrom == '' && $AssetBookValueTo ==''){
            
            $response = AssetRegister::whereBetween('assetTypeId', array($AssetTypeFrom,$AssetTypeTo))->whereBetween('CompanyId', array($CompanyIdFrom,$CompanyIdTo))->whereBetween('DepartmentId', array($DepartmentIdFrom,$DepartmentIdTo))->orderBy('id', 'desc')->get();
           $count = AssetRegister::whereBetween('assetTypeId', array($AssetTypeFrom,$AssetTypeTo))->whereBetween('CompanyId', array($CompanyIdFrom,$CompanyIdTo))->whereBetween('DepartmentId', array($DepartmentIdFrom,$DepartmentIdTo))->count();
            
        }else if($AssetTypeFrom != '' && $AssetTypeTo !='' && $CompanyIdFrom != '' && $CompanyIdTo !='' && $DepartmentIdFrom !='' && $DepartmentIdTo !='' && $PurchasedDateFrom !='' && $PurchasedDateTo !='' && $AssetBookValueFrom == '' && $AssetBookValueTo ==''){
            
            $response = AssetRegister::whereBetween('assetTypeId', array($AssetTypeFrom,$AssetTypeTo))->whereBetween('CompanyId', array($CompanyIdFrom,$CompanyIdTo))->whereBetween('DepartmentId', array($DepartmentIdFrom,$DepartmentIdTo))->whereBetween('purchasedDate', array($PurchasedDateFrom,$PurchasedDateTo))->orderBy('id', 'desc')->get();
            
           $count = AssetRegister::whereBetween('assetTypeId', array($AssetTypeFrom,$AssetTypeTo))->whereBetween('CompanyId', array($CompanyIdFrom,$CompanyIdTo))->whereBetween('DepartmentId', array($DepartmentIdFrom,$DepartmentIdTo))->whereBetween('purchasedDate', array($PurchasedDateFrom,$PurchasedDateTo))->count();
          
            
        }else if($AssetTypeFrom == '' && $AssetTypeTo =='' && $CompanyIdFrom != '' && $CompanyIdTo !='' && $DepartmentIdFrom !='' && $DepartmentIdTo !='' && $PurchasedDateFrom =='' && $PurchasedDateTo =='' && $AssetBookValueFrom == '' && $AssetBookValueTo ==''){
            
            $response = AssetRegister::whereBetween('CompanyId', array($CompanyIdFrom,$CompanyIdTo))->whereBetween('DepartmentId', array($DepartmentIdFrom,$DepartmentIdTo))->orderBy('id', 'desc')->get();
            
           $count = AssetRegister::whereBetween('CompanyId', array($CompanyIdFrom,$CompanyIdTo))->whereBetween('DepartmentId', array($DepartmentIdFrom,$DepartmentIdTo))->count();
          
            
        }else if($AssetTypeFrom != '' && $AssetTypeTo !='' && $CompanyIdFrom == '' && $CompanyIdTo =='' && $DepartmentIdFrom !='' && $DepartmentIdTo !='' && $PurchasedDateFrom =='' && $PurchasedDateTo =='' && $AssetBookValueFrom == '' && $AssetBookValueTo ==''){
            
            $response = AssetRegister::whereBetween('assetTypeId', array($AssetTypeFrom,$AssetTypeTo))->whereBetween('DepartmentId', array($DepartmentIdFrom,$DepartmentIdTo))->orderBy('id', 'desc')->get();
            
           $count = AssetRegister::whereBetween('assetTypeId', array($AssetTypeFrom,$AssetTypeTo))->whereBetween('DepartmentId', array($DepartmentIdFrom,$DepartmentIdTo))->count();
          
            
        }else if($AssetTypeFrom == '' && $AssetTypeTo =='' && $CompanyIdFrom != '' && $CompanyIdTo !='' && $DepartmentIdFrom =='' && $DepartmentIdTo =='' && $PurchasedDateFrom !='' && $PurchasedDateTo !='' && $AssetBookValueFrom !='' && $AssetBookValueTo !=''){
            
            $response = AssetRegister::whereBetween('CompanyId', array($CompanyIdFrom,$CompanyIdTo))->whereBetween('purchasedDate', array($PurchasedDateFrom,$PurchasedDateTo))->whereBetween('AssetBookValue', array($AssetBookValueFrom,$AssetBookValueTo))->orderBy('id', 'desc')->get();
            
           $count = AssetRegister::whereBetween('assetTypeId', array($AssetTypeFrom,$AssetTypeTo))->whereBetween('DepartmentId', array($DepartmentIdFrom,$DepartmentIdTo))->count();
          
            
        }else if($AssetTypeFrom != '' && $AssetTypeTo !='' && $CompanyIdFrom != '' && $CompanyIdTo !='' && $DepartmentIdFrom !='' && $DepartmentIdTo !='' && $PurchasedDateFrom !='' && $PurchasedDateTo !='' && $AssetBookValueFrom !='' && $AssetBookValueTo !=''){
            
            $response = AssetRegister::whereBetween('assetTypeId', array($AssetTypeFrom,$AssetTypeTo))->whereBetween('CompanyId', array($CompanyIdFrom,$CompanyIdTo))->whereBetween('DepartmentId', array($DepartmentIdFrom,$DepartmentIdTo))->whereBetween('purchasedDate', array($PurchasedDateFrom,$PurchasedDateTo))->whereBetween('AssetBookValue', array($AssetBookValueFrom,$AssetBookValueTo))->orderBy('id', 'desc')->get();
            
           $count = AssetRegister::whereBetween('assetTypeId', array($AssetTypeFrom,$AssetTypeTo))->whereBetween('DepartmentId', array($DepartmentIdFrom,$DepartmentIdTo))->count();
          
            
        }else if($AssetTypeFrom != '' && $AssetTypeTo !='' && $CompanyIdFrom != '' && $CompanyIdTo !='' && $DepartmentIdFrom =='' && $DepartmentIdTo =='' && $PurchasedDateFrom !='' && $PurchasedDateTo !='' && $AssetBookValueFrom !='' && $AssetBookValueTo !=''){
            
            $response = AssetRegister::whereBetween('assetTypeId', array($AssetTypeFrom,$AssetTypeTo))->whereBetween('CompanyId', array($CompanyIdFrom,$CompanyIdTo))->whereBetween('purchasedDate', array($PurchasedDateFrom,$PurchasedDateTo))->whereBetween('AssetBookValue', array($AssetBookValueFrom,$AssetBookValueTo))->orderBy('id', 'desc')->get();
            
           $count = AssetRegister::whereBetween('assetTypeId', array($AssetTypeFrom,$AssetTypeTo))->whereBetween('DepartmentId', array($DepartmentIdFrom,$DepartmentIdTo))->count();
          
            
        }else if($PurchasedDateFrom != '' && $PurchasedDateTo !=''){
           
            $response = AssetRegister::whereBetween('purchasedDate', array($PurchasedDateFrom,$PurchasedDateTo))->orderBy('id', 'desc')->get();
           $count = AssetRegister::whereBetween('purchasedDate', array($PurchasedDateFrom,$PurchasedDateTo))->count();
        
        }else if($DepartmentIdFrom != '' && $DepartmentIdTo !=''){
           
           $response = AssetRegister::whereBetween('DepartmentId', array($DepartmentIdFrom,$DepartmentIdTo))->orderBy('id', 'desc')->get();
           $count = AssetRegister::whereBetween('DepartmentId', array($DepartmentIdFrom,$DepartmentIdTo))->count();
        
        }else if($CompanyIdFrom != '' && $CompanyIdTo !=''){
           
            $response = AssetRegister::whereBetween('CompanyId', array($CompanyIdFrom,$CompanyIdTo))->orderBy('id', 'desc')->get();
           $count = AssetRegister::whereBetween('CompanyId', array($CompanyIdFrom,$CompanyIdTo))->count();
           
        }else if($AssetBookValueFrom != '' && $AssetBookValueTo !=''){
           
            $response = AssetRegister::whereBetween('AssetBookValue', array($AssetBookValueFrom,$AssetBookValueTo))->orderBy('id', 'desc')->get();
           $count = AssetRegister::whereBetween('AssetBookValue', array($AssetBookValueFrom,$AssetBookValueTo))->count();
           
        }else{
           
         $response = AssetRegister::orderBy('id','desc')->get();
         $count = AssetRegister::count();
                
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