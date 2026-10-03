<?php

namespace App\Http\Controllers\Api\AffiliateProgram;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\AffiliateProgram\AffiliateLeadsRequest;
use App\Models\AffiliateProgram\AffiliateLeadsModule;
use App\Models\HR\Employee;
use App\Models\AffiliateProgram\AffiliateRegisterModule;

use Illuminate\Http\Request;

class AffiliateLeadsModuleController extends Controller
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

        $affiliateId = $request->affiliateId;

        if (!is_null($affiliateId)) {

            $response = AffiliateLeadsModule::with(['getActivityMedium' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['affiliate_id'=>$affiliateId])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = AffiliateLeadsModule::count();

            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Affiliate Leads Found',
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
            return app(CommonController::class)->getExceptionResponse("Affiliate Id is required");
        }
    }


    public function renderList(Object $apiResponse)
    {
        $data = [];

        foreach ($apiResponse as $key => $value) {
            
            $agentEmp = AffiliateRegisterModule::where(['id'=>$value->affiliate_id])->first();
            
            // $agentEmp = Employee::where(['id'=>$value->emp_id])->first();
            
            if($agentEmp == null || $agentEmp == '' || $agentEmp->affilateStatus == '' || $agentEmp->affilateStatus == null || $agentEmp->affilateStatus == 'affiliates'){
                $agentEmpAffiliate = Employee::where(['id'=>$agentEmp->emp_id])->first();
                // $agentEmpAffiliate = AffiliateRegisterModule::where(['id'=>$agentEmp->id])->first();
                $empData=array(
                    'full_name' => $agentEmpAffiliate == null ? '' : $agentEmpAffiliate->emp_name,
                    'emp_id' => $agentEmpAffiliate == null ? '' : $agentEmpAffiliate->id,
                    'affilateStatus' => '',
                );
            }else{
                $agentEmpOringinal = Employee::where(['id'=>$value->affiliate_id])->first();
                $empData=array(
                    'full_name' => $agentEmpOringinal->emp_name,
                    'emp_id' => $agentEmpOringinal->id,
                    'affilateStatus' => '',
                );;
            }
           
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "affiliate_id" => $value->affiliate_id,
                "countryid" => $value->countryid,
                "cityid" => $value->cityid,
                "projectid" => $value->projectid,
                "typeid" => $value->typeid,
                "natureid" => $value->natureid,
                "actMediumId" => $value->actMediumId,
                "activityName"=>$value->getActivityMedium == null || $value->getActivityMedium == '' ? '' : $value->getActivityMedium->activityName,
                "clientName" => $value->clientName,
                "clientStatus" => $value->clientStatus,
                "Gender" => $value->Gender,
                "email" => $value->email == '' ? '@' : $value->email,
                "mobileNo" => $value->mobileNo,
                "passportCnic" => $value->passportCnic,
                "whatsappNo" => $value->whatsappNo,
                "prefLanguage" => $value->prefLanguage,
                "Budget" => $value->Budget,
                "timeFrame" => $value->timeFrame,
                "RefferdBy" => $value->RefferdBy,
                "clientInterst" => $value->clientInterst,
                "clientActivityDesc" => $value->clientActivityDesc,
                "agentEmp"=>$empData,
                
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
    public function store(AffiliateLeadsRequest $request)
    {
        $response = AffiliateLeadsModule::create($request->all());

        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $response,
            'code'=>'200'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AffiliateProgram\AffiliateLeadsModule  $affiliateLeadsModule
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $response = AffiliateLeadsModule::with(['getActivityMedium' => function ($query){
        //         $query->orderBy('id', 'desc')->get();
        //     } ,])->findOrFail($id);
            
        $response = AffiliateLeadsModule::with(['getActivityMedium' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['id'=>$id])->get();
            
            if($response){
                 $data = $this->renderList($response);

                return response()->json([
                    'message' => "Data retrieved successfully!",
                    'data' => $data,
                    'code'=>'200'
                ], 200);
            }else{
                return app(CommonController::class)->getEmptyListResponse();
            }
           
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AffiliateProgram\AffiliateLeadsModule  $affiliateLeadsModule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AffiliateLeadsModule $affiliateLeadsModule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AffiliateProgram\AffiliateLeadsModule  $affiliateLeadsModule
     * @return \Illuminate\Http\Response
     */
    public function destroy(AffiliateLeadsModule $affiliateLeadsModule)
    {
        //
    }
}
