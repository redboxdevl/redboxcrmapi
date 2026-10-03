<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Models\Finance\Checkoutwardregister;
use App\Models\Finance\AssetRegister;
use App\Models\Finance\Outwardvoucher;
use App\Models\Finance\Bankdeposit;
use App\Models\Finance\AssetLife;

use App\Models\Finance\Billstype;
use App\Models\Finance\Finbills;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;
use Validator;
use Mail;
use DateTime;
use DB;

class BillsummaryflowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $company_id = $request->company_id ?? "" ;
        $getOutwards = Finbills::with(['getBilltype' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['company_id'=>$company_id])->where('billTypeId','!=','0')->groupBy('billTypeId')->get();
        
        $data = $this->renderList($getOutwards);

        return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Fin Bills Wise Dashboard List Found',
            'data' => $data,
        ]);
          
    }

    // PREPARE LIST
    public function renderList(Object $apiResponse)
    {
        $data = [];
        

        foreach ($apiResponse as $key => $value) {
            
             $billamountSumData = [];
             $paidamountSumData = [];
            
            
            $BillamountSum = Finbills::where(['billTypeId'=>$value->billTypeId])->get()->toArray();
            
            foreach($BillamountSum as $sumData){
                $billamountSumData[]=str_replace(",","",$sumData['BillAmount']);
                $paidamountSumData[]=str_replace(",","",$sumData['PaidAmount']);
            }

            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "CompanyId"=>$value->CompanyId,
                "getBilltype"=>$value->getBilltype == null ? '' : $value->getBilltype->typeTitle,
                'billamount'=>number_format(array_sum($billamountSumData)),
                'paidamount'=>number_format(array_sum($paidamountSumData)),
                'balanaceamount'=>number_format(array_sum($billamountSumData) - array_sum($paidamountSumData)),
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
        
        if($request->hasFile("Attachement")){
            $image = $request->file("Attachement");
            $ext = $image->extension();
            $image_name=time().'.'.$ext;
            $image->storeAs('/public/bankLogo/',$image_name);
            $FileData = "public/storage/bankLogo/".$image_name;
            
        }else{
            $FileData="";
        }
        
        
        $response = Banklist::create([
            'title' =>  $request->title,
            'ShortTitle' => $request->ShortTitle,
            'branch' =>  $request->branchtitle,
            'accountno' =>  $request->accountno,
            'iban' =>  $request->iban,
            'bankname' =>  $request->bankname,
            'branchcode' =>  $request->branchcode,
            'City' =>  $request->City,
            'SwiftCode' =>  $request->SwiftCode,
            'NiftCode' =>  $request->NiftCode,
            'bankimage' =>  $FileData,
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
    public function show(Banklist $banklist)
    {
        $response = Banklist::where(['id'=>$banklist->id])->get();
            
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
    public function update(BanklistRequest $request, Banklist $banklist,$id="")
    {

        $checkFiles = Banklist::findOrFail($banklist->id);
        
        if($request->hasFile("Attachement")){
            $image = $request->file("Attachement");
            $ext = $image->extension();
            $image_name=time().'.'.$ext;
            $image->storeAs('/public/bankLogo/',$image_name);
            $image_names = "public/storage/bankLogo/".$image_name;
        }else{
            $image_names=$checkFiles->bankimage;
        }

        $banklist->update([
            'title' =>  $request->title,
            'ShortTitle' => $request->ShortTitle,
            'branch' =>  $request->branchtitle,
            'accountno' =>  $request->accountno,
            'iban' =>  $request->iban,
            'bankname' =>  $request->bankname,
            'branchcode' =>  $request->branchcode,
            'bankimage' =>  $image_names,
            'City' =>  $request->City,
            'SwiftCode' =>  $request->SwiftCode,
            'NiftCode' =>  $request->NiftCode,
        ]);
        
        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $checkFiles,
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
        $banklist->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code'=>'200'
        ], 200);
    }
    
    
    
    
}