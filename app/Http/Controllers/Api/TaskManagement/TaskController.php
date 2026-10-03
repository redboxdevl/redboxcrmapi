<?php

namespace App\Http\Controllers\Api\TaskManagement;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\TaskManagement\TaskRequest;
use App\Models\TaskManagement\Task;
use App\Models\TaskManagement\TaskChecklist;
use App\Models\TaskManagement\TaskAttachment;
use App\Models\TaskManagement\TaskHistory;
use App\Models\TaskManagement\TaskComment;
use App\Models\TaskManagement\Priority;
use App\Models\TaskManagement\TaskStatus;

use App\Models\HR\Employee;
use Illuminate\Http\Request;



use Illuminate\Support\Str;
use Carbon\Carbon;

class TaskController extends Controller
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
        $status = $request->status;
        
        

        if (!is_null($agentId) && empty($status)) {

            $response = Task::with(['getcheckList','gettaskComment','gettaskAttachement','gettaskHistory' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['agentId'=>$agentId])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Task::where(['agentId'=>$agentId,'taskStatus'=>$status])->count();

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
        }else if(!is_null($agentId) && !is_null($status)){
            
            $response = Task::with(['getcheckList','gettaskComment','gettaskAttachement','gettaskHistory' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['agentId'=>$agentId,'taskStatus'=>$status])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Task::where(['agentId'=>$agentId,'taskStatus'=>$status])->count();
            
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
    
    public function getSelfTask (Request $request){
        
        $orderBy = $request->orderBy ?? "id";
        $orderType = $request->orderType ?? "desc";
        $perPage = $request->perPage ?? "20";
        $page = $request->page ?? "1";

        $agentId = $request->agentId;
        $estatus = $request->estatus;
        
        if(!empty($agentId)){
            
            $response = Task::with(['getcheckList','gettaskComment','gettaskAttachement','gettaskHistory' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where('assigned_to','LIKE','%'.$agentId.'%')->get();
            $count = Task::where('assigned_to','LIKE','%'.$agentId.'%')->count();
            
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
        $data2=[];

        foreach ($apiResponse as $key => $value) {
            
            $myArray2 = explode(',', $value->assigned_to);
            $myArray = str_replace(array('[',']'),'',$myArray2);
           
            $AssignByEmp = Employee::where(['id'=>$value->assigned_by])->first();
            $AssignToEmp = Employee::whereIn('id',$myArray)->get();
            
            
            
            $PriorityData = Priority::where(['id'=>$value->priorityId])->first();
            // // $countPrio = count($PriorityData);
            
            // $CommentData = TaskComment::where(['emp_id'=>$value->agentId,'task_id_pk'=>$value->id])->get();
            
           
           
            // $commentArray = [];
            // foreach($CommentData as $comData){
                
            //     $getEmpName = Employee::where(['id'=>$comData['emp_id']])->get();
            //     $empdataArr = [];
                
            //     $obj = [
            //       'emp_name' => $getEmpName[0] == null ? '' : $getEmpName[0]->emp_name,
            //       'email' => $getEmpName[0]->email,
            //       'profile_pic' => $getEmpName[0]->profile_pic,
            //       'id' => $getEmpName[0]->id,
                 
            //     ];
                
            //     $commentArray[]=array(
            //     'comment'=>$comData['comment'],
            //     'task_id_pk'=>$comData['task_id_pk'],
            //     'comment_date_time'=>$comData['comment_date_time'],
            //     'empdata'=>$obj,
            //     );
            // }
            
          
            // // echo '<pre>';
            // // print_r($value->gettaskHistory);
            
          
            // $StatusName = TaskStatus::where(['id'=>$value->gettaskHistory->status])->get();
            
            if($value->taskStatus == 0){
                $statusTask = "Pending";
            }else if($value->taskStatus == 1){
                $statusTask = "Open";
            }else if($value->taskStatus == 2){
                $statusTask = "On Going";
            }else if($value->taskStatus == 3){
                $statusTask = "Complete";
            }else{
                $statusTask="";
            }
        
            
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                'myArray'=>$myArray,
                "task_id"=>$value->task_id,
                'agentId'=>$value->agentId,
                "task_title"=>$value->task_title,
                "description"=>$value->description,
                "departmentId"=>$value->departmentId,
                "dueDate"=>$value->dueDate,
                "priorityId"=>$value->priorityId,
                "assigned_by"=>$value->assigned_by,
                "assigned_to"=>$value->assigned_to,
                "taskStatus"=>$statusTask,
                 "taskStatusId"=>$value->taskStatus,
                "getcheckList"=>$value->getcheckList,
                "AssignByEmp"=>$AssignByEmp,
                "AssignToEmp"=>$AssignToEmp,
                "PriorityData"=>$PriorityData,
                'gettaskComment'=>$value->gettaskComment,
               
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
    public function store(TaskRequest $request)
    {
        
        $uniqid = Str::random(5);

        $response = Task::create([
        'task_id'=> $uniqid,
        'agentId'=> $request->post('emp_id'),
        'task_title'=> $request->post('task_title'),
        'description'=> $request->post('description'),
        'departmentId'=> $request->post('departmentId'),
        'dueDate'=> $request->post('due_date') == null || $request->post('due_date') == null ? date("Y-m-d H:i:s") : $request->post('due_date'),
        'priorityId'=> $request->post('priority'),
        'assigned_by'=> $request->post('assigned_by'),
        'assigned_to'=> $request->post('assigned_to'),
        'taskStatus'=>0, //0 => pending  => approved
        ]);
        
        $taskId = $response->id;
        
        $checklistitemJson = json_decode($request->checklist_item,JSON_UNESCAPED_SLASHES);
        
        foreach($checklistitemJson as $myCheckList){
         
         $checkList = $myCheckList['checkList'];
         
         $CheckListDetails = TaskChecklist::create([
            'task_id_pk' =>  $taskId,
            'checklist_item'=>$checkList,
            'status'=>0,
        ]);
        }
         
        //  if($request->post('checklist_item') == 0){
             
        //  }else{
             
        //       $exp = explode(",",$request->post('checklist_item'));
             
        //      foreach ($exp as $key => $valuePer) {
                
        //         $model=new TaskChecklist();
                
        //         $model->checklist_item = $valuePer;
        //         $model->task_id_pk = $taskId;
        //         $model->save();
    
        //     }
             
        //  }
        
        
        // $modelTask = TaskComment::create([
        //         'task_id_pk'=>$taskId,
        //         'emp_id'=>$request->post('emp_id'),
        //         'comment'=>$request->post('comment'),
        //         'comment_date_time'=>date("Y-m-d H:i:s"),
        // ]);
        
        // $commentId = $modelTask->id;
        
        // if($request->post("attachement") == 0){
        //     $imgData = 0;
        // }else{
            
        //     if($request->hasFile("attachement")){
        //     $image = $request->file("attachement");
        //     $ext = $image->extension();
        //     $image_name=time().'.'.$ext;
        //     $image->storeAs('/public/taskAttachement/',$image_name);
        //     }else{
                
        //     }
            
        //      $imgData = "public/storage/taskAttachement/".$image_name;   
            
        // }
     
        
       
         
    
        // TaskAttachment::create([
        //     'task_id_pk' => $taskId,
        //     'emp_id' => $request->post('emp_id'),
        //     'comment_id' => $commentId,
        //     'attachement' => $imgData,
        //     'attachement_date_time' => date("Y-m-d H:i:s"),
        // ]);

        // TaskHistory::create([
        //     'task_id_pk' => $taskId,
        //     'priority' => $request->post('priority'),
        //     'status' => $request->post('status'),
        //     'assigned_by' => $request->post('assigned_by'),
        //     'assigned_to' => $request->post('assigned_to'),
        //     'due_date' => $request->post('due_date'),
        // ]);
        
         

        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $response,
            'code'=>'200'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TaskManagement\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        
        $response = Task::with(['getcheckList','gettaskComment','gettaskAttachement','gettaskHistory' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['id'=>$task->id])->get();
      
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                  return response()->json([
                    'message' => "Data retrieved successfully!",
                    'data' => $data,
                    'code'=>'200'
                ], 200);
            }

            // FAIL
            else {
                return app(CommonController::class)->getEmptyListResponse();
            }
        // $response = Task::findOrFail($task->id);
        
        
     
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TaskManagement\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(TaskRequest $request, Task $task)
    {
        $task->update($request->all());

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $task,
            'code'=>'200'
        ], 200);
    }
    
    public function taskchangeLog(Request $request){
        
        $task_id_pk = $request->task_id_pk;
        $status = $request->status;
        
        Task::where(['id'=>$task_id_pk])->update([
            'taskStatus' =>  $status,
        ]);
        
         return response()->json([
            'message' => "Data updated successfully!",
            'data' => $request->all(),
            'code'=>'200'
        ], 200);
        
    }
    
    
    public function taskchecklistLog(Request $request){
        
        $task_id_pk = $request->task_id_pk;
        $checklist_item = $request->checklist_item;
        
        foreach($checklist_item as $checkId){
            TaskChecklist::where(['id'=>$checkId,'task_id_pk'=>$task_id_pk])->update([
                'status' =>  1,
            ]);
        }
        
         return response()->json([
            'message' => "Data updated successfully!",
            'code'=>'200'
        ], 200);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TaskManagement\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(TaskRequest $request, Task $task)
    {
        $task->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code'=>'200'
        ], 200);
    }
}
