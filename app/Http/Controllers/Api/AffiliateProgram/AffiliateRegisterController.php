<?php

namespace App\Http\Controllers\Api\AffiliateProgram;

use App\Http\Controllers\Controller;
use App\Http\Requests\AffiliateProgram\AffiliateRegisterRequest;
use App\Models\AffiliateProgram\AffiliateRegisterModule;
use App\Http\Controllers\Api\CommonController;
use Illuminate\Http\Request;
use Mail;
use Validator;


class AffiliateRegisterController extends Controller
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

            $response = AffiliateRegisterModule::orderBy($orderBy, $orderType)->paginate($perPage);
            $count = AffiliateRegisterModule::count();

            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Affiliate Found',
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
                "title" => $value->title,
                "full_name" => $value->full_name,
                "cell_no" => $value->cell_no,
                "email_address" => $value->email_address,
                "business_name" => $value->business_name,
                "address" => $value->address,
                "emp_id" => $value->emp_id,
                "status" => $value->status,
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
    public function store(AffiliateRegisterRequest $request)
    {

        $otp = mt_rand(1000,9999);

        $response = AffiliateRegisterModule::create([
            'title' => $request->title,
            'full_name' => $request->full_name,
            'cell_no' => $request->cell_no,
            'email_address' => $request->email_address,
            'business_name' => $request->business_name,
            'address' => $request->address,
            'emp_id' => $request->emp_id,
            'otpCode' => $otp,
            'Password' => $request->Password,
        ]);

        $data=['code'=>$otp];

        $user['to']=$request->post('email_address');

        Mail::send('affiliateProgram',$data,function($messages) use($user){

            $messages->to($user['to']);

            $messages->from('affiliate@redbox.estate');

            $messages->subject('Otp Request');

        });

        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $response,
            'code'=>'200'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AffiliateProgram\AffiliateRegisterModule  $affiliateRegisterModule
     * @return \Illuminate\Http\Response
     */
    public function show(AffiliateRegisterModule $affiliateRegisterModule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AffiliateProgram\AffiliateRegisterModule  $affiliateRegisterModule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AffiliateRegisterModule $affiliateRegisterModule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AffiliateProgram\AffiliateRegisterModule  $affiliateRegisterModule
     * @return \Illuminate\Http\Response
     */
    public function destroy(AffiliateRegisterModule $affiliateRegisterModule)
    {
        //
    }


    public function OtpVerification (Request $request){

        $rules=array(
            'otpCode'=>'required',
        );
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return $validator->errors();
        }else{

            $checkOtp = AffiliateRegisterModule::where(['otpCode'=>$request->post('otpCode')])->get();
            if(count($checkOtp) > 0){

                return response()->json([
                    'message' => "Otp Verify Successfully!",
                    'data' => $checkOtp,
                    'code'=>'200'
                ], 200);

            }else{

                return response()->json([
                    'message' => "Otp Incorrect",
                    'code'=>'203'
                ], 203);
            }
            

        }

    }

    
}
