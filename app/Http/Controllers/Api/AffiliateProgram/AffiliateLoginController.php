<?php

namespace App\Http\Controllers\Api\AffiliateProgram;

use App\Http\Controllers\Controller;
use App\Models\AffiliateProgram\AffiliateLoginModule;
use App\Http\Requests\AffiliateProgram\AffiliateLoginRequest;
use App\Models\AffiliateProgram\AffiliateRegisterModule;
use Illuminate\Http\Request;

class AffiliateLoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AffiliateLoginRequest $request)
    {
        $email = $request->email_address;
        $Password = $request->password;
        
        $check = AffiliateRegisterModule::where(['email_address'=>$email,'Password'=>$Password])->get();

        if(count($check) > 0){

            return response()->json([
                'message' => "Affiliate Login successfully",
                'data' => $check,
                'code'=>'200'
            ], 200);

        }else{

            return response()->json([
                'message' => "Login Failed",
                'code'=>'203'
            ], 203);

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AffiliateProgram\AffiliateLoginModule  $affiliateLoginModule
     * @return \Illuminate\Http\Response
     */
    public function show(AffiliateLoginModule $affiliateLoginModule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AffiliateProgram\AffiliateLoginModule  $affiliateLoginModule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AffiliateLoginModule $affiliateLoginModule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AffiliateProgram\AffiliateLoginModule  $affiliateLoginModule
     * @return \Illuminate\Http\Response
     */
    public function destroy(AffiliateLoginModule $affiliateLoginModule)
    {
        //
    }
}
