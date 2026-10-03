<?php

namespace App\Http\Controllers\Api\TaskManagement;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\TaskManagement\TaskCommentRequest;
use App\Models\TaskManagement\TaskComment;
use App\Models\HR\Employee;
use Illuminate\Http\Request;

class TaskCommentController extends Controller
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

            $response = TaskComment::orderBy($orderBy, $orderType)->paginate($perPage);
            $count = TaskComment::count();

            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Task Comment Found',
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
                "comment" => $value->comment,
                "comment_date_time" => $value->comment_date_time,
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
    public function store(TaskCommentRequest $request)
    {
        $response = TaskComment::create([
         
         'task_id_pk' => $request->post('task_id_pk'),
         'emp_id' => $request->post('emp_id'),
         'comment' => $request->post('comment'),
         'comment_date_time' => date("Y-m-d"),
            
        ]);
        
        $empData = Employee::where(['id'=>$request->post('emp_id')])->get();
        
        $empArr = [
            "id" => $empData[0]->id,
            "emp_name" => $empData[0]->emp_name,
            "profile_pic" => $empData[0]->profile_pic,
            "created_at" => $empData[0]->created_at,
            "updated_at" => $empData[0]->updated_at,
            "deleted_at" => $empData[0]->deleted_at,
        ];
        
        $newData = [
            
            "comment"=>$response->comment,
            "task_id_pk"=>$response->task_id_pk,
            "comment_date_time"=>$response->comment_date_time,
            "empData"=>$empArr,
              
        ];

        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $newData,
            'code'=>'200'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TaskManagement\TaskComment  $taskComment
     * @return \Illuminate\Http\Response
     */
    public function show(TaskComment $taskcomment)
    {
        $response = TaskComment::findOrFail($taskcomment->id);

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
     * @param  \App\Models\TaskManagement\TaskComment  $taskComment
     * @return \Illuminate\Http\Response
     */
    public function update(TaskCommentRequest $request, TaskComment $taskcomment)
    {
        $taskcomment->update($request->all());

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $taskcomment,
            'code'=>'200'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TaskManagement\TaskComment  $taskComment
     * @return \Illuminate\Http\Response
     */
    public function destroy(TaskCommentRequest $request, TaskComment $taskcomment)
    {
        $taskcomment->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code'=>'200'
        ], 200);
    }
}
