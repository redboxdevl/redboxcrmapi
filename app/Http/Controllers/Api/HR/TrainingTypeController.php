<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Trainingtype;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\HR\TrainingTypeRequest;
use Illuminate\Http\Request;
use Validator;

class TrainingTypeController extends Controller
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

            $response = Trainingtype::orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Trainingtype::count();
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
                "training_type_name" => $value->training_type_name,
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
    public function store(TrainingTypeRequest $request)
    {
        $response = Trainingtype::create($request->all());

        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $response,
            'code' => '200',
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TrainingType  $record
     * @return \Illuminate\Http\Response
     */
    public function show(Trainingtype $record,$id)
    {
        $response = Trainingtype::findOrFail($id);

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
     * @param  \App\Models\Trainingtype  $trainingtype
     * @return \Illuminate\Http\Response
     */
    public function update(TrainingTypeRequest $request, Trainingtype $trainingtype)
    {
        $trainingtype->update($request->all());

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $trainingtype,
            'code' => '200',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Trainingtype  $trainingtype
     * @return \Illuminate\Http\Response
     */
    public function destroy(TrainingTypeRequest $request, Trainingtype $trainingtype)
    {
        $trainingtype->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code' => '200',
        ], 200);
    }
}
