<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\HR\AttendanceRequest;
use App\Http\Controllers\Api\CommonController;
use App\Models\HR\Attendance;
use App\Models\HR\Employee;
use Illuminate\Http\Request;


class AttendanceController extends Controller
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

        if (is_null($agentId)) {

            $response = Attendance::with(['getEmployee' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = Attendance::count();

            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Attendance Record Found',
                    'data' => $data,
                    'orderBy' => $orderBy,
                    'orderType' => $orderType,
                    'perPage' => $perPage,
                    'pageNo' => $page,
                    'TotalCount'=>$count,
                ]);
            }
            
        }else if (!is_null($agentId)) {

            $response = Attendance::with(['getEmployee' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['emp_id'=>$agentId])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = Attendance::where(['emp_id'=>$agentId])->count();

            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Attendance Record Found',
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
        }else if ($agentId == 'all') {

            $response = Attendance::with(['getEmployee' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = Attendance::where(['emp_id'=>$agentId])->count();

            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Attendance Record Found',
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
                "emp_id" => $value->emp_id,
                "emp_name" => $value->getEmployee == '' ? '' : $value->getEmployee->emp_name,
                "machine_id" => $value->machine_id,
                "location_lat" => $value->location_lat,
                "location_long" => $value->location_long,
                "checkout_location_lat" => $value->checkout_location_lat,
                "checkout_location_long" => $value->checkout_location_long,
                "is_mobile" => $value->is_mobile,
                "attendance_date" => $value->attendance_date,
                "checkin" => $value->checkin,
                "checkout" => $value->checkout,
                "checkinType" => $value->checkintype,
                "checkoutType" => $value->checkouttype,
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
    public function store(AttendanceRequest $request)
    {
       $response = Attendance::create($request->all());
       
       $empDtaa = Employee::where(['id'=>$response->emp_id])->first();
       
       $data=[
         
         "id"=>$response->id,
         "created_at"=>$response->created_at,
         "updated_at"=>$response->updated_at,
         "deleted_at"=>$response->deleted_at,
         "emp_id"=>$response->emp_id,
         "emp_name"=>$empDtaa->emp_name,
         "machine_id"=>$response->machine_id,
         "location_lat"=>$response->location_lat,
         "location_long"=>$response->location_long,
         "checkout_location_lat"=>$response->checkout_location_lat,
         "checkout_location_long"=>$response->checkout_location_long,
         "is_mobile"=>$response->is_mobile,
         "attendance_date"=>$response->attendance_date,
         "checkin"=>$response->checkin,
         "checkout"=>$response->checkout,
         "checkintype"=>$response->checkintype,
         "checkouttype"=>$response->checkouttype,
         
       
       ];

        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $data,
            'code' => '200',
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HR\Attendance  $record
     * @return \Illuminate\Http\Response
     */
    public function show(Attendance $record , $id)
    {
        $response = Attendance::findOrFail($id);

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
     * @param  \App\Models\HR\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function update(AttendanceRequest $request, Attendance $attendance)
    {
        $attendance->update($request->all());
        
         $data=[
         
         "id"=>$attendance->id,
         "created_at"=>$attendance->created_at,
         "updated_at"=>$attendance->updated_at,
         "deleted_at"=>$attendance->deleted_at,
         "emp_id"=>$attendance->emp_id,
         "emp_name"=>$empDtaa->emp_name,
         "machine_id"=>$attendance->machine_id,
         "location_lat"=>$attendance->location_lat,
         "location_long"=>$attendance->location_long,
         "checkout_location_lat"=>$attendance->checkout_location_lat,
         "checkout_location_long"=>$attendance->checkout_location_long,
         "is_mobile"=>$attendance->is_mobile,
         "attendance_date"=>$attendance->attendance_date,
         "checkin"=>$attendance->checkin,
         "checkout"=>$attendance->checkout,
         "checkintype"=>$attendance->checkintype,
         "checkouttype"=>$attendance->checkouttype,
         
       
       ];

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $data,
            'code' => '200',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HR\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(AttendanceRequest $request, Attendance $attendance)
    {
        $attendance->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code' => '200',
        ], 200);
    }
}
