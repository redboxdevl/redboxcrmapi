<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\HR\BranchRequest;
use App\Models\ListMyLead;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AssignLeadsExport;

class AssignLeadExportController extends Controller
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

        $agentId = $request->agentId;

        if (!is_null($agentId)) {

            $response = ListMyLead::orderBy($orderBy, $orderType)->paginate($perPage);
            $count = ListMyLead::count();

            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Assign Leads Found',
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
    public function store(BranchRequest $request)
    {
 

        return response()->json([
            'message' => "Data saved successfully!",
            'data' => '',
            'code'=>'200'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function show(Branch $branch)
    {


        return response()->json([
            'message' => "Data retrieved successfully!",
            'data' => '',
            'code'=>'200'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function update(BranchRequest $request, Branch $branch)
    {
       

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => '',
            'code'=>'200'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function destroy(BranchRequest $request, Branch $branch)
    {

        return response()->json([
            'message' => "Data deleted successfully!",
            'code'=>'200'
        ], 200);
    }
    
    public function ListAssignLeadsExport (Request $request){

        ini_set('memory_limit','-1');
           
        $agentId = $request->agentId;
       
        
        $data = Excel::store(new AssignLeadsExport($agentId), 'users.xlsx');
        
        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $data,
            'code'=>'200'
        ], 200);
    }
}