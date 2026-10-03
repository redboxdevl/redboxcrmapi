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

class billdueSummaryFlowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $company_id = $request->company_id ?? "" ;
        $getOutwards = Finbills::with(['getCompany' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['company_id'=>$company_id])->where('companyId','!=','0')->groupBy('companyId')->get();
        
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
        
        $datexs = Carbon::parse(date("Y-m-d"));
        $nextMonth = $datexs->addMonth()->format('m');
        

        foreach ($apiResponse as $key => $value) {
            
             $billamountSumData = [];
             $paidamountSumData = [];
             
            $TodaybillamountSumData = [];
            $TodayPaidamountSumData = [];
            
            $PastbillamountSumData = [];
            $PastPaidamountSumData = [];
            
            $Last3DaysbillamountSumData = [];
            $Last3DaysPaidamountSumData = [];
            
            $Last7DaysbillamountSumData = [];
            $Last7DaysPaidamountSumData = [];
            
            $Last15DaysbillamountSumData = [];
            $Last15DaysPaidamountSumData = [];
            
             $Last30DaysbillamountSumData = [];
            $Last30DaysPaidamountSumData = [];
            
             $Last90DaysbillamountSumData = [];
            $Last90DaysPaidamountSumData = [];
            
             $Last60DaysbillamountSumData = [];
            $Last60DaysPaidamountSumData = [];
            
            
            $BillamountSum = Finbills::where(['companyId'=>$value->companyId])->get()->toArray();
            
            foreach($BillamountSum as $sumData){
                $billamountSumData[]=str_replace(",","",$sumData['BillAmount']);
                $paidamountSumData[]=str_replace(",","",$sumData['PaidAmount']);
            }
            
            
            $BillamountTodaySum = Finbills::where(['companyId'=>$value->companyId])->where(DB::raw('DATE(dueDate)'), date('Y-m-d'))->get()->toArray();
            foreach($BillamountTodaySum as $sumTodayData){
                $TodaybillamountSumData[]=str_replace(",","",$sumTodayData['BillAmount']);
                $TodayPaidamountSumData[]=str_replace(",","",$sumTodayData['PaidAmount']);
            }
            
            $BillamountPastSum = Finbills::where(['companyId'=>$value->companyId])->where('dueDate','<',date('Y-m-d'))->get()->toArray();
            foreach($BillamountPastSum as $sumPastData){
                $PastbillamountSumData[]=str_replace(",","",$sumPastData['BillAmount']);
                $PastPaidamountSumData[]=str_replace(",","",$sumPastData['PaidAmount']);
            }
            
            $BillamountLast3DaysSum = Finbills::where(['companyId'=>$value->companyId])->whereBetween('dueDate', [date('Y-m-d'),Carbon::now()->addDays(3)])->get()->toArray();
            
            foreach($BillamountLast3DaysSum as $sumLast3DaysData){
                
             
                $Last3DaysbillamountSumData[]=str_replace(",","",$sumLast3DaysData['BillAmount']);
                $Last3DaysPaidamountSumData[]=str_replace(",","",$sumLast3DaysData['PaidAmount']);
            }
            
            
            $BillamountLast7DaysSum = Finbills::where(['companyId'=>$value->companyId])->whereBetween('dueDate',[Carbon::now(),Carbon::now()->addDays(7)])->get()->toArray();
            foreach($BillamountLast7DaysSum as $sumLast7DaysData){
                $Last7DaysbillamountSumData[]=str_replace(",","",$sumLast7DaysData['BillAmount']);
                $Last7DaysPaidamountSumData[]=str_replace(",","",$sumLast7DaysData['PaidAmount']);
            }
        
            $BillamountLast15DaysSum = Finbills::where(['companyId'=>$value->companyId])->whereBetween('dueDate',[Carbon::now(),Carbon::now()->addDays(15)])->get()->toArray();
            foreach($BillamountLast15DaysSum as $sumLast15DaysData){
                $Last15DaysbillamountSumData[]=str_replace(",","",$sumLast15DaysData['BillAmount']);
                $Last15DaysPaidamountSumData[]=str_replace(",","",$sumLast15DaysData['PaidAmount']);
            }
            
            $BillamountLast30DaysSum = Finbills::where(['companyId'=>$value->companyId])->whereBetween('dueDate',[Carbon::now(),Carbon::now()->addDays(30)])->get()->toArray();
            foreach($BillamountLast30DaysSum as $sumLast30DaysData){
                $Last30DaysbillamountSumData[]=str_replace(",","",$sumLast30DaysData['BillAmount']);
                $Last30DaysPaidamountSumData[]=str_replace(",","",$sumLast30DaysData['PaidAmount']);
            }
            
            $BillamountLast90DaysSum = Finbills::where(['companyId'=>$value->companyId])->whereBetween('dueDate',[Carbon::now(),Carbon::now()->addDays(90)])->get()->toArray();
            foreach($BillamountLast90DaysSum as $sumLast90DaysData){
                $Last90DaysbillamountSumData[]=str_replace(",","",$sumLast90DaysData['BillAmount']);
                $Last90DaysPaidamountSumData[]=str_replace(",","",$sumLast90DaysData['PaidAmount']);
            }
            
            $BillamountLast60DaysSum = Finbills::where(['companyId'=>$value->companyId])->whereBetween('dueDate',[Carbon::now(),Carbon::now()->addDays(60)])->get()->toArray();
            foreach($BillamountLast60DaysSum as $sumLast60DaysData){
                $Last60DaysbillamountSumData[]=str_replace(",","",$sumLast60DaysData['BillAmount']);
                $Last60DaysPaidamountSumData[]=str_replace(",","",$sumLast60DaysData['PaidAmount']);
            }

            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "CompanyId"=>$value->CompanyId,
                "getCompany"=>$value->getCompany == null ? '' : $value->getCompany->company_name,
                'Todaybalanaceamount'=>number_format(array_sum($TodaybillamountSumData) - array_sum($TodayPaidamountSumData)),
                'Pastbalanaceamount'=>number_format(array_sum($PastbillamountSumData) - array_sum($PastPaidamountSumData)),
                'Next3daysbalanaceamount'=>number_format(array_sum($Last3DaysbillamountSumData)),
                'Next7daysbalanaceamount'=>number_format(array_sum($Last7DaysbillamountSumData) - array_sum($Last7DaysPaidamountSumData)),
                'Next15daysbalanaceamount'=>number_format(array_sum($Last15DaysbillamountSumData) - array_sum($Last15DaysPaidamountSumData)),
                'Next30daysbalanaceamount'=>number_format(array_sum($Last30DaysbillamountSumData) - array_sum($Last30DaysPaidamountSumData)),
                'Next90daysbalanaceamount'=>number_format(array_sum($Last90DaysbillamountSumData) - array_sum($Last90DaysPaidamountSumData)),
                'Next60daysbalanaceamount'=>number_format(array_sum($Last60DaysbillamountSumData) - array_sum($Last60DaysPaidamountSumData)),
               
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