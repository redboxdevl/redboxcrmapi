<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Leaveapplication;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\HR\LeaveApplicationRequest;
use App\Models\HR\Leavebalance;
use App\Models\HR\Employee;

use Illuminate\Http\Request;
use Validator;

class LeaveApplicationController extends Controller
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

        if ($agentId == 'all') {

            $response = Leaveapplication::with(['getleavetype' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Leaveapplication::count();
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
        }else if (!is_null($agentId)) {

            $response = Leaveapplication::with(['getleavetype' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->orderBy($orderBy, $orderType)->where(['emp_id'=>$agentId])->paginate($perPage);
            $count = Leaveapplication::count();
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
            
            $empData = Employee::where(['id'=>$value->emp_id])->first();

            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "leave_type_id" => $value->leave_type_id,
                'leave_type_name' => $value->getleavetype == '' ? '' : $value->getleavetype->leave_type_name,
                "from_date" => $value->from_date,
                "to_date" => $value->to_date,
                "applied_date" => $value->applied_date,
                "applied_time" => $value->applied_time,
                "description" => $value->description,
                "status" => $value->status,
                "approved_by_id" => $value->approved_by_id,
                "no_of_days" => $value->no_of_days,
                "emp_name" => $empData->emp_name,
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
    public function store(LeaveApplicationRequest $request)
    {
        
        
        $BalanceData=Leavebalance::where(['emp_id'=>$request->post('emp_id'),'leave_type_id'=>$request->post('leave_type_id')])->first();
        
        $balanceTotal = $BalanceData['leave_balance'] - $request->post('no_of_days');
        
       
        
        $response = Leaveapplication::create($request->all());
        
     
        
         Leavebalance::where(['emp_id'=>$request->post('emp_id'),'leave_type_id'=>$request->post('leave_type_id')])
            ->update([
            'leave_balance' => $balanceTotal,
            ]);
        
        //employeeId
        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $response,
            'code' => '200',
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Leaveapplication  $record
     * @return \Illuminate\Http\Response
     */
    public function show(Leaveapplication $record , $id)
    {
        $response = Leaveapplication::findOrFail($id);

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
     * @param  \App\Models\Leaveapplication  $record
     * @return \Illuminate\Http\Response
     */
    public function update(LeaveApplicationRequest $request, Leaveapplication $leaveapplication)
    {
        $leaveapplication->update($request->all());

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $leaveapplication,
            'code' => '200',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Leaveapplication  $leaveapplication
     * @return \Illuminate\Http\Response
     */
    public function destroy(LeaveApplicationRequest $request, Leaveapplication $leaveapplication)
    {
        $leaveapplication->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code' => '200',
        ], 200);
    }
}
