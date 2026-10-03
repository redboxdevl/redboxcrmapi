<?php

namespace App\Http\Controllers\Api\Videorequest;

use App\Http\Requests\Videorequestform\VideoFormRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Models\Videorequest;
use Illuminate\Http\Request;
use Validator;

class VideoRequestController extends Controller
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
        
        
        
        if(!is_null($agentId) && $agentId != 'all'){
            $response = Videorequest::with(['getcategories','getProjects','getEmployee' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['emp_id'=>$agentId])->orderBy($orderBy, $orderType)->paginate($perPage);
        }else if($agentId == 'all'){
            $response = Videorequest::with(['getcategories','getProjects','getEmployee' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->orderBy($orderBy, $orderType)->paginate($perPage);
        }else{
            $response = Videorequest::with(['getcategories','getProjects','getEmployee' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->orderBy($orderBy, $orderType)->paginate($perPage);
        }
        
        $count = Videorequest::count();
        
        

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

    // PREPARE LIST
    public function renderList(Object $apiResponse)
    {
        $data = [];

        foreach ($apiResponse as $key => $value) {
            


            array_push($data, (object) [
                "id" => $value->id,
                "updated_at" => $value->updated_at,
                "created_at" => $value->created_at,
                "deleted_at" => $value->deleted_at,
                "emp_id" => $value->emp_id,
                "emp_name" => $value['getEmployee'] == '' ? '' : $value['getEmployee']->emp_name,
                
                "video_category_id" => $value->video_category_id,
                "video_category_name" => $value['getcategories'] == '' ? '' : $value['getcategories']->video_category_name,
                "project_id" => $value->project_id,
                "project_name" => $value['getProjects'] == '' ? '' : $value['getProjects']->project_name,
                "video_format" => $value->video_format,
                "video_topic" => $value->video_topic,
                "guest_name" => $value->guest_name,
                "other_category" => $value->other_category,
                "gender" => json_decode($value->gender),
                "age_group" => json_decode($value->age_group),
                "education" => json_decode($value->education),
                "location" => json_decode($value->location),
                "video_outline" => json_decode($value->video_outline),
                "video_instructions" => $value->video_instructions,
                "video_ref_image" => $value->video_ref_image,
                "video_ref_ppt" => $value->video_ref_ppt,
                "video_ref_pdf" => $value->video_ref_pdf,
                "video_ref_web" => $value->video_ref_web,
                "video_ref_video" => $value->video_ref_video,
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
    public function store(VideoFormRequest $request)
    {
        $response = Videorequest::create([
            'emp_id'=>$request->post('emp_id'),
            'video_category_id'=>$request->post('video_category_id'),
            'project_id'=>$request->post('project_id'),
            'video_format'=>$request->post('video_format'),
            'video_topic'=>$request->post('video_topic'),
            'guest_name'=>$request->post('guest_name'),
            'other_category'=>$request->post('other_category'),
            'gender'=>json_encode($request->post('gender'),JSON_UNESCAPED_SLASHES),
            'age_group'=>json_encode($request->post('age_group'),JSON_UNESCAPED_SLASHES),
            'education'=>json_encode($request->post('education'),JSON_UNESCAPED_SLASHES),
            'location'=>json_encode($request->post('location'),JSON_UNESCAPED_SLASHES),
            'video_outline'=>json_encode($request->post('video_outline'),JSON_UNESCAPED_SLASHES),
            'video_instructions'=>$request->post('video_instructions'),
            'video_ref_image'=>$request->post('video_ref_image'),
            'video_ref_ppt'=>$request->post('video_ref_ppt'),
            'video_ref_pdf'=>$request->post('video_ref_pdf'),
            'video_ref_web'=>$request->post('video_ref_web'),
            'video_ref_video'=>$request->post('video_ref_video'),
            'status'=>'Pending'
        ]);

        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $response,
            'code'=>'200'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inventory\Videorequest  $videorequest
     * @return \Illuminate\Http\Response
     */
    public function show(Videorequest $videorequest)
    {
        $response = Videorequest::findOrFail($videorequest->id);

        return response()->json([
            'message' => "Data retrieved successfully!",
            'data' => $response
        ], 200);
    }
    
    public function VideoFormUpdate (Request $request){
        
        $rules=array(
            'videoId'=>'required',
        );

        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return $validator->errors();
        }else{
            
            $model = Videorequest::where('id','=',$request->post('videoId'))->first();
            $model->status = 'Approved';
            $data = $model->update();
            
            return response()->json([
                'message' => "Data updated successfully!",
                'code' => '200',
            ], 200);
            
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inventory\Videorequest  $videorequest
     * @return \Illuminate\Http\Response
     */
    public function update(VideoFormRequest $request, Videorequest $videorequest)
    {
        $videorequest->update($request->all());

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $videorequest
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inventory\Videorequest  $videorequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(VideoFormRequest $request, Videorequest $videorequest)
    {
        $videorequest->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
        ], 200);
    }
}
