<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Termination;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\HR\TerminationRequest;
use Illuminate\Http\Request;

use Validator;

class TerminationController extends Controller
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

            $response = Termination::with(['getEmployee','getTerminationBy' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['emp_id'=>$agentId])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Termination::count();
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
                "emp_name" => $value->getEmployee->emp_name,
                "subject" => $value->subject,
                "type" => $value->type,
                "description" => $value->description,
                "notice_date" => $value->notice_date,
                "termination_date" => $value->termination_date,
                "termination_by" => $value->termination_by,
                "termination_by_name" => $value->getTerminationBy == '' ? '' : $value->getTerminationBy->emp_name,
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
    public function store(TerminationRequest $request)
    {
        $response = Termination::create($request->all());

        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $response,
            'code' => '200',
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EmpTermination  $record
     * @return \Illuminate\Http\Response
     */
    public function show(Termination $record , $id)
    {
        $response = Termination::findOrFail($id);

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
     * @param  \App\Models\Termination  $termination
     * @return \Illuminate\Http\Response
     */
    public function update(TerminationRequest $request, Termination $termination)
    {
        $termination->update($request->all());

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $termination,
            'code' => '200',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Termination  $termination
     * @return \Illuminate\Http\Response
     */
    public function destroy(TerminationRequest $request, Termination $termination)
    {
        $termination->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code' => '200',
        ], 200);
    }
}
