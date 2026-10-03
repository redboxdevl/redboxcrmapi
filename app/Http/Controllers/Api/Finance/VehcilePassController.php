<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\VehcilepassRequest;
use App\Models\Finance\Vehcilepass;
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

class VehcilePassController extends Controller
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
            
                $response = Vehcilepass::orderBy($orderBy, $orderType)->paginate($perPage);
                $count = Vehcilepass::count();
       
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Vehcile Pass List Found',
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
            
            $response = Vehcilepass::orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Vehcilepass::count();
        
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Vehcile Pass List Found',
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
    public function store(VehcilepassRequest $request)
    {
        
        $response = Vehcilepass::create([
            'agentId' =>  $request->agentId,
            'PassDatetime'=>$request->PassDatetime,
            'DepartmentId' =>  $request->DepartmentId,
            'VehcileType' =>  $request->VehcileType,
            'PlaceToVisit' =>  $request->PlaceToVisit,
            'PurposeofVisit' =>  $request->PurposeofVisit,
            'Meterreading' =>  $request->Meterreading,
            'TimeIn' => $request->TimeIn,
            'TimeOut' =>  $request->TimeOut,
            'MeterreadingArrival' =>  $request->MeterreadingArrival,
            'DistanceCovered' =>  $request->DistanceCovered,
            'TotalDuration' =>  $request->TotalDuration,
            'status' =>  $request->status,
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
    public function show(Vehcilepass $vehcilepass , $id="")
    {
        $response = Vehcilepass::where(['id'=>$id])->get();
            
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
    public function update(VehcilepassRequest $request, Vehcilepass $vehcilepass,$id="")
    {
        
        
        $checkFiles = Vehcilepass::findOrFail($id);
        
        Vehcilepass::where(['id'=>$id])->update([
            'PassDatetime'=>$request->PassDatetime,
            'DepartmentId' =>  $request->DepartmentId,
            'VehcileType' =>  $request->VehcileType,
            'PlaceToVisit' =>  $request->PlaceToVisit,
            'PurposeofVisit' =>  $request->PurposeofVisit,
            'Meterreading' =>  $request->Meterreading,
            'TimeIn' => $request->TimeIn,
            'TimeOut' =>  $request->TimeOut,
            'MeterreadingArrival' =>  $request->MeterreadingArrival,
            'DistanceCovered' =>  $request->DistanceCovered,
            'TotalDuration' =>  $request->TotalDuration,
            'status' =>  $request->status,
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
    public function destroy(VehcilepassRequest $request, Vehcilepass $vehcilepass)
    {
        $vehcilepass->delete($request->all());

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