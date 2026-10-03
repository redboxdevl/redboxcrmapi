<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\PaymentplanRequest;
use App\Models\Finance\Paymentplan;
use App\Models\Finance\Paymentplandata;

use App\Models\Inventory\Project;
use App\Models\HR\Company;
use App\Models\HR\Employee;
use App\Models\Inventory\Projectinventory;

use App\Models\AffiliateProgram\AffiliateLeadsModule;
use App\Models\Inventory\Inventorysold;



use App\Models\ListMyLead;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;

class PaymentPlanDataController extends Controller
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
        
        if (is_null($agentId)) {

            $response = Paymentplandata::orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Paymentplandata::count();
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Payment Plan Data Found',
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
                
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "accountHead" => $value->accountHead,
                "dueAmount" => $value->dueAmount,
                "dueDate" => $value->dueDate,
                
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
    public function store(PaymentplanRequest $request)
    {
        
        return response()->json([
            'message' => "Plan Generated successfully!",
            'data' => '',
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
        
        $response = Paymentplandata::where(['id'=>$id])->get();
            
        if($response){
            $data = $this->renderList($response);

            return response()->json([
                'message' => "Data retrieved successfully!",
                'data' => $data,
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
    public function update(PaymentrecieptRequest $request, Paymentreciept $paymentreciept,$id="")
    {

        // $checkFiles = Paymentreciept::findOrFail($id);
        
        // SecurityFiles::where(['id'=>$id])->update([
        //     'docTypeId' =>  $docTypeIds,
        //     'proTypeId' =>  $ProPType,
        //     'docRef' =>  $request->docRef,
        //     'docStatus' =>  $request->docStatus,
        //     'releaseName' =>  $request->releaseName,
        //     'releaseDate' =>  $request->releaseDate,
        //     'Attachement' =>  $image_name,
        //     'status' =>  $request->status,
        //     'uploadedBy' => $request->uploadedBy,
        //     'docNature' => $request->docNature,
        //     'ownerShipTitle' => $request->ownerShipTitle,
        //     'docDesc' => $request->docDesc,
        //     'issuingauthorityId'=>$IssUingPType,
        //     'CompaniesId' => $request->CompaniesId,
        //     'PurposeId' => $PurposeTypeId,
        // ]);
        
        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $request->all(),
            'code'=>'200'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentrecieptRequest $request, Paymentreciept $paymentreciept)
    {
        $paymentreciept->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code'=>'200'
        ], 200);
    }
    
    
    
    
}