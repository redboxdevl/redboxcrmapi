<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Leavebalance;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\HR\LeaveBalanceRequest;
use Illuminate\Http\Request;
use Validator;

class LeaveBalanceController extends Controller
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
        $leaveTypeId = $request->leavetypeid;

        if (!is_null($agentId)) {
            
            if(!is_null($leaveTypeId)){
                
                $response = Leavebalance::with(['getLeaveType' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['emp_id'=>$agentId,'leave_type_id'=>$leaveTypeId])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            }else{
                
                $response = Leavebalance::with(['getLeaveType' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['emp_id'=>$agentId])->orderBy($orderBy, $orderType)->paginate($perPage);
            
                
            }

            

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
                "emp_id" => $value->emp_id,
                "leave_type_id" => $value->leave_type_id,
                "leave_type_name" => $value->getLeaveType == null ? '' : $value->getLeaveType->leave_type_name,
                "leave_balance" => $value->leave_balance,
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
    public function store(LeaveBalanceRequest $request)
    {
        $response = Leavebalance::create($request->all());

        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $response
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Leavebalance  $record
     * @return \Illuminate\Http\Response
     */
    public function show(Leavebalance $record , $id)
    {
        $response = Leavebalance::findOrFail($id);

        return response()->json([
            'message' => "Data retrieved successfully!",
            'data' => $response
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Leavebalance  $leavebalance
     * @return \Illuminate\Http\Response
     */
    public function update(LeaveBalanceRequest $request, Leavebalance $leavebalance)
    {
        $leavebalance->update($request->all());

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $leavebalance
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Leavebalance  $leavebalance
     * @return \Illuminate\Http\Response
     */
    public function destroy(LeaveBalanceRequest $request, Leavebalance $leavebalance)
    {
        $leavebalance->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
        ], 200);
    }
}
