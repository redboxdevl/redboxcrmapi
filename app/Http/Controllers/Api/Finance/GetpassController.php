<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\GetpassRequest;
use App\Models\Finance\Getpass;
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

class GetpassController extends Controller
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
        
        
        if (is_null($agentId)) {
            
                
                $response = Getpass::orderBy($orderBy, $orderType)->paginate($perPage);
                $count = Getpass::count();
       
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Get Pass List Found',
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
            
            $response = Getpass::orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Getpass::count();
        
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Get Pass List Found',
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
                "deleted_at" => $value->deleted_at,
                'agentId'=>$value->agentId,
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
    public function store(GetpassRequest $request)
    {
        
        if($request->hasFile("AssetImage1")){
            $image = $request->file("AssetImage1");
            $ext = $image->extension();
            $image_name="gatepass1-".time().'.'.$ext;
            $image->storeAs('/public/AssetImages/',$image_name);
             $FileData = "public/storage/AssetImages/".$image_name;
            
        }else{
            $FileData="";
        }
        
        if($request->hasFile("AssetImage2")){
            $image = $request->file("AssetImage2");
            $ext = $image->extension();
            $image_name="gatepass2-".time().'.'.$ext;
            $image->storeAs('/public/AssetImages2/',$image_name);
            $FileData2 = "public/storage/AssetImages2/".$image_name;
            
        }else{
            $FileData2="";
        }
       
        
        $response = Getpass::create([
            'assetId' =>  $request->assetId,
            'GatePassDate'=>$request->GatePassDate,
            'GatePassTime' =>  $request->GatePassTime,
            'Quantity' =>  $request->Quantity,
            'PlaceToGo' =>  $request->PlaceToGo,
            'Purpose' =>  $request->Purpose,
            'AssetImage1' =>  $FileData,
            'AssetImage2' => $FileData2,
            'status' =>  $request->status,
            'agentId' => $request->agentId,
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