<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProjectAdd;
use Illuminate\Http\Request;
use Validator;

class SalesInventories extends Controller
{
    public function listInventoriesData(Request $request){
        
        
        
        
        $orderbY = $request->orderby;
        $orderType = $request->ordertype;
        $perPage = $request->perpage;
        $url = $request->seourl;

        if(is_null($orderbY)){ $orderbY = "id"; }else{
            $orderbY = $orderbY;
        }

        if(is_null($orderType)){ $ordertype = 'desc'; }else{
            $ordertype = $orderType;
        }
        
        if(is_null($perPage)){
            
            $listAssignCount = ProjectAdd::where(['status'=>'active'])->orderBy($orderbY , $ordertype)->count();
            
            $PerPage = $listAssignCount;
            $totalRec = $listAssignCount;
            
        }else{
     
            if($perPage){ $PerPage = $perPage; }else{
                $PerPage = 5;
            }
        
        }
        
        if(is_null($url)){
            
            $listAssign = ProjectAdd::where(['status'=>'active'])->orderBy($orderbY , $ordertype)->paginate($PerPage);
            
            $listAssignTotalPages = ProjectAdd::where(['status'=>'active'])->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
            
             $listAssignCount = ProjectAdd::where(['status'=>'active'])->orderBy($orderbY , $ordertype)->count();
            
        }else{
            
            $listAssign = ProjectAdd::where(['seourl'=>$url,'status'=>'active'])->orderBy($orderbY , $ordertype)->paginate($PerPage);
            
            $listAssignTotalPages = ProjectAdd::where(['seourl'=>$url,'status'=>'active'])->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
            
             $listAssignCount = ProjectAdd::where(['seourl'=>$url,'status'=>'active'])->orderBy($orderbY , $ordertype)->count();
            
        }
        
        $countListassign = count($listAssign);
        
        $TOtalPerPage = ceil($listAssignTotalPages/$PerPage);

      
        if(count($listAssign) > 0){
            return response()->json(['code'=>'200','success'=>true,'message'=>'Record Found','data'=>$listAssign,'orderBy'=>$orderbY,'orderType'=>$ordertype,'perPage'=>$PerPage,'pageNo'=>$request->page,'totalRecords'=>$listAssignCount,'TotalPerPage'=>$TOtalPerPage]);
        }else{
            return response()->json(['code'=>'403','error'=>false,'message'=>'Record Not Found','data'=>$listAssign,'orderBy'=>'','orderType'=>'','perPage'=>'','pageNo'=>'','totalRecords'=>0]);
        }
        
        // $url = $req->get('seourl');
        // if(is_null($url)){
        //     $proData = ProjectAdd::where(['status'=>'active'])->get();
        // }else{
        //     $proData = ProjectAdd::where(['status'=>'active','seourl'=>$url])->get();
        // }

       
        // if(count($proData) > 0){
        //     return response()->json(['code'=>'200','success'=>true,'message'=>'Data Found','data'=>$proData]);
        // }else{
        //     return response()->json(['code'=>'403','error'=>false,'message'=>'Data Not Found']);
        // }
        
    }
}
