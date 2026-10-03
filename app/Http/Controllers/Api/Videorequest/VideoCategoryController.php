<?php

namespace App\Http\Controllers\Api\Videorequest;

use App\Http\Requests\Videorequestform\VideoCategoriesRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Models\Videocategory;
use Illuminate\Http\Request;

class VideoCategoryController extends Controller
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

        $response = Videocategory::orderBy($orderBy, $orderType)->paginate($perPage);

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
                "deleted_at" => $value->deleted_at,
                "video_category_name" => $value->video_category_name,
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
    public function store(VideoCategoriesRequest $request)
    {
        $response = Videocategory::create($request->all());

        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $response
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Videocategory\Videocategory  $videocategory
     * @return \Illuminate\Http\Response
     */
    public function show(Videocategory $videocategory)
    {
        $response = Videocategory::findOrFail($videocategory->id);

        return response()->json([
            'message' => "Data retrieved successfully!",
            'data' => $response
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Videocategory\Videocategory  $videocategory
     * @return \Illuminate\Http\Response
     */
    public function update(VideoCategoriesRequest $request, Videocategory $videocategory)
    {
        $videocategory->update($request->all());

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $videocategory
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Videocategory\Videocategory  $videocategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(VideoCategoriesRequest $request, Videocategory $videocategory)
    {
        $videocategory->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
        ], 200);
    }
}
