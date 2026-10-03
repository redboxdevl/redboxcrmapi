<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\PostDatedChequeRequest;
use App\Models\Finance\Postdatedcheque;
use App\Models\Finance\BankListData;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;
use DB;

class PostDatedChequeController extends Controller
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
        
        $agentId = $request->agentId;
        
        

        if (is_null($agentId) && $agentId == '') {

                $response = Postdatedcheque::orderBy($orderBy, $orderType)->paginate($perPage);
            
             $count = Postdatedcheque::count();
          
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Post Dated Cheque Found',
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
            
            
            $response = Postdatedcheque::orderBy($orderBy, $orderType)->paginate($perPage);
             $count = Postdatedcheque::count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Post Dated Cheque Found',
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
            
            
            $BankListData = BankListData::where(['id'=>$value->BankName])->first();
                
                
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "chequeDate" => $value->chequeDate,
                "RecievedDate" => $value->RecievedDate,
                "Amount" => $value->Amount,
                "AmountInWords" => $value->AmountInWords,
                "BankName" => $BankListData == '' || $BankListData == null ? '' : $BankListData->banktitle,
                "BranchName" => $value->BranchName,
                "ChequeNo" => $value->ChequeNo,
                "RecievedFrom" => $value->RecievedFrom,
                "Purpose" => $value->Purpose,
                "Status" => $value->Status,
                "PayeeType" => $value->PayeeType,
                "BeneficiaryTitle" => $value->BeneficiaryTitle,
                "AttachementImage"=>$value->AttachementImage,
                "DateOfDeposit"=>$value->DateOfDeposit,
                "AcDeposit"=>$value->AcDeposit,
                "DateOfReleased"=>$value->DateOfReleased,
                "ReleasedTitle"=>$value->ReleasedTitle,
                "DateOfReturned"=>$value->DateOfReturned,
                "ReturnTitle"=>$value->ReturnTitle,
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
    public function store(PostDatedChequeRequest $request)
    {
        
        
        if($request->hasFile("Attachement")){
            $image = $request->file("Attachement");
            $ext = $image->extension();
            $image_name=time().'.'.$ext;
            $image->storeAs('/public/postDatedCheque/',$image_name);
             $FileData = "public/storage/postDatedCheque/".$image_name;
            
        }else{
            $FileData="";
        }
        
        $response = Postdatedcheque::create([
            'chequeDate' =>  $request->chequeDate,
            'RecievedDate' =>  $request->RecievedDate,
            'Amount' =>  $request->Amount,
            'AmountInWords' =>  $request->AmountInWords,
            'BankName' =>  $request->BankName,
            'BranchName' =>  $request->BranchName,
            'ChequeNo' =>  $request->ChequeNo,
            'RecievedFrom' =>  $request->RecievedFrom,
            'Purpose' =>  $request->Purpose,
            'Status' =>  $request->Status,
            'PayeeType' =>  $request->PayeeType,
            'BeneficiaryTitle' =>  $request->BeneficiaryTitle,
            'AttachementImage' =>  $FileData,
            'DateOfDeposit' =>  $request->DateOfDeposit,
            'AcDeposit' =>  $request->AcDeposit,
            'DateOfReleased' =>  $request->DateOfReleased,
            'ReleasedTitle' =>  $request->ReleasedTitle,
            'DateOfReturned' =>  $request->DateOfReturned,
            'ReturnTitle' =>  $request->ReturnTitle,
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
    public function show($id="")
    {
        $response = Postdatedcheque::where(['id'=>$id])->get();
            
        if($response){
            return response()->json([
                'message' => "Data retrieved successfully!",
                'data' => $response,
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
    public function update(PostDatedChequeRequest $request,$id="")
    {
        
        $checkFiles = Postdatedcheque::findOrFail($id);
        
        if($request->hasFile("Attachement")){
            $image = $request->file("Attachement");
            $ext = $image->extension();
            $image_name=time().'.'.$ext;
            $image->storeAs('/public/postDatedCheque/',$image_name);
            $image_name = "public/storage/postDatedCheque/".$image_name;
        }else{
            $image_name=$checkFiles->Attachement;
        }

        Postdatedcheque::where(['id'=>$id])->update([
            'chequeDate' =>  $request->chequeDate,
            'RecievedDate' =>  $request->RecievedDate,
            'Amount' =>  $request->Amount,
            'AmountInWords' =>  $request->AmountInWords,
            'BankName' =>  $request->BankName,
            'BranchName' =>  $request->BranchName,
            'ChequeNo' =>  $request->ChequeNo,
            'RecievedFrom' =>  $request->RecievedFrom,
            'Purpose' =>  $request->Purpose,
            'Status' =>  $request->Status,
            'PayeeType' =>  $request->PayeeType,
            'BeneficiaryTitle' =>  $request->BeneficiaryTitle,
            'AttachementImage' =>  $image_name,
        ]);
        
        
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
    public function destroy(PostDatedChequeRequest $request, Postdatedcheque $postdatedcheque)
    {
        $postdatedcheque->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code'=>'200'
        ], 200);
    }
    
    public function BankListData(Request $request){
        
        $BankListData = BankListData::all();
          return response()->json([
            'message' => "Bank List Data",
            'data' => $BankListData,
            'code'=>'200'
        ], 200);
         
    }
    
    
        public function PostDatedChequeFilters (Request $request){
        
        $agentId = $request->agentId;
        
        $RecievedDateFrom = $request->RecievedDateFrom ?? "";
        $RecievedDateTo = $request->RecievedDateTo ?? "";
        
        $ChequeDateFrom = $request->ChequeDateFrom ?? "";
        $ChequeeDateTo = $request->BankNameTo ?? "";
        
        $BankNameFrom = $request->BankNameFrom ?? "";
        $BankNameTo = $request->AccountNoTo ?? "";
        
        $PayeeTypeFrom = $request->PayeeTypeFrom ?? "";
        $PayeeTypeTo = $request->PayeeTypeTo ?? "";
        
        $StatusFrom = $request->StatusFrom ?? "";
        $StatusTo = $request->StatusTo ?? "";
        
        
        if (is_null($agentId) && $agentId == '') {

           
            if($RecievedDateFrom != '' && $RecievedDateTo !=''){
                
                $response = Postdatedcheque::whereBetween('RecievedDate', array($RecievedDateFrom,$RecievedDateTo))->get();
                $count = Postdatedcheque::whereBetween('RecievedDate', array($RecievedDateFrom,$RecievedDateTo))->count();
            
            }else if($ChequeDateFrom != '' && $ChequeeDateTo !=''){
                
                $response = Postdatedcheque::whereBetween('chequeDate', array($ChequeDateFrom,$ChequeeDateTo))->get();
                $count = Postdatedcheque::whereBetween('chequeDate', array($ChequeDateFrom,$ChequeeDateTo))->count();
            
            }else if($BankNameFrom != '' && $BankNameTo !=''){
                
                $response = Postdatedcheque::whereBetween('BankName', array($BankNameFrom,$BankNameTo))->get();
                $count = Postdatedcheque::whereBetween('BankName', array($BankNameFrom,$BankNameTo))->count();
            
            }else if($PayeeTypeFrom != '' && $PayeeTypeTo !=''){
                
                $response = Postdatedcheque::whereBetween('PayeeType', array($PayeeTypeFrom,$PayeeTypeTo))->get();
                $count = Postdatedcheque::whereBetween('PayeeType', array($PayeeTypeFrom,$PayeeTypeTo))->count();
            
            }else if($StatusFrom != '' && $StatusTo !=''){
                
                $response = Postdatedcheque::whereBetween('Status', array($StatusFrom,$StatusTo))->get();
                $count = Postdatedcheque::whereBetween('Status', array($StatusFrom,$StatusTo))->count();
            
            }else{
               
                $response = Postdatedcheque::all();
                $count = Postdatedcheque::count();
            }
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Cheque Outward Filters Found',
                    'data' => $data,
                    'orderBy' => '',
                    'orderType' => '',
                    'perPage' => '',
                    'pageNo' => '',
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
    
    
}