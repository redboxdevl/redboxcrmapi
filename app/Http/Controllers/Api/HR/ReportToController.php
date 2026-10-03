<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\HR\ReporttoRequest;
use App\Models\HR\Reportto;
use App\Models\HR\Department;
use Illuminate\Http\Request;

class ReportToController extends Controller
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
      
       
        $response = Reportto::orderBy($orderBy, $orderType)->paginate($perPage);
        $count = Reportto::count();
        // SUCCESS
        if (count($response) > 0) {
        $data = $this->renderList($response);
        
        return response()->json([
            'code' => '200',
            'success' => true,
            'message' => 'Report To Found',
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
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "reportToTitle"=>$value->reportToTitle,
                "departmentId"=>$value->departmentId,
                "designationId"=>$value->designationId,
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
    public function store(ReporttoRequest $request)
    {
        $response = Reportto::create([
            'departmentId' => $request->DepartmentName,
            'designationId' => $request->DesignationName,
            'reportToTitle' => $request->ReportToName,
        ]);

        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $response,
            'code' => '200',
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Designation  $designation
     * @return \Illuminate\Http\Response
     */
    public function show(Reportto $reportto)
    {
        $response = Reportto::findOrFail($reportto->id);

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
     * @param  \App\Models\Designation  $designation
     * @return \Illuminate\Http\Response
     */
    public function update(ReporttoRequest $request, Reportto $reportto)
    {
        $reportto->update($request->all());

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $reportto,
            'code' => '200',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Designation  $designation
     * @return \Illuminate\Http\Response
     */
    public function destroy(ReporttoRequest $request, Reportto $reportto)
    {
        $reportto->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'data'=>$reportto,
            'code' => '200',
        ], 200);
    }
}
