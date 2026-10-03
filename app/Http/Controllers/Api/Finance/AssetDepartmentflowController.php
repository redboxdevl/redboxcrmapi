<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Models\Finance\Checkoutwardregister;
use App\Models\Finance\AssetRegister;
use App\Models\Finance\Outwardvoucher;
use App\Models\Finance\Bankdeposit;
use App\Models\Finance\AssetLife;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;
use Validator;
use Mail;
use DateTime;
use DB;

class AssetDepartmentflowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $company_id = $request->company_id ?? "" ;
        $getOutwards = AssetRegister::with(['getDepartmentId' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['company_id'=>$company_id])->where('DepartmentId','!=','0')->groupBy('DepartmentId')->get();
        
        $data = $this->renderList($getOutwards);

        return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Asset Company Wise Dashboard List Found',
            'data' => $data,
        ]);
          
    }

    // PREPARE LIST
    public function renderList(Object $apiResponse)
    {
        $data = [];
        

        foreach ($apiResponse as $key => $value) {
        
        $bookValueSUm = [];
        $DeprecationValueSUm = [];
        $ResidueValueSUm = [];
       
        
        
        $BookValueSUmToday = AssetRegister::where(['DepartmentId'=>$value->DepartmentId])->get()->toArray();
        foreach($BookValueSUmToday as $sumToday){
            
            $AssetLife = AssetLife::where(['id'=>$sumToday['assetLifeId']])->first();
            
            $yearCount = $AssetLife == '' ? '' : $AssetLife->LifeTitle;
            $yearwiseMonthCount = $yearCount * 12;
            
            $monthData = [];
            for($y=1; $y<=$yearwiseMonthCount; $y++)
            {
            $d=date('t', mktime(0, 0, 0, $y, 1, $yearCount)); 
            $yearData = date("Y", mktime(0, 0, 0, ($y)));
            $monthNameData = date("m", mktime(0, 0, 0, ($y)));
            $monthData[]=$d;
            }
            $montSum = array_sum($monthData);
            
            $assetBookValP = (int) str_replace(',', '',$sumToday['AssetBookValue']);
            $perDayDeprecation = floor($assetBookValP / $montSum);
            
            //Total No Of Days
            
            $ExpiryDate = date('Y-m-d', strtotime($sumToday['purchasedDate']. ' + '.$montSum.' days'));
            
            $datetime1 = new DateTime($ExpiryDate);
            $datetime2 = new DateTime($sumToday['purchasedDate']);
            $interval = $datetime1->diff($datetime2);
            $days = $interval->format('%a');//now do whatever you like with $days
            
            $TotalNoOfDatsxs = $days + 1;
            
            // Days Lapsed
            $dateCurrent = date("Y-m-d");
            
            $datetime12 = new DateTime($dateCurrent);
            $datetime23 = new DateTime($sumToday['purchasedDate']);
            $intervals = $datetime12->diff($datetime23);
            $daysxs = $intervals->format('%a');//now do whatever you like with $days
            
            $totalDaysLapsed = $daysxs + 1;
            
            $depForm = $assetBookValP / $TotalNoOfDatsxs * $totalDaysLapsed;
            
            
            $bookValueSUm[]=str_replace(",","",$sumToday['AssetBookValue']);
            $DeprecationValueSUm[]=$depForm;
            // $ResidueValueSUm[]=$residueLife;
        }
        
        
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "CompanyId"=>$value->CompanyId,
                "getDepartmentId"=>$value->getDepartmentId,
                "department_name"=>$value->getDepartmentId == '' || $value->getDepartmentId == null ? '' : $value->getDepartmentId->department_name,
                // "ShortTitle"=>$value->getAccountdetails == '' || $value->getAccountdetails == null ? '' : $value->getAccountdetails->ShortTitle,
                // "branch"=>$value->getAccountdetails == '' || $value->getAccountdetails == null ? '' : $value->getAccountdetails->branch,
                // "accountno"=>$value->getAccountdetails == '' || $value->getAccountdetails == null ? '' : $value->getAccountdetails->accountno,
                // "bankname"=>$value->getAccountdetails == '' || $value->getAccountdetails == null ? '' : $value->getAccountdetails->bankname,
                // // // "getCompanies"=>$value->getCompanies,
                // // "company_name"=>$value->getCompanies == '' || $value->getCompanies == null ? '' : $value->getCompanies->company_name,
                // 'outwardcashPayDay'=>number_format(array_sum($todaySumData)),
                // 'outwardcashPayWeekly'=>number_format(array_sum($WeeklySumData)),
                // 'cashPayCurrentMonth'=>number_format(array_sum($CurrentMonthSumData)),
                // 'cashPayLastMonth'=>number_format(array_sum($LastMonthSumData)),
                'bookValueSUm'=>number_format(array_sum($bookValueSUm)),
                'DeprecationValueSUm'=>number_format(array_sum($DeprecationValueSUm)),
                'ResidueValueSUm'=>number_format(array_sum($bookValueSUm)-array_sum($DeprecationValueSUm)),
                
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