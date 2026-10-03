<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Requests\Inventory\ProjectCategoryRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Models\Inventory\Projectcategory;
use Illuminate\Http\Request;

class ProjectCategoryController extends Controller
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

        $response = Projectcategory::orderBy($orderBy, $orderType)->paginate($perPage);

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
                "project_id" => $value->project_name,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "project_category_name" => $value->location_id,
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
    public function store(ProjectCategoryRequest $request)
    {
        $response = Projectcategory::create($request->all());

        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $response
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inventory\Projectcategory  $projectcategory
     * @return \Illuminate\Http\Response
     */
    public function show(Projectcategory $projectcategory)
    {
        $response = Projectcategory::findOrFail($projectcategory->id);

        return response()->json([
            'message' => "Data retrieved successfully!",
            'data' => $response
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inventory\Projectcategory  $projectcategory
     * @return \Illuminate\Http\Response
     */
    public function update(ProjectCategoryRequest $request, Projectcategory $projectcategory)
    {
        $projectcategory->update($request->all());

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $projectcategory
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inventory\Projectcategory  $projectcategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProjectCategoryRequest $request, Projectcategory $projectcategory)
    {
        $projectcategory->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
        ], 200);
    }
}
