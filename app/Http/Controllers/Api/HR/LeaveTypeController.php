<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Leavetype;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\HR\LeaveTypeRequest;
use Illuminate\Http\Request;

use Validator;

class LeaveTypeController extends Controller
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

            $response = Leavetype::orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Leavetype::count();
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Record Found',
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
                "leave_type_name" => $value->leave_type_name,
                "num_of_days" => $value->num_of_days,
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
    public function store(LeaveTypeRequest $request)
    {
        $response = Leavetype::create($request->all());

        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $response,
            'code' => '200',
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Leavetype  $leavetype
     * @return \Illuminate\Http\Response
     */
    public function show(Leavetype $leavetype)
    {
        $response = Leavetype::findOrFail($leavetype->id);

        return response()->json([
            'message' => "Data retrieved successfully!",
            'data' => $response,
            'code' => '200',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Leavetype  $leavetype
     * @return \Illuminate\Http\Response
     */
    public function update(LeaveTypeRequest $request, Leavetype $leavetype)
    {
        $leavetype->update($request->all());

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $leavetype,
            'code' => '200',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Leavetype  $leavetype
     * @return \Illuminate\Http\Response
     */
    public function destroy(LeaveTypeRequest $request, Leavetype $leavetype)
    {
        $leavetype->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
        ], 200);
    }
}
