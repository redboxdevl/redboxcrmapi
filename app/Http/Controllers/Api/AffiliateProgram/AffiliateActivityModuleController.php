<?php

namespace App\Http\Controllers\Api\AffiliateProgram;

use App\Http\Controllers\Controller;
use App\Models\AffiliateProgram\AffiliateActivityModule;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\AffiliateProgram\AffiliateActivityRequest;
use Illuminate\Http\Request;

class AffiliateActivityModuleController extends Controller
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

            $response = AffiliateActivityModule::where(['affiliate_id'=>$affiliateId])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = AffiliateActivityModule::count();

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

            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
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
    public function store(AffiliateActivityRequest $request)
    {
        $response = AffiliateActivityModule::create($request->all());

        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $response,
            'code'=>'200'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AffiliateProgram\AffiliateActivityModule  $affiliateActivityModule
     * @return \Illuminate\Http\Response
     */
    public function show($id='')
    {
        $response = AffiliateActivityModule::where('affiliate_lead_id',$id)->get();

        return response()->json([
            'message' => "Data retrieved successfully!",
            'data' => $response,
            'code'=>'200'
        ], 200);
        return $id;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AffiliateProgram\AffiliateActivityModule  $affiliateActivityModule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AffiliateActivityModule $affiliateActivityModule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AffiliateProgram\AffiliateActivityModule  $affiliateActivityModule
     * @return \Illuminate\Http\Response
     */
    public function destroy(AffiliateActivityModule $affiliateActivityModule)
    {
        //
    }
}
