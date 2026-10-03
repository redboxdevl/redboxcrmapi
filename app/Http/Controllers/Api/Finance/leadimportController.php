<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\BanklistRequest;
use App\Models\LeadImportDatabase;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ImportLeadExport;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;


use Validator;
use Mail;
use DB;

class leadimportController extends Controller
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
        $perPage = $request->perPage ?? "20";
        $page = $request->page ?? "1";
        $q = $request->q ?? "";
        $agentId = $request->agentId;
        $ProjectN = $request->ProjectN ?? "";
        $StartDates = $request->StartDates ?? "";
        $EndDate = $request->EndDate ?? "";

        if (is_null($agentId)) {
            
            if(empty($q) && empty($ProjectN) && empty($StartDates) && empty($EndDate)){
                 $response = LeadImportDatabase::orderBy($orderBy, $orderType)->paginate($perPage);
                 $count = LeadImportDatabase::orderBy($orderBy, $orderType)->count();
            }else if(!empty($q) && empty($ProjectN) && empty($StartDates) && empty($EndDate)){
                $response = LeadImportDatabase::where('clientPhone', 'like', '' . $q . '%')->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = LeadImportDatabase::where('clientPhone', 'like', '' . $q . '%')->orderBy($orderBy, $orderType)->count();
            }else if(empty($q) && !empty($ProjectN) && empty($StartDates) && empty($EndDate)){
                $response = LeadImportDatabase::where(['clientProject'=>$ProjectN])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = LeadImportDatabase::where(['clientProject'=>$ProjectN])->orderBy($orderBy, $orderType)->count();
            }else if(empty($q) && empty($ProjectN) && !empty($StartDates) && !empty($EndDate)){
                $response = LeadImportDatabase::whereBetween(DB::raw('DATE(created_at)'), array($StartDates, $EndDate))->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = LeadImportDatabase::whereBetween(DB::raw('DATE(created_at)'), array($StartDates, $EndDate))->orderBy($orderBy, $orderType)->count();
            }else if(!empty($q) && !empty($ProjectN)){
                $response = LeadImportDatabase::where('clientPhone', 'like', '' . $q . '%')->where(['clientProject'=>$ProjectN])->orderBy($orderBy, $orderType)->paginate($perPage);
                $count = LeadImportDatabase::where('clientPhone', 'like', '' . $q . '%')->where(['clientProject'=>$ProjectN])->orderBy($orderBy, $orderType)->count();
            }else{
                
            }
            
          
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Import Leads List Found',
                    'data' => $data,
                    'orderBy' => $orderBy,
                    'orderType' => $orderType,
                    'perPage' => $perPage,
                    'pageNo' => $page,
                    'TotalCount'=>$count,
                    'TotalCountData'=>number_format($count),
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
           
            
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "clientName" => $value->clientName,
                "clientPhone" => $value->clientPhone,
                "clientemail" => $value->clientemail,
                "clientProject" => $value->clientProject,
                "clientDesignation" => $value->clientDesignation,
                "nature" => $value->nature,
                "leadStatus" => $value->leadStatus,
                "leadType" => $value->leadType,
                "created_at" => date("Y-m-d H:i:s",strtotime($value->created_at)),
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
        
        // $response = Banklist::create([
        //     'title' =>  $request->title,
        //     'branch' =>  $request->branchtitle,
        //     'accountno' =>  $request->accountno,
        //     'iban' =>  $request->iban,
        //     'bankname' =>  $request->bankname,
        //     'branchcode' =>  $request->branchcode,
        //     'City' =>  $request->City,
        //     'SwiftCode' =>  $request->SwiftCode,
        //     'NiftCode' =>  $request->NiftCode,
        //     'bankimage' =>  $FileData,
        // ]);
        
        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $request->all(),
            'code'=>'200'
        ], 200);
        
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(LeadImportDatabase $leadimportdatabase)
    {
        $response = LeadImportDatabase::where(['id'=>$leadimportdatabase->id])->get();
            
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
    
    public function getAllLeadExport(Request $request){
        
        ini_set('memory_limit','-1');
        
        $search = $request->search ?? "";
        $fdate = $request->fdate ?? "";
        $tdate = $request->tdate ?? "";
        $project = $request->project ?? "";
        
        return Excel::download(new ImportLeadExport($search,$fdate,$tdate,$project), 'exportLeads.xlsx');
        
    }
    
    
    
}