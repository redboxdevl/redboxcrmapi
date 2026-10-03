<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Requests\Inventory\InventoryHoldRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Models\Inventory\Inventoryhold;
use App\Models\Inventory\Projectinventory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InventoryHoldController extends Controller
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

        $response = Inventoryhold::orderBy($orderBy, $orderType)->paginate($perPage);
        $count = Inventoryhold::count();
        
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
            
            $now = Carbon::now();
            
            $to = Carbon::parse($value->hold_date);
            $from = Carbon::parse($now);
            
            $years = $to->diffInYears($from);
            $months = $to->diffInMonths($from);
            $weeks = $to->diffInWeeks($from);
            $days = $to->diffInDays($from);
            $hours = $to->diffInHours($from);
            $minutes = $to->diffInMinutes($from);
            $seconds = $to->diffInSeconds($from);
            
            // $finalVar = "Year- ".$years." Month- ".$months." Weeks- ".$weeks." Days- ".$days." hours- ".$hours." minutes- ".$minutes." seconds- ".$seconds;
            
            $array = array(
                'days'=>$days,
                'hours'=>$hours,
                'minutes'=>$minutes,
                'seconds'=>$seconds,
            );

            array_push($data, (object) [
                "id" => $value->id,
                "inventory_id" => $value->inventory_id,
                "customer_id" => $value->customer_id,
                "employee_id" => $value->employee_id,
                "hold_date" => $value->hold_date,
                "hold_time" => $value->hold_time,
                "hold_test" => $array,
                "hold_desc" => $value->hold_desc,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
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
    public function store(InventoryHoldRequest $request)
    {
        $response = Inventoryhold::create($request->all());
        $invid = $request->post('inventory_id');
        Projectinventory::where('id', $invid)
            ->update([
            'status' => 'hold',
            ]);

        return response()->json([
            'message' => "Data saved successfully!",
            'code' => '200'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inventory\Inventoryhold  $inventoryhold
     * @return \Illuminate\Http\Response
     */
    public function show(Inventoryhold $inventoryhold)
    {
        $response = Inventoryhold::findOrFail($inventoryhold->id);

        return response()->json([
            'message' => "Data retrieved successfully!",
            'data' => $response
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inventory\Inventoryhold  $inventoryhold
     * @return \Illuminate\Http\Response
     */
    public function update(InventoryHoldRequest $request, Inventoryhold $inventoryhold)
    {
        $inventoryhold->update($request->all());

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $inventoryhold
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inventory\Inventoryhold  $inventoryhold
     * @return \Illuminate\Http\Response
     */
    public function destroy(InventoryHoldRequest $request, Inventoryhold $inventoryhold)
    {
        $inventoryhold->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
        ], 200);
    }
}
