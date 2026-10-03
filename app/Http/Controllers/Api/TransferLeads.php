<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TranasferShareLeads;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransferDatabaseExport;
use Validator;

class TransferLeads extends Controller
{
    public function listTransferLeads(Request $request){
       
        
        $orderbY = $request->orderby;
        $orderType = $request->ordertype;
        $perPage = $request->perpage;
        $agentid = $request->agentid;

        if(is_null($orderbY)){ $orderbY = "id"; }else{
            $orderbY = $orderbY;
        }

        if(is_null($orderType)){ $ordertype = 'desc'; }else{
            $ordertype = $orderType;
        }
        
        if(is_null($perPage)){
            
            $listAssignCount = TranasferShareLeads::orderBy($orderbY , $ordertype)->count();
            
            $PerPage = $listAssignCount;
            $totalRec = $listAssignCount;
            
        }else{
     
            if($perPage){ $PerPage = $perPage; }else{
                $PerPage = 5;
            }
        
        }
        
        if(is_null($agentid)){
           
            $listAssign = TranasferShareLeads::with(['LeadData','toAgent' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->orderBy($orderbY , $ordertype)->paginate($PerPage);
            
            $listAssignTotalPages = TranasferShareLeads::orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
            
             $listAssignCount = TranasferShareLeads::orderBy($orderbY , $ordertype)->count();
            
        }else if($agentid == 'all'){
            
            $listAssign = TranasferShareLeads::with(['LeadData','toAgent' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->orderBy($orderbY , $ordertype)->paginate($PerPage);
            
            $listAssignTotalPages = TranasferShareLeads::orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
            
             $listAssignCount = TranasferShareLeads::orderBy($orderbY , $ordertype)->count();
            
        }else{
            
            $listAssign = TranasferShareLeads::with(['LeadData','toAgent' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['fagent'=>$agentid])->orderBy($orderbY , $ordertype)->paginate($PerPage);
            
            $listAssignTotalPages = TranasferShareLeads::where(['fagent'=>$agentid])->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
            
             $listAssignCount = TranasferShareLeads::where(['fagent'=>$agentid])->orderBy($orderbY , $ordertype)->count();
            
        }
        
        $countListassign = count($listAssign);
        
       
      
        if(count($listAssign) > 0){
            
             $TOtalPerPage = ceil($listAssignTotalPages/$PerPage);


            return response()->json(['code'=>'200','success'=>true,'message'=>'Record Found','data'=>$listAssign,'orderBy'=>$orderbY,'orderType'=>$ordertype,'perPage'=>$PerPage,'pageNo'=>$request->page,'totalRecords'=>$listAssignCount,'TotalPerPage'=>$TOtalPerPage]);
        }else{
            return response()->json(['code'=>'403','error'=>false,'message'=>'Record Not Found','data'=>$listAssign,'orderBy'=>'','orderType'=>'','perPage'=>'','pageNo'=>'','totalRecords'=>0]);
        }
        
        
        
        
        
        // $agentid =$req->get('agentid');
        // $rules=array(
        //     'agentid'=>'required',
        // );

        // $validator = Validator::make($req->all(),$rules);
        // if($validator->fails()){
        //     return $validator->errors();
        // }else{
        //     $TransferData = TranasferShareLeads::where(['fagent'=>$agentid])->orderBy('id')->get();
        //     if(count($TransferData) > 0){
        //         return response()->json(['code'=>'200','success'=>true,'message'=>'Data Found','data'=>$TransferData]);
        //     }else{
        //         return response()->json(['code'=>'403','error'=>false,'message'=>'Data Not Found']);
        //     }
        // }

    }
    
    
    public function ListTransferExport(Request $request){
        
        ini_set('memory_limit','-1');
       
        
        $q = $request->q;
        $fdate = $request->fdate;
        $tdate = $request->tdate;
        
        return Excel::download(new TransferDatabaseExport($q,$fdate,$tdate), 'transfer.xlsx');
    }
    
}
