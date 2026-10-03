<?php

namespace App\Http\Controllers\Api\TaskManagement;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\TaskManagement\TaskChecklistRequest;
use App\Models\TaskManagement\TaskChecklist;
use Illuminate\Http\Request;

class TaskChecklistController extends Controller
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

            $response = TaskChecklist::orderBy($orderBy, $orderType)->paginate($perPage);
            $count = TaskChecklist::count();

            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Task Checklist Found',
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
                "checklist_item" => $value->checklist_item,
                "status" => $value->status,
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
    public function store(TaskChecklistRequest $request)
    {   
        $checKList = $request->all('checklist_item');
        $task_id_pk = $request->post('task_id_pk');

        foreach ($request->post('checklist_item') as $key => $valuePer) {
            
            $model=new TaskChecklist();

            $model->checklist_item = $valuePer;
            $model->task_id_pk = $task_id_pk;
            $data = $model->save();

        }

        return response()->json([
            'message' => "Data saved successfully!",
            'code'=>'200'
        ], 200);
       
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TaskManagement\TaskChecklist  $taskChecklist
     * @return \Illuminate\Http\Response
     */
    public function show(TaskChecklist $taskchecklist)
    {
        $response = TaskChecklist::findOrFail($taskchecklist->id);

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
     * @param  \App\Models\TaskManagement\TaskChecklist  $taskChecklist
     * @return \Illuminate\Http\Response
     */
    public function update(TaskChecklistRequest $request)
    {
        //$taskchecklist->update($request->all());
         $exp = explode(",",$request->post('checklist_item'));
         
         if($request->post('status') == 0){
             $dataCo=0;
         }else{
             $dataCo=1;
         }
         
         $edata = [];
        foreach ($exp as $key => $valuePer) {
            
            $model=TaskChecklist::where('id','=',$valuePer)->first();
            
            $model->status = $dataCo;
            $model->update();
            
            $edata[]=$model;

        }
        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $edata,
            'status'=>$request->post('status'),
            'code'=>'200'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TaskManagement\TaskChecklist  $taskChecklist
     * @return \Illuminate\Http\Response
     */
    public function destroy(TaskChecklistRequest $request, TaskChecklist $taskchecklist)
    {
        $taskchecklist->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code'=>'200'
        ], 200);
    }
}
