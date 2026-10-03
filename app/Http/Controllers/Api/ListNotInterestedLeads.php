<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Models\ListAssignLeads;
use App\Models\ListHotLead;
use App\Models\ListNotInterested;
use App\Models\HR\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ListNotInterestedLeads extends Controller
{
    
    public function listNotInterestedLeads(Request $request){
        
        $orderBy = $request->orderBy ?? "id";
        $orderType = $request->orderType ?? "desc";
        $perPage = $request->perPage ?? 10;
        $page = $request->page ?? 1;
      
        $agentId = $request->agentId;
        $teamObj = $request->teamObj ?? "";
        $SalesConsul = $request->SalesConsul ?? "";
        
        if (is_null($agentId) && empty($teamObj) && empty($SalesConsul)) {
        
            $response = ListNotInterested::with(['getreminder','getleads_no' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ,])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = ListNotInterested::with(['getreminder','getleads_no' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ,])->orderBy($orderBy, $orderType)->count();
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);
            
                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'employee Found',
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
        }else if($agentId == 'all' && empty($teamObj) && empty($SalesConsul)){
        
            $response = ListNotInterested::with(['getreminder','getleads_no' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ,])->orderBy($orderBy, $orderType)->groupBy('leadid')->paginate($perPage);
            $count = ListNotInterested::with(['getreminder','getleads_no' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ,])->orderBy($orderBy, $orderType)->groupBy('leadid')->get()->count();
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);
            
                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'employee Found',
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
        
        }else if($agentId == 'all' && !empty($teamObj) && empty($SalesConsul)){
            
            $myArray2 = explode(',', $teamObj);
            $myArray = str_replace(array('[',']'),'',$myArray2);
            
            $response = ListNotInterested::with(['getreminder','getleads_no' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ,])->whereIn('agentid',$myArray)->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = ListNotInterested::with(['getreminder','getleads_no' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ,])->whereIn('agentid',$myArray)->orderBy($orderBy, $orderType)->count();
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);
            
                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'employee Found',
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
        
        }else if($agentId == 'all' && !empty($teamObj) && !empty($SalesConsul)){
            
            $myArray2 = explode(',', $teamObj);
            $myArray = str_replace(array('[',']'),'',$myArray2);
            
            $response = ListNotInterested::with(['getreminder','getleads_no' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ,])->where(['agentid'=>$SalesConsul])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = ListNotInterested::with(['getreminder','getleads_no' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ,])->where(['agentid'=>$SalesConsul])->orderBy($orderBy, $orderType)->count();
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);
            
                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'employee Found',
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
            $response = ListNotInterested::with(['getreminder','getleads_no' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ,])->where(['agentid'=>$agentId])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = ListNotInterested::with(['getreminder','getleads_no' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ,])->where(['agentid'=>$agentId])->orderBy($orderBy, $orderType)->count();
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);
            
                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'employee Found',
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
            
            if($value->customerId == null){
                $LeadTypes = "Assign Leads";
            }else{
                $LeadTypes = "My Leads";
            }
            
            $Employee = Employee::where(['id'=>$value->agentid])->first();
           
            $timeSince = Carbon::parse($value->created_at)->diffForHumans();
            
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "clientName" => $value->getleads_no == null ? '' : $value->getleads_no->clientName,
               "clientProject" => $value->getleads_no == null ? '' : $value->getleads_no->clientProject,
               "clientDesignation" => $value->getleads_no == null ? '' : $value->getleads_no->clientDesignation,
               "clientPhone" => $value->getleads_no == null ? '' : $value->getleads_no->clientPhone,
               "budgetPrice" => $value->budgetPrice,
               "reasonTitle" => $value->reasonTitle,
               'emp_name' => $Employee == null ? '' : $Employee->emp_name,
               'LeadType' => $LeadTypes,
               'leadid' => $value->leadid,
               "timeSince"=>$timeSince,
                "PublishDate"=>date("d F Y",strtotime($value->created_at)),
                "PublishTime"=>date("H:i",strtotime($value->created_at)),
            ]);
        }

        return $data;
    }

}
