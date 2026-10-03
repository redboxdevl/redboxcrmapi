<?php

namespace App\Http\Controllers\Api\TaskManagement;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\TaskManagement\TaskHistoryRequest;
use App\Models\TaskManagement\TaskHistory;
use Illuminate\Http\Request;

class TaskHistoryController extends Controller
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

            $response = TaskHistory::orderBy($orderBy, $orderType)->paginate($perPage);
            $count = TaskHistory::count();

            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Task Found',
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
                "task_id_pk" => $value->task_id_pk,
                "status" => $value->status,
                "description" => $value->description,
                "status_changed_by" => $value->status_changed_by,
                "status_date_time" => $value->status_date_time,
                "assigned_by" => $value->assigned_by,
                "assigned_to" => $value->assigned_to,
                "assigned_date_time" => $value->assigned_date_time,
                "priority" => $value->priority,
                "priority_changed_by" => $value->priority_changed_by,
                "priority_date_time" => $value->priority_date_time,
                "due_date" => $value->due_date,
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
    public function store(TaskHistoryRequest $request)
    {
        $response = TaskHistory::create($request->all());

        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $response,
            'code'=>'200'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TaskManagement\TaskHistory  $taskHistory
     * @return \Illuminate\Http\Response
     */
    public function show(TaskHistory $taskhistory)
    {
        $response = TaskHistory::findOrFail($taskhistory->id);

        return response()->json([
            'message' => "Data retrieved successfully!",
            'data' => $response,
            'code'=>'200'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TaskManagement\TaskHistory  $taskHistory
     * @return \Illuminate\Http\Response
     */
    public function update(TaskHistoryRequest $request)
    {   
         $model = TaskHistory::where('task_id_pk','=',$request->post('task_id_pk'))->first();
        $model->task_id_pk = $request->post('task_id_pk');
        $model->status = $request->post('status');
        $model->status_changed_by = $request->post('status_changed_by');
        $model->status_date_time = date("Y-m-d H:i:s");
        $model->update();
      
        // $taskhistory->update($request->all());

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $model,
            'code'=>'200'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TaskManagement\TaskHistory  $taskHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy(TaskHistoryRequest $request, TaskHistory $taskhistory)
    {
        $taskhistory->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code'=>'200'
        ], 200);
    }
}
