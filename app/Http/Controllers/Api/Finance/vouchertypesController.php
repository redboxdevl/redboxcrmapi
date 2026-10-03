<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\VouchertypeRequest;
use App\Models\Finance\Vouchertype;


use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;

class vouchertypesController extends Controller
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
        

        $response = Vouchertype::orderBy($orderBy, $orderType)->paginate($perPage);
        $count = Vouchertype::count();
        // SUCCESS
        if (count($response) > 0) {
            $data = $this->renderList($response);
        
            return response()->json([
                'code' => '200',
                'success' => true,
                'message' => 'Voucher Type List Found',
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
                "typeTitle" => $value->typeTitle,
                "code" => $value->code,
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
    public function store(VouchertypeRequest $request)
    {
        
        $countLas = Vouchertype::count();
        
        if($countLas == 0){
            $voucherCode = str_pad(1,3,'0',STR_PAD_LEFT);
        }else{
            $voucherCode = str_pad($countLas+1,3,'0',STR_PAD_LEFT);
        }
       
        $response = Vouchertype::create([
            'typeTitle' =>  $request->typeTitle,
            'code' => $voucherCode,
        ]);
        
        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $response,
            'code'=>'200'
        ], 200);
        
        
    }
   

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Vouchertype $vouchertype)
    {
        $response = Vouchertype::where(['id'=>$vouchertype->id])->get();
            
        if($response){
            return response()->json([
                'message' => "Data retrieved successfully!",
                'data' => $response,
                'code'=>'200'
            ], 200);
        
        }else{
           
            return response()->json([
                'message' => "Data Not Found!",
                'code'=>'203'
            ], 203);
        
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(VouchertypeRequest $request, Vouchertype $vouchertype,$id="")
    {

        $vouchertype->update([
            'typeTitle' =>  $request->typeTitle,
        ]);
        
        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $vouchertype,
            'code'=>'200'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(VouchertypeRequest $request, Vouchertype $vouchertype)
    {
        $vouchertype->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code'=>'200'
        ], 200);
    }
    
    
    
    
}