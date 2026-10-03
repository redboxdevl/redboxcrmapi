<?php

namespace App\Http\Controllers\Api\TaskManagement;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\TaskManagement\TaskAttachmentRequest;
use App\Models\TaskManagement\TaskAttachment;
use Illuminate\Http\Request;

class TaskAttachmentController extends Controller
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

            $response = TaskAttachment::orderBy($orderBy, $orderType)->paginate($perPage);
            $count = TaskAttachment::count();

            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Task Attachement Found',
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
                "emp_id" => $value->emp_id,
                "comment_id" => $value->comment_id,
                "attachement" => $value->attachement,
                "attachement_date_time" => $value->attachement_date_time,
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
    public function store(TaskAttachmentRequest $request)
    {
        $response = TaskAttachment::create($request->all());

        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $response,
            'code'=>'200'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TaskManagement\TaskAttachment  $taskAttachment
     * @return \Illuminate\Http\Response
     */
    public function show(TaskAttachment $Taskattachment , $id='')
    {
        $response = TaskAttachment::findOrFail($id);

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
     * @param  \App\Models\TaskManagement\TaskAttachment  $taskAttachment
     * @return \Illuminate\Http\Response
     */
    public function update(TaskAttachmentRequest $request, TaskAttachment $taskattachment)
    {
        $taskattachment->update($request->all());

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $taskattachment,
            'code'=>'200'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TaskManagement\TaskAttachment  $taskAttachment
     * @return \Illuminate\Http\Response
     */
    public function destroy(TaskAttachmentRequest $request, TaskAttachment $taskattachment)
    {
        $taskattachment->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code'=>'200'
        ], 200);
    }
}
