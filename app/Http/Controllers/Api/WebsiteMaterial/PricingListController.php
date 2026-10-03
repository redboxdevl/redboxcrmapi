<?php

namespace App\Http\Controllers\Api\WebsiteMaterial;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\WebsiteMaterial\PricingRequest;
use App\Models\WebsiteMaterial\Pricingpage;
use App\Models\DocumentType;
use App\Models\Issuingauthority;
use App\Models\propertyType;
use App\Models\Purposeprop;


use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;

class PricingListController extends Controller
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
        $q = $request->q ?? "";
        $agentId = $request->agentId;

        if (is_null($agentId)) {

            $response = Pricingpage::orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Pricingpage::count();
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Pricing List Found',
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
                "pageTitle" => $value->pageTitle,
                "pageBanner" => $value->pageBanner,
                "headerEditor" => $value->headerEditor,
                "videoLink" => $value->videoLink,
                "middleEditor" => $value->middleEditor,
                "footerEditor" => $value->footerEditor,
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
    public function store(PricingRequest $request)
    {
        
        if($request->hasFile("pageBanner")){
            $image = $request->file("pageBanner");
            $ext = $image->extension();
            $image_name=time().'.'.$ext;
            $image->storeAs('/public/pricingBanner/',$image_name);
            $FileData = "public/storage/pricingBanner/".$image_name;
            
        }else{
            $FileData="";
        }
        
        
        $response = Pricingpage::create([
            'pageTitle' =>  $request->pageTitle,
            'pageUrl'=> Str::slug($request->pageTitle),
            'headerEditor' =>  $request->headerEditor,
            'videoLink' =>  $request->videoLink,
            'middleEditor' =>  $request->middleEditor,
            'footerEditor' =>  $request->footerEditor,
            'pageBanner' =>  $FileData,
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
    public function show(Pricingpage $pricingpage , $id)
    {
        $response = Pricingpage::where(['id'=>$id])->first();
            
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
    public function update(PricingRequest $request, Pricingpage $pricingpage,$id="")
    {

        $checkFiles = Pricingpage::findOrFail($id);
        
        if($request->hasFile("pageBanner")){
            $image = $request->file("pageBanner");
            $ext = $image->extension();
            $image_name=time().'.'.$ext;
            $image->storeAs('/public/pricingBanner/',$image_name);
            $image_names = "public/storage/pricingBanner/".$image_name;
        }else{
            $image_names=$checkFiles->pageBanner;
        }

        Pricingpage::where(['id'=>$id])->update([
            'pageTitle' =>  $request->pageTitle,
            'pageUrl'=>Str::slug($request->pageTitle),
            'headerEditor' =>  $request->headerEditor,
            'videoLink' =>  $request->videoLink,
            'middleEditor' =>  $request->middleEditor,
            'footerEditor' =>  $request->footerEditor,
            'pageBanner' =>  $image_names,
        ]);
        
        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $checkFiles,
            'code'=>'200'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(PricingRequest $request, Pricingpage $pricingpage)
    {
        $pricingpage->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code'=>'200'
        ], 200);
    }
    
    
    
    
}