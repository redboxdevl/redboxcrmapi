<?php

namespace App\Http\Controllers\Api;

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
use App\Models\ListMyLead;
use App\Models\HR\Employee;
use App\Models\Zongcalling;
use App\Models\ActivityTypes;
use Validator;
use Mail;
use DateTime;
use DB;

class Listsuperdatabasecontroller extends Controller
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
        $search = $request->search ?? "";
        $ProjectN = $request->ProjectN ?? "";
        $LeadStatys = $request->LeadStatys ?? "";
        $Status = $request->Status ?? "";
        $ReminderStatus = $request->ReminderStatus ?? "";
        $SalesConsul = $request->SalesConsul ?? "";
        $ReminderPriority = $request->ReminderPriority ?? "";
        $company_id = $request->company_id ?? "";
        
        $StartDates = $request->StartDates ?? "";
        $EndDate = $request->EndDate ?? "";
        
        if(empty($q) && empty($agentId) && empty($search) && empty($ProjectN) && empty($LeadStatys) && empty($Status) && empty($ReminderStatus) && empty($StartDates) && empty($EndDate) && empty($SalesConsul) && empty($ReminderPriority)){
            
            $Employee = Employee::where(['id'=>$agentId,'company_id'=>$company_id])->first();
        
            $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->where(['agentid'=>$agentId,'ReasonStatus'=>'No'])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = ListMyLead::where(['agentid'=>$agentId,'ReasonStatus'=>'No','companyId'=>$company_id])->count();
            
            // SUCCESS
            if (count($response) > 0) {
            $data = $this->renderList($response);
            
            return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Leads List Found',
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
            
        }else if(empty($q) && !empty($agentId) && !empty($search) && empty($ProjectN) && empty($LeadStatys) && empty($Status) && empty($ReminderStatus) && empty($StartDates) && empty($EndDate) && empty($SalesConsul) && empty($ReminderPriority)){
            
             $Employee = Employee::where(['id'=>$agentId,'company_id'=>$company_id])->first();
            
            if($agentId == 'all'){
                
                $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ])->where(['ReasonStatus'=>'No','companyId'=>$company_id])->where('mobileNo','LIKE',"%".$search."%")->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = ListMyLead::where(['ReasonStatus'=>'No','companyId'=>$company_id])->where('mobileNo','LIKE',"%".$search."%")->count();
                
            }else{
                
                $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ])->where(['agentid'=>$agentId,'ReasonStatus'=>'No','companyId'=>$company_id])->where('mobileNo','LIKE',"%".$search."%")->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = ListMyLead::where(['agentid'=>$agentId,'ReasonStatus'=>'No','companyId'=>$company_id])->where('mobileNo','LIKE',"%".$search."%")->count();
                
            }
            
            // SUCCESS
            if (count($response) > 0) {
            $data = $this->renderList($response);
            
            return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Leads List Found',
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
            
        }else if(empty($q) && $agentId == 'all' && empty($search) && empty($ProjectN) && empty($LeadStatys) && empty($Status) && empty($ReminderStatus) && empty($StartDates) && empty($EndDate) && empty($SalesConsul)  && empty($ReminderPriority)){
            
            $Employee = Employee::where(['id'=>$agentId,'company_id'=>$company_id])->first();
        
            $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->where(['ReasonStatus'=>'No','companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = ListMyLead::where(['ReasonStatus'=>'No','companyId'=>$company_id])->count();
            
            // SUCCESS
            if (count($response) > 0) {
            $data = $this->renderList($response);
            
            return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Leads List Found',
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
            
        }else if(empty($q) && $agentId == 'all' && empty($search) && empty($ProjectN) && empty($LeadStatys) && empty($Status) && empty($ReminderStatus) && !empty($StartDates) && !empty($EndDate) && empty($SalesConsul) && empty($ReminderPriority)){
            
            $Employee = Employee::where(['id'=>$agentId,'company_id'=>$company_id])->first();
        
            $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->where(['ReasonStatus'=>'No','companyId'=>$company_id])->whereBetween(DB::raw('DATE(`created_at`)'),[$StartDates, $EndDate])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = ListMyLead::where(['ReasonStatus'=>'No','companyId'=>$company_id])->whereBetween(DB::raw('DATE(`created_at`)'),[$StartDates, $EndDate])->count();
            
            // SUCCESS
            if (count($response) > 0) {
            $data = $this->renderList($response);
            
            return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Leads List Found',
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
            
        }else if(empty($q) && !empty($agentId) && empty($search) && empty($ProjectN) && empty($LeadStatys) && empty($Status) && empty($ReminderStatus) && $agentId != 'all' && empty($StartDates) && empty($EndDate) && empty($SalesConsul) && empty($ReminderPriority)){
            
            $Employee = Employee::where(['id'=>$agentId,'company_id'=>$company_id])->first();
        
            $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->where(['agentid'=>$agentId,'ReasonStatus'=>'No','companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = ListMyLead::where(['agentid'=>$agentId,'ReasonStatus'=>'No','companyId'=>$company_id])->count();
            
            // SUCCESS
            if (count($response) > 0) {
            $data = $this->renderList($response);
            
            return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Leads List Found',
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
            
        }else if(empty($q) && $agentId == 'all' && empty($search) && empty($ProjectN) && empty($LeadStatys) && empty($Status) && empty($ReminderStatus) && !empty($StartDates) && !empty($EndDate) && !empty($SalesConsul) && empty($ReminderPriority)){
            
            $Employee = Employee::where(['id'=>$agentId,'company_id'=>$company_id])->first();
        
            $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->where(['agentId'=>$SalesConsul,'ReasonStatus'=>'No','companyId'=>$company_id])->whereBetween(DB::raw('DATE(`created_at`)'), [$StartDates, $EndDate])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = ListMyLead::where(['agentId'=>$SalesConsul,'ReasonStatus'=>'No','companyId'=>$company_id])->whereBetween(DB::raw('DATE(`created_at`)'), [$StartDates, $EndDate])->count();
            
            // SUCCESS
            if (count($response) > 0) {
            $data = $this->renderList($response);
            
            return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Leads List Found',
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
            
        }else if(empty($q) && $agentId == 'all' && empty($search) && empty($ProjectN) && empty($LeadStatys) && empty($Status) && empty($ReminderStatus) && empty($StartDates) && empty($EndDate) && !empty($SalesConsul) && empty($ReminderPriority)){
            
            $Employee = Employee::where(['id'=>$agentId,'company_id'=>$company_id])->first();
        
            $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->where(['agentId'=>$SalesConsul,'ReasonStatus'=>'No','companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = ListMyLead::where(['agentId'=>$SalesConsul,'ReasonStatus'=>'No','companyId'=>$company_id])->count();
            
            // SUCCESS
            if (count($response) > 0) {
            $data = $this->renderList($response);
            
            return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Leads List Found',
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
            
        }else if(empty($q) && $agentId == 'all' && empty($search) && !empty($ProjectN) && empty($LeadStatys) && empty($Status) && empty($ReminderStatus) && empty($StartDates) && empty($EndDate) && empty($SalesConsul) && empty($ReminderPriority)){
            
            $Employee = Employee::where(['id'=>$agentId,'company_id'=>$company_id])->first();
        
            $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->where(['projectid'=>$ProjectN,'ReasonStatus'=>'No','companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = ListMyLead::where(['projectid'=>$ProjectN,'ReasonStatus'=>'No','companyId'=>$company_id])->count();
            
            // SUCCESS
            if (count($response) > 0) {
            $data = $this->renderList($response);
            
            return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Leads List Found',
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
            
        }else if(empty($q) && $agentId == 'all' && empty($search) && !empty($ProjectN) && empty($LeadStatys) && empty($Status) && empty($ReminderStatus) && !empty($StartDates) && !empty($EndDate) && empty($SalesConsul) && empty($ReminderPriority)){
            
            $Employee = Employee::where(['id'=>$agentId,'company_id'=>$company_id])->first();
        
            $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->where(['projectid'=>$ProjectN,'ReasonStatus'=>'No','companyId'=>$company_id])->whereBetween(DB::raw('DATE(`created_at`)'), [$StartDates, $EndDate])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = ListMyLead::where(['projectid'=>$ProjectN,'ReasonStatus'=>'No','companyId'=>$company_id])->whereBetween(DB::raw('DATE(`created_at`)'), [$StartDates, $EndDate])->count();
            
            // SUCCESS
            if (count($response) > 0) {
            $data = $this->renderList($response);
            
            return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Leads List Found',
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
            
        }else if(empty($q) && $agentId == 'all' && empty($search) && empty($ProjectN) && !empty($LeadStatys) && empty($Status) && empty($ReminderStatus) && empty($StartDates) && empty($EndDate) && empty($SalesConsul) && empty($ReminderPriority)){
            
            $Employee = Employee::where(['id'=>$agentId,'company_id'=>$company_id])->first();
        
            $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->where(['clientInterst'=>$LeadStatys,'ReasonStatus'=>'No','companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = ListMyLead::where(['clientInterst'=>$LeadStatys,'ReasonStatus'=>'No','companyId'=>$company_id])->count();
            
            // SUCCESS
            if (count($response) > 0) {
            $data = $this->renderList($response);
            
            return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Leads List Found',
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
            
        }else if(empty($q) && $agentId == 'all' && empty($search) && empty($ProjectN) && !empty($LeadStatys) && empty($Status) && empty($ReminderStatus) && !empty($StartDates) && !empty($EndDate) && empty($SalesConsul) && empty($ReminderPriority)){
            
            $Employee = Employee::where(['id'=>$agentId,'company_id'=>$company_id])->first();
        
            $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->where(['clientInterst'=>$LeadStatys,'ReasonStatus'=>'No','companyId'=>$company_id])->whereBetween(DB::raw('DATE(`created_at`)'), [$StartDates, $EndDate])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = ListMyLead::where(['clientInterst'=>$LeadStatys,'ReasonStatus'=>'No','companyId'=>$company_id])->whereBetween(DB::raw('DATE(`created_at`)'), [$StartDates, $EndDate])->count();
            
            // SUCCESS
            if (count($response) > 0) {
            $data = $this->renderList($response);
            
            return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Leads List Found',
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
            
        }else if(empty($q) && $agentId == 'all' && empty($search) && !empty($ProjectN) && empty($LeadStatys) && empty($Status) && empty($ReminderStatus) && !empty($StartDates) && !empty($EndDate) && !empty($SalesConsul) && empty($ReminderPriority)){
            
            $Employee = Employee::where(['id'=>$agentId,'company_id'=>$company_id])->first();
        
            $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->where(['projectid'=>$ProjectN,'agentId'=>$SalesConsul,'ReasonStatus'=>'No','companyId'=>$company_id])->whereBetween(DB::raw('DATE(`created_at`)'), [$StartDates, $EndDate])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = ListMyLead::where(['projectid'=>$ProjectN,'agentId'=>$SalesConsul,'ReasonStatus'=>'No','companyId'=>$company_id])->whereBetween(DB::raw('DATE(`created_at`)'), [$StartDates, $EndDate])->count();
            
            // SUCCESS
            if (count($response) > 0) {
            $data = $this->renderList($response);
            
            return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Leads List Found',
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
            
        }else if(empty($q) && $agentId == 'all' && empty($search) && !empty($ProjectN) && empty($LeadStatys) && empty($Status) && empty($ReminderStatus) && empty($StartDates) && empty($EndDate) && !empty($SalesConsul) && empty($ReminderPriority)){
            
            $Employee = Employee::where(['id'=>$agentId,'company_id'=>$company_id])->first();
        
            $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->where(['projectid'=>$ProjectN,'agentId'=>$SalesConsul,'ReasonStatus'=>'No','companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = ListMyLead::where(['projectid'=>$ProjectN,'agentId'=>$SalesConsul,'ReasonStatus'=>'No','companyId'=>$company_id])->count();
            
            // SUCCESS
            if (count($response) > 0) {
            $data = $this->renderList($response);
            
            return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Leads List Found',
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
            
        }else if(empty($q) && $agentId == 'all' && empty($search) && empty($ProjectN) && !empty($LeadStatys) && empty($Status) && empty($ReminderStatus) && empty($StartDates) && empty($EndDate) && !empty($SalesConsul) && empty($ReminderPriority)){
            
            $Employee = Employee::where(['id'=>$agentId,'company_id'=>$company_id])->first();
        
            $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->where(['clientInterst'=>$LeadStatys,'agentId'=>$SalesConsul,'ReasonStatus'=>'No','companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = ListMyLead::where(['clientInterst'=>$LeadStatys,'agentId'=>$SalesConsul,'ReasonStatus'=>'No','companyId'=>$company_id])->count();
            
            // SUCCESS
            if (count($response) > 0) {
            $data = $this->renderList($response);
            
            return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Leads List Found',
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
            
        }else if(empty($q) && $agentId == 'all' && empty($search) && empty($ProjectN) && !empty($LeadStatys) && empty($Status) && empty($ReminderStatus) && !empty($StartDates) && !empty($EndDate) && !empty($SalesConsul) && empty($ReminderPriority)){
            
            $Employee = Employee::where(['id'=>$agentId,'company_id'=>$company_id])->first();
        
            $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->where(['clientInterst'=>$LeadStatys,'agentId'=>$SalesConsul,'ReasonStatus'=>'No','companyId'=>$company_id])->whereBetween(DB::raw('DATE(`created_at`)'), [$StartDates, $EndDate])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = ListMyLead::where(['clientInterst'=>$LeadStatys,'agentId'=>$SalesConsul,'ReasonStatus'=>'No','companyId'=>$company_id])->whereBetween(DB::raw('DATE(`created_at`)'), [$StartDates, $EndDate])->count();
            
            // SUCCESS
            if (count($response) > 0) {
            $data = $this->renderList($response);
            
            return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Leads List Found',
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
            
        }else if(empty($q) && $agentId == 'all' && empty($search) && empty($ProjectN) && empty($LeadStatys) && empty($Status) && empty($ReminderStatus) && empty($StartDates) && empty($EndDate) && empty($SalesConsul) && !empty($ReminderPriority)){
            
            $Employee = Employee::where(['id'=>$agentId,'company_id'=>$company_id])->first();
        
            $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->whereHas('getlastactivity', function($q) use($ReminderPriority){
                    $q->where(['ReminderPriority'=>$ReminderPriority]);
                })->where(['ReasonStatus'=>'No','companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->whereHas('getlastactivity', function($q) use($ReminderPriority){
                    $q->where(['ReminderPriority'=>$ReminderPriority]);
                })->where(['ReasonStatus'=>'No','companyId'=>$company_id])->count();
            
            // SUCCESS
            if (count($response) > 0) {
            $data = $this->renderList($response);
            
            return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Leads List Found',
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
            
        }else if(empty($q) && $agentId == 'all' && empty($search) && empty($ProjectN) && empty($LeadStatys) && empty($Status) && empty($ReminderStatus) && !empty($StartDates) && !empty($EndDate) && empty($SalesConsul) && !empty($ReminderPriority)){
            
            $Employee = Employee::where(['id'=>$agentId,'company_id'=>$company_id])->first();
        
            $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->whereHas('getlastactivity', function($q) use($ReminderPriority){
                    $q->where(['ReminderPriority'=>$ReminderPriority]);
                })->where(['ReasonStatus'=>'No','companyId'=>$company_id])->whereBetween(DB::raw('DATE(`created_at`)'), [$StartDates, $EndDate])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->whereHas('getlastactivity', function($q) use($ReminderPriority){
                    $q->where(['ReminderPriority'=>$ReminderPriority]);
                })->where(['ReasonStatus'=>'No','companyId'=>$company_id])->whereBetween(DB::raw('DATE(`created_at`)'), [$StartDates, $EndDate])->count();
            
            // SUCCESS
            if (count($response) > 0) {
            $data = $this->renderList($response);
            
            return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Leads List Found',
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
            
        }else if(empty($q) && $agentId == 'all' && empty($search) && empty($ProjectN) && empty($LeadStatys) && empty($Status) && empty($ReminderStatus) && empty($StartDates) && empty($EndDate) && !empty($SalesConsul) && !empty($ReminderPriority)){
            
            $Employee = Employee::where(['id'=>$agentId,'company_id'=>$company_id])->first();
        
            $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->whereHas('getlastactivity', function($q) use($ReminderPriority){
                    $q->where(['ReminderPriority'=>$ReminderPriority]);
                })->where(['agentId'=>$SalesConsul,'ReasonStatus'=>'No','companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->whereHas('getlastactivity', function($q) use($ReminderPriority){
                    $q->where(['ReminderPriority'=>$ReminderPriority]);
                })->where(['agentId'=>$SalesConsul,'ReasonStatus'=>'No','companyId'=>$company_id])->count();
            
            // SUCCESS
            if (count($response) > 0) {
            $data = $this->renderList($response);
            
            return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Leads List Found',
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
            
        }else if(empty($q) && $agentId == 'all' && empty($search) && empty($ProjectN) && empty($LeadStatys) && empty($Status) && empty($ReminderStatus) && !empty($StartDates) && !empty($EndDate) && !empty($SalesConsul) && !empty($ReminderPriority)){
            
            $Employee = Employee::where(['id'=>$agentId,'company_id'=>$company_id])->first();
        
            $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->whereHas('getlastactivity', function($q) use($ReminderPriority){
                    $q->where(['ReminderPriority'=>$ReminderPriority]);
                })->where(['agentId'=>$SalesConsul,'ReasonStatus'=>'No','companyId'=>$company_id])->whereBetween(DB::raw('DATE(`created_at`)'), [$StartDates, $EndDate])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->whereHas('getlastactivity', function($q) use($ReminderPriority){
                    $q->where(['ReminderPriority'=>$ReminderPriority]);
                })->where(['agentId'=>$SalesConsul,'ReasonStatus'=>'No','companyId'=>$company_id])->whereBetween(DB::raw('DATE(`created_at`)'), [$StartDates, $EndDate])->count();
            
            // SUCCESS
            if (count($response) > 0) {
            $data = $this->renderList($response);
            
            return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Leads List Found',
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
            
        }else if(empty($q) && $agentId == 'all' && empty($search) && empty($ProjectN) && empty($LeadStatys) && empty($Status) && !empty($ReminderStatus) && empty($StartDates) && empty($EndDate) && empty($SalesConsul) && empty($ReminderPriority)){
            
            if($ReminderStatus == 'Today'){
                
                $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ])->whereHas('ReminderData', function($q){
                    $q->where(DB::raw("DATE(reminderSet) = '".date('Y-m-d')."'"));
                })->where(['ReasonStatus'=>'No','companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
                $count = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ])->whereHas('ReminderData', function($q){
                    $q->where(DB::raw("DATE(reminderSet) = '".date('Y-m-d')."'"));
                })->where(['ReasonStatus'=>'No','companyId'=>$company_id])->count();
                
            }else if($ReminderStatus == 'Tomorrow'){
                
                $tommorrowDate = date('Y-m-d', strtotime(date("Y-m-d") .' +1 day'));
                
                 $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ])->whereHas('ReminderData', function($q) use($tommorrowDate){
                    $q->where(DB::raw('DATE(reminderSet)'), $tommorrowDate);
                })->where(['ReasonStatus'=>'No','companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
                $count = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ])->whereHas('ReminderData', function($q) use($tommorrowDate){
                    $q->where(DB::raw('DATE(reminderSet)'), $tommorrowDate);
                })->where(['ReasonStatus'=>'No','companyId'=>$company_id])->count();
                
            }else if($ReminderStatus == 'Overdue'){
                
                $tommorrowDate = date('Y-m-d', strtotime(date("Y-m-d") .' +1 day'));
                
                 $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ])->whereHas('ReminderData', function($q){
                    $q->where(DB::raw('DATE(reminderSet)'),'<', date('Y-m-d'));
                })->where(['ReasonStatus'=>'No','companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
                $count = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ])->whereHas('ReminderData', function($q) use($tommorrowDate){
                    $q->where(DB::raw('DATE(reminderSet)'),'<', date('Y-m-d'));
                })->where(['ReasonStatus'=>'No','companyId'=>$company_id])->count();
                
            }else{
                
                $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->where(['ReasonStatus'=>'No'])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = ListMyLead::where(['ReasonStatus'=>'No','companyId'=>$company_id])->count();
                
            }
            
            
            
            // SUCCESS
            if (count($response) > 0) {
            $data = $this->renderList($response);
            
            return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Leads List Found',
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
            
        }else if(empty($q) && $agentId != 'all' && empty($search) && empty($ProjectN) && empty($LeadStatys) && empty($Status) && !empty($ReminderStatus) && empty($StartDates) && empty($EndDate) && empty($SalesConsul) && empty($ReminderPriority)){
            
            if($ReminderStatus == 'Today'){
                
                $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ])->whereHas('ReminderData', function($q){
                    $q->where(DB::raw("DATE(reminderSet) = '".date('Y-m-d')."'"));
                })->where(['agentId'=>$agentId,'ReasonStatus'=>'No','companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
                $count = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ])->whereHas('ReminderData', function($q){
                    $q->where(DB::raw("DATE(reminderSet) = '".date('Y-m-d')."'"));
                })->where(['agentId'=>$agentId,'ReasonStatus'=>'No','companyId'=>$company_id])->count();
                
            }else if($ReminderStatus == 'Tomorrow'){
                
                $tommorrowDate = date('Y-m-d', strtotime(date("Y-m-d") .' +1 day'));
                
                 $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ])->whereHas('ReminderData', function($q) use($tommorrowDate){
                    $q->where(DB::raw('DATE(reminderSet)'), $tommorrowDate);
                })->where(['agentId'=>$agentId,'ReasonStatus'=>'No','companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
                $count = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ])->whereHas('ReminderData', function($q) use($tommorrowDate){
                    $q->where(DB::raw('DATE(reminderSet)'), $tommorrowDate);
                })->where(['agentId'=>$agentId,'ReasonStatus'=>'No','companyId'=>$company_id])->count();
                
            }else if($ReminderStatus == 'Overdue'){
                
                $tommorrowDate = date('Y-m-d', strtotime(date("Y-m-d") .' +1 day'));
                
                 $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ])->whereHas('ReminderData', function($q){
                    $q->where(DB::raw('DATE(reminderSet)'),'<', date('Y-m-d'));
                })->where(['agentId'=>$agentId,'ReasonStatus'=>'No','companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
                $count = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ])->whereHas('ReminderData', function($q) use($tommorrowDate){
                    $q->where(DB::raw('DATE(reminderSet)'),'<', date('Y-m-d'));
                })->where(['agentId'=>$agentId,'ReasonStatus'=>'No','companyId'=>$company_id])->count();
                
            }else{
                
                $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->where(['agentId'=>$agentId,'ReasonStatus'=>'No'])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = ListMyLead::where(['agentId'=>$agentId,'ReasonStatus'=>'No','companyId'=>$company_id])->count();
                
            }
            
            
            
            // SUCCESS
            if (count($response) > 0) {
            $data = $this->renderList($response);
            
            return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Leads List Found',
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
            
        }else if(empty($q) && $agentId == 'all' && empty($search) && empty($ProjectN) && empty($LeadStatys) && empty($Status) && !empty($ReminderStatus) && empty($StartDates) && empty($EndDate) && !empty($SalesConsul) && empty($ReminderPriority)){
            
            if($ReminderStatus == 'Today'){
                
                $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ])->whereHas('ReminderData', function($q){
                    $q->where(DB::raw("DATE(reminderSet) = '".date('Y-m-d')."'"));
                })->where(['agentId'=>$SalesConsul,'ReasonStatus'=>'No','companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
                $count = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ])->whereHas('ReminderData', function($q){
                    $q->where(DB::raw("DATE(reminderSet) = '".date('Y-m-d')."'"));
                })->where(['agentId'=>$SalesConsul,'ReasonStatus'=>'No','companyId'=>$company_id])->count();
                
            }else if($ReminderStatus == 'Tomorrow'){
                
                 $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ])->whereHas('ReminderData', function($q){
                   $q->where(DB::raw("DATE(reminderSet) = '".date('Y-m-d', strtotime(date("Y-m-d") .' +1 day'))."'"));
                })->where(['agentId'=>$SalesConsul,'ReasonStatus'=>'No','companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
                $count = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ])->whereHas('ReminderData', function($q) use($tommorrowDate){
                    $q->where(DB::raw("DATE(reminderSet) = '".date('Y-m-d', strtotime(date("Y-m-d") .' +1 day'))."'"));
                })->where(['agentId'=>$SalesConsul,'ReasonStatus'=>'No','companyId'=>$company_id])->count();
                
            }else if($ReminderStatus == 'Overdue'){
                
                $tommorrowDate = date('Y-m-d', strtotime(date("Y-m-d") .' +1 day'));
                
                 $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ])->whereHas('ReminderData', function($q){
                    $q->where(DB::raw('DATE(reminderSet)'),'<', date('Y-m-d'));
                })->where(['agentId'=>$SalesConsul,'ReasonStatus'=>'No','companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
                
                $count = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ])->whereHas('ReminderData', function($q) use($tommorrowDate){
                    $q->where(DB::raw('DATE(reminderSet)'),'<', date('Y-m-d'));
                })->where(['agentId'=>$SalesConsul,'ReasonStatus'=>'No','companyId'=>$company_id])->count();
                
            }else{
                
                $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->where(['agentId'=>$SalesConsul,'ReasonStatus'=>'No'])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = ListMyLead::where(['agentId'=>$SalesConsul,'ReasonStatus'=>'No','companyId'=>$company_id])->count();
                
            }
            
            
            
            // SUCCESS
            if (count($response) > 0) {
            $data = $this->renderList($response);
            
            return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Leads List Found',
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
          
            $Employee = Employee::where(['id'=>$agentId,'company_id'=>$company_id])->first();
        
            $response = ListMyLead::with(['ReminderData','getleads','getActivities','getEmployeeDetails','ProjectData','getlastactivity','getallactivity','getleadstatus' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ])->where(['agentid'=>$agentId,'ReasonStatus'=>'No'])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = ListMyLead::where(['agentid'=>$agentId,'ReasonStatus'=>'No','companyId'=>$company_id])->count();
            
            // SUCCESS
            if (count($response) > 0) {
            $data = $this->renderList($response);
            
            return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Leads List Found',
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
        $getActivityType = [];
        
        foreach ($apiResponse as $key => $value) {
            $getActivityType = [];
            $getcallingdata = Zongcalling::where('phone', 'LIKE', '%'.$value->mobileNo.'%')->get();
            
            if($value->getallactivity == ""){
                 array_push($getActivityType, (object) []);
            }else{
                foreach($value->getallactivity as $itemACti){
                    
                    $ActivityTypes = ActivityTypes::where(['id'=>$itemACti->typeId])->first();
                    
                    array_push($getActivityType, (object) [
                        'id'=>$itemACti->id,
                        'activityName'=>$ActivityTypes->activityName,
                        'typeId'=>$itemACti->typeId,
                        'activityDetails'=>$itemACti->activityDetails,
                        'created_at'=>$itemACti->created_at,
                    ]);
                    // $getActivityType[]=array(
                        
                    //     'id'=>$itemACti->id,
                    //     'typeId'=>$itemACti->typeId,
                    //     'activityDetails'=>$itemACti->activityDetails,
                    //     'created_at'=>$itemACti->created_at,
                    // );
                    
                }
            }
            $myxs="";
            $replaceWhatsappPhone="";
            $iosClientPhone="";
            if($value->getleads == null || $value->getleads == ''){
                $replaceClientPhone = null;
                $replacewhatsappClientPhone = null;
            }else{
                
                $haystack = $value->getleads->clientPhone;
                $needle   = '+92';
                
                $needlex   = '0';
                
                if (strpos($haystack, $needle) !== false) {
                  
                    $replaceWhatsappPhone = str_replace("+92","92",$value->getleads->clientPhone);
                    $iosClientPhone = str_replace("+92","92",$value->getleads->clientPhone);
                }else if (strpos($haystack, $needlex) !== false) {
                    
                    $replaceWhatsappPhone = "+920".ltrim($value->getleads->clientPhone,"92");
                    $iosClientPhone= "0".ltrim($value->getleads->clientPhone,"92");
                    
                }else{
                    $iosClientPhone= "0".ltrim($value->getleads->clientPhone,"92");
                    $replaceWhatsappPhone = str_replace("0","92",ltrim($value->getleads->clientPhone,"92"));
                }
                
                $replacePhone = str_replace("+92","0",ltrim($value->getleads->clientPhone,"92"));
                
                $replaceClientPhone = $replacePhone;
                $replacewhatsappClientPhone = $replaceWhatsappPhone;
                
                $myxs = $replaceWhatsappPhone;
            }
            
                
            array_push($data, (object) [
                "id" => $value->id,
                "myxs"=>$myxs,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "emp_name"=>$value->getEmployeeDetails == null ? '' : $value->getEmployeeDetails->emp_name,
                "leadid"=>$value->leadid,
                "title"=>$value->ProjectData == null ? '' : $value->ProjectData->title,
                "agentid"=>$value->agentid,
                "mobileNo"=>$value->mobileNo,
                "email"=>$value->email == null ? '@' : $value->email,
                "actMediumId"=>$value->actMediumId,
                // "leadstatus"=>$value->clientInterst,
                "clientName"=>$value->clientName,
                "clientPhone" => $replaceClientPhone,
                "whatsappclientPhone" => $replacewhatsappClientPhone,
                "clientPhoneIos" => $iosClientPhone,
                "getlastactivity"=>$value->getlastactivity,
                "activityDetails"=>$value->getlastactivity == null ? 'No Latest Activity Found' : $value->getlastactivity->activityDetails,
                "activitytime"=>$value->getlastactivity == null ? '-' : date("H:i:s a",strtotime($value->getlastactivity->created_at)),
                "activitydate"=>$value->getlastactivity == null ? '-' : date("Y-m-d",strtotime($value->getlastactivity->created_at)),
                "getallactivity"=>$getActivityType,
                "getleadstatus"=>$value->getleadstatus,
                "leadstatus"=>$value->getleadstatus == null ? '-' : $value->getleadstatus->PercentageTitle,
                "leadbasestatus"=>$value->leadid == 0 ? 'Self Leads' : 'Redbox Leads',
                "getcallingdata"=>$getcallingdata,
                // "getActivityType"=>$getActivityType,
                "ReminderData"=>$value->ReminderData,
                "priority"=>$value->ReminderData == null || $value->ReminderData == '' ? 'N/a' : $value->ReminderData->priority,
                
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