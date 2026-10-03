<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Training;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\HR\TrainingRequest;
use Illuminate\Http\Request;
use Validator;

class TrainingController extends Controller
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

            $response = Training::with(['getEmployee','getDepartment','getTrainingType' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Training::count();
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
                "emp_id" => $value->emp_id,
                "emp_name" => $value->getEmployee == null ? '' : $value->getEmployee->emp_name,
                "trainee_name" => $value->trainee_name,
                "department_id" => $value->department_id,
                "department_name" => $value->getDepartment == null ? '' : $value->getDepartment->department_name,
                "training_type_id" => $value->training_type_id,
                "training_type_name" => $value->getTrainingType->training_type_name,
                "training_title" => $value->training_title,
                "description" => $value->description,
                "from_date" => $value->from_date,
                "to_date" => $value->to_date,
                "start_time" => $value->start_time,
                "end_time" => $value->end_time,
                "status" => $value->status,
                "attachement" => $value->attachement,
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
    public function store(TrainingRequest $request)
    {
        $response = Training::create($request->all());

        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $response,
            'code' => '200',
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Training  $record
     * @return \Illuminate\Http\Response
     */
    public function show(Training $record,$id)
    {
        $response = Training::findOrFail($id);

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
     * @param  \App\Models\Training  $trainings
     * @return \Illuminate\Http\Response
     */
    public function update(TrainingRequest $request, Training $training)
    {
        $training->update($request->all());

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $training,
            'code' => '200',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Training  $training
     * @return \Illuminate\Http\Response
     */
    public function destroy(TrainingRequest $request, Training $training)
    {
        $training->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code' => '200',
        ], 200);
    }
}
