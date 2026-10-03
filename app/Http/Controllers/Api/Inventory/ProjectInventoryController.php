<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Requests\Inventory\ProjectInventoryRequest;
use App\Imports\ProjectInventoryImports;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Models\Inventory\Projectinventory;
use App\Models\Inventory\Inventorysold;
use App\Models\Inventory\Projectcategory;
use App\Models\AffiliateProgram\AffiliateRegisterModule;
use App\Models\AffiliateProgram\AffiliateLeadsModule;
use App\Models\ListMyLead;
use App\Models\HR\Employee;
use App\Exports\ExportInventories;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;


class ProjectInventoryController extends Controller
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
        $proid = $request->proid;
        $q = $request->q ?? "";
        $InventoriesStatus = $request->InventoriesStatus ?? "";
      
        $blockName = $request->blockName ?? "";
        $categoryName = $request->categoryName ?? "";
        
        if(!empty($proid) && empty($q) && empty($InventoriesStatus) && empty($blockName) && empty($categoryName)){
            
           
            $response = Projectinventory::with(['Holdinventories','ProjectData' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['project_id'=>$proid])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Projectinventory::where(['project_id'=>$proid])->whereNull('status')->count();
        }else if(!empty($proid) && !empty($q)){
            
          
            $response = Projectinventory::with(['Holdinventories','ProjectData' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where('project_id',$proid)->where('jsonFormat->property_no', $q)
                ->get();
          
            $count = Projectinventory::where('project_id',$proid)->where('jsonFormat->property_no', $q)->count();
            
            
            
        }else if(!empty($proid) && !empty($InventoriesStatus) && empty($q) && empty($blockName) && empty($categoryName)){
         
          
            
            if($InventoriesStatus == 'remaining'){
                $response = Projectinventory::with(['Holdinventories','ProjectData' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where('project_id',$proid)->where('status',null)
                ->orderBy($orderBy, $orderType)->paginate($perPage);
          
            $count = Projectinventory::where('project_id',$proid)->where('status',null)->count();
            }else{
                $response = Projectinventory::with(['Holdinventories','ProjectData' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where('project_id',$proid)->where('status', $InventoriesStatus)
                ->orderBy($orderBy, $orderType)->paginate($perPage);
          
            $count = Projectinventory::with(['Holdinventories','ProjectData' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where('project_id',$proid)->where('status', $InventoriesStatus)
                ->count();
            }
         
            
            
        }else if(!empty($proid) && empty($InventoriesStatus) && empty($q) && !empty($blockName) && empty($categoryName)){
         
           
            $response = Projectinventory::with(['Holdinventories','ProjectData' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ,])->where('project_id',$proid)->where('jsonFormat->block', $blockName)
            ->get();
            
            $count = Projectinventory::where('project_id',$proid)->where('jsonFormat->block', $blockName)->count();
         
            
            
        }else if(!empty($proid) && empty($InventoriesStatus) && empty($q) && empty($blockName) && !empty($categoryName)){
         
           
            $response = Projectinventory::with(['Holdinventories','ProjectData' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ,])->where('project_id',$proid)->where('jsonFormat->property_category', $categoryName)
            ->get();
            
            $count = Projectinventory::where('project_id',$proid)->where('jsonFormat->property_category', $categoryName)->count();
         
            
            
        }else if(!empty($proid) && empty($InventoriesStatus) && empty($q) && !empty($blockName) && !empty($categoryName)){
         
           
            $response = Projectinventory::with(['Holdinventories','ProjectData' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ,])->where('project_id',$proid)->where('jsonFormat->property_category', $categoryName)->where('jsonFormat->block', $blockName)
            ->get();
            
            $count = Projectinventory::where('project_id',$proid)->where('jsonFormat->property_category', $categoryName)->where('jsonFormat->block', $blockName)->count();
         
            
            
        }else{
            
            $response = Projectinventory::with(['Holdinventories','ProjectData' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Projectinventory::WhereIn('status', array('hold'))->count();
        }
        
        $allInventories = Projectinventory::where(['project_id'=>$proid])->count();
        $SoldInventories = Projectinventory::where(['project_id'=>$proid,'status'=>'sold'])->count();
        $HoldInventories = Projectinventory::where(['project_id'=>$proid,'status'=>'hold'])->count();
        
        $totalMerge = $SoldInventories+$HoldInventories;
        
        $remainingInvet = $allInventories - $totalMerge;
        
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
                'totalInventories'=>$allInventories,
                'SoldInventories'=>$SoldInventories,
                'HoldInventories'=>$HoldInventories,
                'RemainingInventories'=>$remainingInvet,
                'TotalCount'=>$count,
            ]);
        }

        // FAIL
        else {
            return app(CommonController::class)->getEmptyListResponse();
        }
    }

    // PREPARE LIST
    public function renderList(Object $apiResponse)
    {
        $data = [];

        foreach ($apiResponse as $key => $value) {
            
            $proTypes = Projectcategory::where(['project_id'=>$value->project_id])->get();
            
            
            
            if($value->Holdinventories == ''){
                $array="";
            }else{
                
                $expolode = explode(":",$value->Holdinventories->hold_time);
                $HOURS = (int) $expolode[0];
                $Min = (int) $expolode[1];
                $SEC = (int) $expolode[2];
                
                $now = Carbon::now();
            
            $to = Carbon::parse($value->Holdinventories->hold_date);
            $from = Carbon::parse($now);
            
            $years = $to->diffInYears($from);
            $months = $to->diffInMonths($from);
            $weeks = $to->diffInWeeks($from);
            $days = $to->diffInDays($from);
            $hours = $to->diffInHours($from);
            $minutes = $to->diffInMinutes($from);
            $seconds = $to->diffInSeconds($from);
            
            // $finalVar = "Year- ".$years." Month- ".$months." Weeks- ".$weeks." Days- ".$days." hours- ".$hours." minutes- ".$minutes." seconds- ".$seconds;
            
            $array = array(
                'days'=>$days,
                'hours'=>$HOURS,
                'minutes'=>$Min,
                'seconds'=>$SEC,
            );
            
            }
            
            if($value->Holdinventories == ''){
                $HoldData="";
                $agentDetails = "";
            }else{
                $customerD = ListMyLead::where(['id'=>$value->Holdinventories->customer_id])->first();
                $HoldData = $customerD;
                
                $agentDetails = Employee::where(['id'=>$value->Holdinventories->employee_id])->first();
            }
            
            
            
            
            array_push($data, (object) [
                "id" => $value->id,
                "jsonFormat" => json_decode($value->jsonFormat,JSON_UNESCAPED_SLASHES),
                "project_id" => $value->project_id,
                "project_name" => $value->ProjectData == '' ? '' : $value->ProjectData->project_name,
                "status" => $value->status,
                "proTypes"=>$proTypes,
                "customer_id" => $value->Holdinventories == '' ? '' : $value->Holdinventories->customer_id,
                "clientName"=>$HoldData == '' ? '' : $HoldData->clientName,
                "employee_id" => $value->Holdinventories == '' ? '' : $value->Holdinventories->employee_id,
                "hold_date" => $value->Holdinventories == '' ? '' : $value->Holdinventories->hold_date,
                "hold_time" => $value->Holdinventories == '' ? '' : $value->Holdinventories->hold_time,
                "timer_data" => $array,
                "hold_desc" => $value->Holdinventories == '' ? '' : $value->Holdinventories->hold_desc,
                "updated_at" => $value->Holdinventories == '' ? '' : $value->updated_at,
                "deleted_at" => $value->Holdinventories == '' ? '' : $value->deleted_at,
                "agentDetails"=>$agentDetails,
                "Holdinventories"=>$value->Holdinventories,
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
    public function store(ProjectInventoryRequest $request)
    {

        // $response = Projectinventory::create($request->all());
        Excel::import(new ProjectInventoryImports,request()->file('filter_csv'));

        return response()->json([
            'message' => "Data saved successfully!",
            'code' => '200'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inventory\Projectinventory  $projectInventory
     * @return \Illuminate\Http\Response
     */
    public function show(Projectinventory $projectinventory)
    {
        
        //$response = Projectinventory::findOrFail($projectinventory->id);
        $response = Projectinventory::with(['HoldInventories' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['id'=>$projectinventory->id])->get();
       if (count($response) > 0) {
            $data = $this->renderList($response);

            return response()->json([
                'code' => '200',
                'success' => true,
                'message' => 'Record Found',
                'data' => $data,
            ]);
        }

        // FAIL
        else {
            return app(CommonController::class)->getEmptyListResponse();
        }
        // return response()->json([
        //     'message' => "Data retrieved successfully!",
        //     'data' => $response
        // ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inventory\Projectinventory  $projectinventory
     * @return \Illuminate\Http\Response
     */
    public function update(ProjectInventoryRequest $request, Projectinventory $projectinventory)
    {
        $projectinventory->update($request->all());

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $projectinventory
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inventory\Projectinventory  $projectinventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProjectInventoryRequest $request, Projectinventory $projectinventory)
    {
        $projectinventory->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
        ], 200);
    }
    
    
    public function getProjectWiseBlock(Request $request){
        
        $proid = $request->proid;
        
        
        $proBlock = Projectinventory::where(['project_id'=>$proid])->whereNull('status')->groupBy('jsonFormat->block')->select('jsonFormat->block as BlockName', DB::raw('count(*) as total'))->get();
        
        $PlotNoData = Projectinventory::where(['project_id'=>$proid])->whereNull('status')->groupBy('jsonFormat->property_no')->select('jsonFormat->property_no as propertyNo', DB::raw('count(*) as total'))->orderBy('id', 'asc')->get();

        $blockData = Projectinventory::where(['project_id'=>$proid])->whereNull('status')->groupBy('jsonFormat->block')->select('jsonFormat->block as BlockName', DB::raw('count(*) as total'))->orderBy('id', 'asc')->get();
        
        $sizeData = Projectinventory::where(['project_id'=>$proid])->whereNull('status')->groupBy('jsonFormat->size')->select('jsonFormat->size as SizeName', DB::raw('count(*) as total'))->orderBy('id', 'asc')->get();
        
        $categoryData = Projectinventory::where(['project_id'=>$proid])->whereNull('status')->groupBy('jsonFormat->property_category')->select('jsonFormat->property_category as CategoryName', DB::raw('count(*) as total'))->orderBy('id', 'asc')->get();
        
        return response()->json([
            'data' => $proBlock,
            'PlotNoData'=>$PlotNoData,
            'blockData'=>$blockData == null || $blockData == '' ? '' : $blockData,
            'sizeData' => $sizeData == null || $sizeData == '' ? '' : $sizeData,
            'categoryData'=> $categoryData == null || $categoryData == '' ? '' : $categoryData,
        ], 200);
       
    }
    
    public function getBlockWisePlot(Request $request){
        
        $blockName = $request->blockName;
        $proid = $request->proid;
        
        if($blockName == '' || $proid == 2){
            
             $proPlot = Projectinventory::where(['project_id'=>$proid])->whereNull('status')->groupBy('jsonFormat->property_no')->select('jsonFormat->property_no as propertyNo', DB::raw('count(*) as total'))->orderBy('id', 'asc')->get();
                
                return response()->json([
                    'data' => $proPlot,
                ], 200);
        }else{
            $proPlot = Projectinventory::where(['jsonFormat->block'=>$blockName,'project_id'=>$proid])->whereNull('status')->groupBy('jsonFormat->property_no')->select('jsonFormat->property_no as propertyNo', DB::raw('count(*) as total'))->orderBy('id', 'asc')->get();
            
            return response()->json([
                'data' => $proPlot,
            ], 200);
        }
        
        
      
        
       
    }
    
     public function getPlotWiseSize(Request $request){
        
        $BlockId = $request->BlockId;
        $plotNo = $request->plotNo;
        $proid = $request->proid;
       
        if($plotNo == '' && $proid == ''){
            
            return response()->json([
                'code' => '203',
            ], 203);
            
        }else  if($plotNo == '' || $proid != ''){
            
            $proPlot = Projectinventory::where(['project_id'=>$proid,'jsonFormat->block'=>$BlockId])->first();
            
            $FormatDx = json_decode($proPlot->jsonFormat,JSON_UNESCAPED_SLASHES);
            
            return response()->json([
                'data' => array(
                    "id" => $proPlot->id,
                    "size" => $FormatDx['size'],
                    "project_id" => $proPlot->project_id,
                    "jsonFormat"=>$FormatDx
                 ),
            ], 200);
            
        }else{
            $proPlot = Projectinventory::where(['project_id'=>$proid,'jsonFormat->block'=>$BlockId])->first();
            
            $FormatDx = json_decode($proPlot->jsonFormat,JSON_UNESCAPED_SLASHES);
            
            return response()->json([
                'data' => array(
                    "id" => $proPlot->id,
                    "size" => $FormatDx['size'],
                    "project_id" => $proPlot->project_id,
                    "jsonFormat"=>$FormatDx
                 ),
            ], 200);
        }
       
        
        
        
        
       
    }
    
    
    public function getCustomerSold(Request $request){
        
        $agentId = $request->agentId;
        
        $myArr = [];
        
        if($agentId == 'all'){
            $Inventorysold = Inventorysold::orderBy('id','desc')->get();
            foreach($Inventorysold as $invData){
                if($invData->Mystatus == 'affiliate'){
                    
                    $LeadData = AffiliateLeadsModule::where(['id'=>$invData->customer_id])->get();
                   
                    foreach($LeadData as $leadInof){
                        $myArr[]=array(
                         'clientName' => $leadInof->clientName,
                         'id' => $leadInof->id,
                         'status' => 'affiliates',
                         'JsonFormat' => $invData
                        );
                    }
                    
                
                    
                }else{
                    
                    $LeadData = ListMyLead::where(['id'=>$invData->customer_id])->first();
                    
                    $myArr[]=array(
                     'clientName' => $LeadData->clientName,
                     'id' => $LeadData->id,
                      'status' => 'normal',
                      'JsonFormat' => $invData
                    );
                    
                    
                }
            }
        }else{
            $Inventorysold = Inventorysold::where(['employee_id'=>$agentId])->get();
            foreach($Inventorysold as $invData){
                if($invData->Mystatus == 'affiliate'){
                    
                    $LeadData = AffiliateLeadsModule::where(['id'=>$invData->customer_id])->first();
                    $myArr[]=array(
                     'clientName' => $LeadData->clientName,
                     'status' => 'affiliates',
                     'id' => $LeadData->id,
                     'JsonFormat' => $invData
                    );
                
                    
                }else{
                    
                    $LeadData = ListMyLead::where(['id'=>$invData->customer_id])->first();
                    
                    $myArr[]=array(
                     'clientName' => $LeadData->clientName,
                     'id' => $LeadData->id,
                      'status' => 'normal',
                      'JsonFormat' => $invData
                     
                    );
                    
                }
            }
        }
        
        
         return response()->json([
                'data' => $myArr,
            ], 200);
        
        
    }
    
    
    public function getCustomerSoldDetails (Request $request){
        
        $customerId = $request->customerId;
        
        if(empty($customerId)){
            
            $AffiliateLeadsModule="";
            $Inventorysold="";
            
        }else{
            
            $explode = explode("-",$customerId);
            $custId = $explode[0];
            $cusStatus = $explode[1];
            
            if($cusStatus == 'affiliates'){
                
                $AffiliateLeadsModule = AffiliateLeadsModule::where(['id'=>$custId])->first();
                $Inventorysold = Inventorysold::where(['customer_id'=>$custId])->first();
                
            }else{
                $AffiliateLeadsModule = ListMyLead::where(['id'=>$custId])->first();
                $Inventorysold = Inventorysold::where(['customer_id'=>$custId])->first();
            }
            
        }
        
        
        
         return response()->json([
                'AffiliateLeadsModule' => $AffiliateLeadsModule,
                'Inventorysold'=>$Inventorysold,
            ], 200);
        
    }
    
    
    public function ListInventoriesExport (Request $request) {
        
        //  ini_set('memory_limit','-1');
           
        $status = $request->status;
        $proid = $request->proid;
        
        return Excel::download(new ExportInventories($status,$proid), 'InventoryList.xlsx');
       
        
        // $data = Excel::store(new AssignLeadsExport($agentId), 'users.xlsx');
        
        return response()->json([
            'message' => "Data Retrieve successfully!",
            'data' => $status,
            'code'=>'200'
        ], 200);
        
    }
    
    
    
    
    
}
