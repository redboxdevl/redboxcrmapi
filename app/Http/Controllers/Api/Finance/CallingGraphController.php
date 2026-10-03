<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\BanklistRequest;
use App\Models\Finance\Banklist;
use App\Models\Zongcalling;
use App\Models\Issuingauthority;
use App\Models\propertyType;
use App\Models\Purposeprop;
use App\Models\City;
use App\Models\HR\Employee;
use App\Models\ListAssignLeads;
use App\Models\ListMyLead;
use App\Models\Inventory\Inventorysold;
use App\Models\ListNotInterested;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;
use DB;

class CallingGraphController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
 
        $users = DB::table('zong_calling_data')
            ->where('Source', '!=', '')
            ->groupBy(DB::raw('DATE(datetime)'))->orderBy('datetime', 'desc')
            ->get();
         $data = $this->renderList($users);
             
       return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Data Found',
                    'data' => $data,
                 
                ]);
    }

    // PREPARE LIST
    public function renderList(Object $apiResponse)
    {
        $data = [];

        foreach ($apiResponse as $key => $value) {
            
            $dateItem = date("Y-m-d",strtotime($value->datetime));
            
            $youtube = DB::table('zong_calling_data')
            ->where(['Source'=>'youtube'])->whereDate('datetime',$dateItem)->count();
            
             $socialmedia = DB::table('zong_calling_data')
            ->where(['Source'=>'social media'])->whereDate('datetime',$dateItem)->count();
            
            $sms = DB::table('zong_calling_data')
            ->where(['Source'=>'sms'])->whereDate('datetime',$dateItem)->count();
            
            $web = DB::table('zong_calling_data')
            ->where(['Source'=>'web'])->whereDate('datetime',$dateItem)->count();
            
            $reference = DB::table('zong_calling_data')
            ->where(['Source'=>'reference'])->whereDate('datetime',$dateItem)->count();
            
            $followup = DB::table('zong_calling_data')
            ->where(['Source'=>'follow-up'])->whereDate('datetime',$dateItem)->count();
            
            $personalCalls = DB::table('zong_calling_data')
            ->where(['Source'=>'personal-call'])->whereDate('datetime',$dateItem)->count();
            
            array_push($data, (object) [
                "datetime" => $value->datetime,
                "youtube"=>$youtube,
                "socialmedia"=>$socialmedia,
                "sms"=>$sms,
                "web"=>$web,
                "reference"=>$reference,
                "followup"=>$followup,
                "personalCalls"=>$personalCalls
            
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