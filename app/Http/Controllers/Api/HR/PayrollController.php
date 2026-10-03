<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\HR\PayrollRequest;
use App\Models\HR\Payroll;
use App\Models\HR\Attendance;
use App\Models\HR\Employee;
use App\Models\HR\Designation;

use Illuminate\Http\Request;

class PayrollController extends Controller
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
        $perPage = $request->perPage ?? "12";
        $page = $request->page ?? "1";
        
        $StartDates = $request->StartDates ?? '';
        $EndDate = $request->EndDate ?? '';
      
        
        $agentId = $request->agentId;

        if (is_null($agentId) && empty($StartDates)) {

            $response = Payroll::with(['getEmployee' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['payrollDate'=>date("Y-m-d")])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Payroll::count();

            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Payroll Found',
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
        }else if(is_null($agentId) || !empty($StartDates)){
           
            $response = Payroll::with(['getEmployee' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['payrollDate'=>$StartDates])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Payroll::count();

            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Payroll Found',
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
            
            $Designation = Designation::where(['id'=>$value->getEmployee->designation_id])->get();
            
            foreach($Designation as $desigName){
                
                 array_push($data, (object) [
                    "id" => $value->id,
                    "created_at" => $value->created_at,
                    "updated_at" => $value->updated_at,
                    "deleted_at" => $value->deleted_at,
                    "emp_id" => $value->emp_id,
                    "attendance_count" => $value->attendance_count,
                    "gross_salary" => $value->gross_salary,
                    "guel_charges" => $value->guel_charges,
                    "mobile_charges" => $value->mobile_charges,
                    "advance_amt" => $value->advance_amt,
                    "account_no" => $value->account_no,
                    "tax_amt" => $value->tax_amt,
                    "net_salary" => $value->net_salary,
                    "getEmployee"=>$value->getEmployee,
                    "designation_name"=>$desigName->designation_name,
                    "payrollMonth" => date("F",strtotime($value->payrollDate)),
                    "payrollYear" => date("Y",strtotime($value->payrollDate)),
                ]);
            }

           
        }

        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PayrollRequest $request)
    {
        $StartDates = $request->StartDates;
        $EndDate = $request->EndDate;
        // $response = Payroll::create($request->all());
        
        $collection = Attendance::whereBetween('attendance_date',[$StartDates,$EndDate])->groupBy('emp_id')
        ->selectRaw('count(*) as total, emp_id')
        ->get();
        
        $JsonData = [];
        
        foreach($collection as $empData){
            
            $empName = Employee::where(['id'=>$empData->emp_id])->first();
            
            $checkInOut = Attendance::where(['emp_id'=>$empData->emp_id])->whereNotNull('checkin')->whereNotNull('checkout')->count();
            
            $response = Payroll::create([
                'emp_id' =>  $empData->emp_id,
                'attendance_count' =>  $checkInOut,
                'gross_salary' =>  0,
                'guel_charges' =>  0,
                'mobile_charges' =>  0,
                'advance_amt' =>  0,
                'tax_amt' =>  0,
                'net_salary' =>  0,
                'payrollDate' => date("Y-m-d",strtotime($StartDates)),
            ]);
            
            $JsonData[]=$response;
            
            // $JsonData[]=array(
            //   'emp_id'=>$empData->emp_id,
            //   'emp_name'=>$empName->emp_name,
            //   'designation'=>$empName->designation_id,
            //   'casual_Leave'=>0,
            //   'sick_leave'=>0,
            //   'emergency_leave'=>0,
            //   'present_days_count'=>$empData->total,
            //   'total_days_count'=>30,
            //   'gross_salary'=>0,
            //   'fuel_charges	'=>0,
            //   'mobile_charges'=>0,
            //   'advance_amt'=>0,
            //   'account_no'=>$empData->bank_account,
            //   'bank'=>$empData->bank_name,
            //   'tax_amt'=>0,
            //   'net_salary'=>0,
            // );
        }
            
        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $JsonData,
            'code'=>'200'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function show(Payroll $payroll)
    {
        $response = Payroll::findOrFail($payroll->id);

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
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function update(BranchRequest $request, Branch $branch)
    {
        // $branch->update($request->all());

        // return response()->json([
        //     'message' => "Data updated successfully!",
        //     'data' => $branch,
        //     'code'=>'200'
        // ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function destroy(BranchRequest $request, Branch $branch)
    {
        // $branch->delete($request->all());

        // return response()->json([
        //     'message' => "Data deleted successfully!",
        //     'code'=>'200'
        // ], 200);
    }
}