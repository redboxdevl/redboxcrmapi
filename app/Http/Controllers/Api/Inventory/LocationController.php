<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Requests\Inventory\LocationRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Models\Inventory\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
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

        $response = Location::orderBy($orderBy, $orderType)->paginate($perPage);

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
                "city_id" => $value->city_id,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "location_name" => $value->location_name
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
    public function store(LocationRequest $request)
    {
        $response = Location::create($request->all());

        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $response
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inventory\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function show(Location $location)
    {
        $response = Location::findOrFail($location->id);

        return response()->json([
            'message' => "Data retrieved successfully!",
            'data' => $response
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inventory\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function update(LocationRequest $request, Location $location)
    {
        $location->update($request->all());

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $location
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inventory\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function destroy(LocationRequest $request, Location $location)
    {
        $location->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
        ], 200);
    }
}
