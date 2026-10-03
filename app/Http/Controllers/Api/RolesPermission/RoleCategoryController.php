<?php

namespace App\Http\Controllers\Api\RolesPermission;

use App\Http\Controllers\Controller;
use App\Http\Requests\RolesPermission\RoleCategoryRequest;
use App\Models\RolesPermission\RoleCategory;
use App\Http\Controllers\Api\CommonController;
use Illuminate\Http\Request;

class RoleCategoryController extends Controller
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
        $perPage = $request->perPage ?? "40";
        $page = $request->page ?? "1";

        $agentId = $request->agentId;

        if (!is_null($agentId)) {

            $response = RoleCategory::orderBy($orderBy, $orderType)->paginate($perPage);
            $count = RoleCategory::count();

            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Role Category Found',
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
                "role_category_name" => $value->role_category_name,
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
    public function store(RoleCategoryRequest $request)
    {
        $response = RoleCategory::create($request->all());

        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $response,
            'code'=>'200'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RolesPermission\RoleCategory  $rolecategory
     * @return \Illuminate\Http\Response
     */
    public function show(RoleCategory $rolecategory)
    {
        $response = RoleCategory::findOrFail($rolecategory->id);

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
     * @param  \App\Models\RolesPermission\RoleCategory  $rolecategory
     * @return \Illuminate\Http\Response
     */
    public function update(RoleCategoryRequest $request, RoleCategory $rolecategory)
    {
        $rolecategory->update($request->all());

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $rolecategory,
            'code'=>'200'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RolesPermission\RoleCategory  $rolecategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(RoleCategoryRequest $request, RoleCategory $rolecategory)
    {
        $rolecategory->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code'=>'200'
        ], 200);
    }
}
