<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Noticeboard;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\HR\NoticeBoardRequest;
use Illuminate\Http\Request;
use Validator;

class NoticeBoardController extends Controller
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

        if (!is_null($agentId) && $agentId !='all') {

            $response = Noticeboard::with(['getEmployee' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['emp_id'=>$agentId])->latest()->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Noticeboard::where(['emp_id'=>$agentId])->latest()->count();
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
        }else if ($agentId == 'all') {
            
            

            $response = Noticeboard::with(['getEmployee' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Noticeboard::count();
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
                "emp_name" => $value->getEmployee == null || $value->getEmployee == '' ? '' : $value->getEmployee->emp_name,
                "notice_title" => $value->notice_title,
                "description" => $value->description,
                "status" => $value->status,
                "attachement" => $value->attachement,
                "published_date" => $value->published_date,
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
    public function store(NoticeBoardRequest $request)
    {
        $response = Noticeboard::create($request->all());

        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $response,
            'code' => '200',
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Noticeboard  $record
     * @return \Illuminate\Http\Response
     */
    public function show(Noticeboard $noticeboard)
    {
        $response = Noticeboard::findOrFail($noticeboard->id);

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
     * @param  \App\Models\Noticeboard  $noticeboard
     * @return \Illuminate\Http\Response
     */
    public function update(NoticeBoardRequest $request, Noticeboard $noticeboard)
    {
        $noticeboard->update($request->all());

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $noticeboard,
            'code' => '200',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Noticeboard  $noticeboard
     * @return \Illuminate\Http\Response
     */
    public function destroy(NoticeBoardRequest $request, Noticeboard $noticeboard)
    {
        $noticeboard->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code' => '200',
        ], 200);
    }
}
