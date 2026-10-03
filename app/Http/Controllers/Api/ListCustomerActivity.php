<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ListAssignLeads;
use App\Models\LeadImpression;
use App\Models\CustomerActivity;
use Illuminate\Http\Request;

class ListCustomerActivity extends Controller
{
    public function ActivityList(Request $request){
        
        $orderbY = $request->orderby ?? "id";
        $ordertype = $request->ordertype ?? "desc";
        $PerPage = $request->perpage ?? "10";
        $page = $request->page ?? "1";
        $agentid = $request->agentid;
        $customerId = $request->customerId;
        $teamObj = $request->teamObj ?? "";

        
        if(is_null($agentid) && empty($teamObj)){
            
            if($customerId == '' || $customerId == null || $customerId == 'undefined'){
                $listAssign = CustomerActivity::with(['CustomerData','customer_activity_type','customer_activity_outcome','getEmployeeDetails' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->orderBy($orderbY , $ordertype)->paginate($PerPage);
                
                $listAssignTotalPages = CustomerActivity::orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
                
                 $listAssignCount = CustomerActivity::orderBy($orderbY , $ordertype)->count();
            }else{
                $listAssign = CustomerActivity::with(['CustomerData','customer_activity_type','customer_activity_outcome','getEmployeeDetails' => function ($query){
                    $query->orderBy('id', 'desc')->get();
                } ,])->where(['customerId'=>$customerId])->orderBy($orderbY , $ordertype)->paginate($PerPage);
                
                $listAssignTotalPages = CustomerActivity::where(['customerId'=>$customerId])->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
                
                 $listAssignCount = CustomerActivity::where(['customerId'=>$customerId])->orderBy($orderbY , $ordertype)->count();
            }
            
            
            
        }else if($agentid == 'all' && empty($teamObj)){
            
            $listAssign = CustomerActivity::with(['CustomerData','customer_activity_type','customer_activity_outcome','getEmployeeDetails' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->orderBy($orderbY , $ordertype)->paginate($PerPage);
            
            $listAssignTotalPages = CustomerActivity::orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
            
             $listAssignCount = CustomerActivity::orderBy($orderbY , $ordertype)->count();
            
        }else if($agentid == 'all' && !empty($teamObj)){
            
            $myArray2 = explode(',', $teamObj);
            $myArray = str_replace(array('[',']'),'',$myArray2);
            
            $listAssign = CustomerActivity::with(['CustomerData','customer_activity_type','customer_activity_outcome','getEmployeeDetails' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->whereIn('agentid',$myArray)->orderBy($orderbY , $ordertype)->paginate($PerPage);
            
            $listAssignTotalPages = CustomerActivity::whereIn('agentid',$myArray)->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
            
             $listAssignCount = CustomerActivity::whereIn('agentid',$myArray)->orderBy($orderbY , $ordertype)->count();
            
        }else{
            
            $listAssign = CustomerActivity::with(['CustomerData','customer_activity_type','customer_activity_outcome','getEmployeeDetails' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['agentid'=>$agentid])->orderBy($orderbY , $ordertype)->paginate($PerPage);
            
            $listAssignTotalPages = CustomerActivity::where(['agentid'=>$agentid])->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
            
             $listAssignCount = CustomerActivity::where(['agentid'=>$agentid])->orderBy($orderbY , $ordertype)->count();
            
        }
        
        $countListassign = count($listAssign);
        
        
      
        if(count($listAssign) > 0){
            
            $TOtalPerPage = ceil($listAssignTotalPages/$PerPage);

            return response()->json(['code'=>'200','success'=>true,'message'=>'Record Found','data'=>$listAssign,'orderBy'=>$orderbY,'orderType'=>$ordertype,'perPage'=>$PerPage,'pageNo'=>$request->page,'totalRecords'=>$listAssignCount,'TotalPerPage'=>$TOtalPerPage]);
        }else{
            return response()->json(['code'=>'403','error'=>false,'message'=>'Record Not Found','data'=>$listAssign,'orderBy'=>'','orderType'=>'','perPage'=>'','pageNo'=>'','totalRecords'=>0]);
        }
        
    }
}
