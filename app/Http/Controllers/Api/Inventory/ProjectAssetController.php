<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Requests\Inventory\ProjectAssetRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Models\Inventory\Projectasset;
use Illuminate\Http\Request;

class ProjectAssetController extends Controller
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

        $response = Projectasset::orderBy($orderBy, $orderType)->paginate($perPage);

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
                "project_id" => $value->project_id,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "asset_name" => $value->asset_name,
                "project_asset_path" => $value->project_asset_path,
                "asset_type" => $value->asset_type
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
    public function store(ProjectAssetRequest $request)
    {
        $response = Projectasset::create($request->all());

        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $response
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inventory\ProjectAsset  $projectAsset
     * @return \Illuminate\Http\Response
     */
    public function show(ProjectAsset $projectAsset)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inventory\ProjectAsset  $projectAsset
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProjectAsset $projectAsset)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inventory\ProjectAsset  $projectAsset
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProjectAsset $projectAsset)
    {
        //
    }
}
