<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Models\ListMyLead;
use App\Models\LeadImpression;
use App\Models\CustomerActivity;
use App\Models\Zongcalling;
use App\Models\HR\Employee;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;
use DB;

class TalktimeHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $agentId = $request->agentId;
        $orderBy = $request->orderBy ?? "id";
        $orderType = $request->orderType ?? "asc";
        $perPage = $request->perPage ?? "12";
        $page = $request->page ?? "1";
        $datefrom = $request->datefrom ?? "";
        $dateto = $request->dateto ?? "";

        if (!empty($agentId) && empty($datefrom) && empty($dateto)) {
          
            $Employee = Employee::where(['id'=>$agentId])->first();
            
            $Zongcalling = Zongcalling::whereIn('ext',[$Employee->incommingPhone,$Employee->outgoingPhone])->where(DB::raw('date(datetime)'),date("Y-m-d"))->orderBy($orderBy,$orderType)->get();
            
            $count = Zongcalling::whereIn('ext',[$Employee->incommingPhone,$Employee->outgoingPhone])->where(DB::raw('date(datetime)'),date("Y-m-d"))->count();
        
        $data = $this->renderList($Zongcalling,$agentId);
            
            return response()->json([
                'code' => '200',
                'success' => true,
                'message' => 'Calling History List Found',
                'data' => $data,
                'orderBy' => $orderBy,
                'orderType' => $orderType,
                'perPage' => $perPage,
                'pageNo' => $page,
                'TotalCount'=>$count,
                'empName'=>$Employee == '' || $Employee == null ? '' : $Employee->emp_name
                // 'LeadImpression'=>$LeadImpression,
            ], 200);
        
        } else if (!empty($agentId) && !empty($datefrom) && !empty($dateto)) {
          
            $Employee = Employee::where(['id'=>$agentId])->first();
            
            $Zongcalling = Zongcalling::whereIn('ext',[$Employee->incommingPhone,$Employee->outgoingPhone])->whereBetween(DB::raw('DATE(datetime)'), [$datefrom,$dateto])->orderBy($orderBy,$orderType)->get();
            
            $count = Zongcalling::whereIn('ext',[$Employee->incommingPhone,$Employee->outgoingPhone])->whereBetween(DB::raw('DATE(datetime)'), [$datefrom,$dateto])->count();
        
        $data = $this->renderList($Zongcalling,$agentId);
            
            return response()->json([
                'code' => '200',
                'success' => true,
                'message' => 'Calling History List Found',
                'data' => $data,
                'orderBy' => $orderBy,
                'orderType' => $orderType,
                'perPage' => $perPage,
                'pageNo' => $page,
                'TotalCount'=>$count,
                'empName'=>$Employee == '' || $Employee == null ? '' : $Employee->emp_name
                // 'LeadImpression'=>$LeadImpression,
            ], 200);
        
        } 
        // AGENT ID NOT SELECTED
        else {
            return app(CommonController::class)->getExceptionResponse("Agent Id is required");
        }
    }

    
    public function renderList(Object $apiResponse,$agentId)
    {
        $data = [];

        foreach ($apiResponse as $key => $value) {
            
            
                 array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "datetime"=>$value->datetime,
                "phone"=>$value->phone,
                "recording"=>$value->recording,
                "status"=>$value->status,
                "Type"=>$value->Type,
                "duration"=>$value->duration,
                "ext"=>$value->ext,
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
    public function store(BanklistRequest $request)
    {
        
       
        
        return response()->json([
            'message' => "Data saved successfully!",
            'data' => '',
            'code'=>'200'
        ], 200);
        
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Banklist $banklist)
    {
       return response()->json([
                'message' => "Data Not Found!",
                'code'=>'203'
            ], 203);
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(BanklistRequest $request, Banklist $banklist,$id="")
    {

       
        
        return response()->json([
            'message' => "Data updated successfully!",
            'data' => '',
            'code'=>'200'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(BanklistRequest $request, Banklist $banklist)
    {
       
        return response()->json([
            'message' => "Data deleted successfully!",
            'code'=>'200'
        ], 200);
    }
    
    
    
    
}