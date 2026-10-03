<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\HR\CircularlistRequest;
use App\Models\HR\Circularlist;
use App\Models\HR\Circularcategory;
use App\Models\DocumentType;
use App\Models\Issuingauthority;
use App\Models\propertyType;
use App\Models\Purposeprop;
use App\Models\City;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;

class CircularpolicyController extends Controller
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
        $agentId = $request->agentId ?? "";
        $CircularCategory = $request->CircularCategory ?? "";
        $subjectName = $request->subjectName ?? "";
        $policyDate = $request->policyDate ?? ""; 

        if (is_null($agentId) && empty($CircularCategory) && empty($subjectName) && empty($policyDate)) {
            
            $response = Circularlist::orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = Circularlist::count();
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Circular Policy List Found',
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
        }   else if ($agentId == 'all' && empty($CircularCategory) && empty($subjectName) && empty($policyDate)) {
            
                $response = Circularlist::orderBy($orderBy, $orderType)->paginate($perPage);
                
            $count = Circularlist::count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Circular Policy List Found',
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
            
        }else if ($agentId == 'all' && !empty($CircularCategory) && empty($subjectName) && empty($policyDate)) {
            
                $response = Circularlist::where(['catpolicyId'=>$CircularCategory])->orderBy($orderBy, $orderType)->paginate($perPage);
                
            $count = Circularlist::where(['catpolicyId'=>$CircularCategory])->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Circular Policy List Found',
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
            
        }else if ($agentId == 'all' && empty($CircularCategory) && !empty($subjectName) && empty($policyDate)) {
            
                $response = Circularlist::where('subjectName','LIKE','%'.$subjectName.'%')->orderBy($orderBy, $orderType)->paginate($perPage);
                
            $count = Circularlist::where('subjectName','LIKE','%'.$subjectName.'%')->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Circular Policy List Found',
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
            
        }else if ($agentId == 'all' && empty($CircularCategory) && empty($subjectName) && !empty($policyDate)) {
            
                $response = Circularlist::where(['policyDate'=>$policyDate])->orderBy($orderBy, $orderType)->paginate($perPage);
                
            $count = Circularlist::where(['policyDate'=>$policyDate])->count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Circular Policy List Found',
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
            
            $Circularcategory = Circularcategory::where(['id'=>$value->catpolicyId])->first();
          
            array_push($data, (object) [
                "id" => $value->id,
                "created_at" => $value->created_at,
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "catpolicyId" => $value->catpolicyId,
                "subjectName" => $value->subjectName,
                "policyDate" => $value->policyDate,
                "Attachment" => $value->Attachment,
                "categoryTitle"=>$Circularcategory == '' || $Circularcategory == null ? '' : $Circularcategory->categoryTitle,
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
    public function store(CircularlistRequest $request)
    {
        
        if($request->hasFile("Attachment")){
            $image = $request->file("Attachment");
            $ext = $image->extension();
            $image_name="AttachmentPolicy-".time().'.'.$ext;
            $image->storeAs('/public/bankLogo/',$image_name);
            $FileData = "public/storage/bankLogo/".$image_name;
            
        }else{
            $FileData="";
        }
        
        
        $response = Circularlist::create([
            'catpolicyId' =>  $request->catpolicyId,
            'subjectName' => $request->subjectName,
            'policyDate' =>  $request->policyDate,
            'Attachment' =>  $FileData,
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
    public function show(Circularlist $circularlist,$id="")
    {
        $response = Circularlist::where(['id'=>$id])->get();
            
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
    public function update(CircularlistRequest $request, $id="")
    {

        $checkFiles = Circularlist::findOrFail($id);
        
        if($request->hasFile("Attachment")){
            $image = $request->file("Attachment");
            $ext = $image->extension();
            $image_name="AttachmentPolicy-".time().'.'.$ext;
            $image->storeAs('/public/bankLogo/',$image_name);
            $image_names = "public/storage/bankLogo/".$image_name;
        }else{
            $image_names=$checkFiles->Attachment;
        }

        Circularlist::where("id",$id)->update([
            'catpolicyId' =>  $request->catpolicyId,
            'subjectName' => $request->subjectName,
            'policyDate' =>  $request->policyDate,
            'Attachment' =>  $image_names,
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
     
    public function destroy(Request $request,$id='')
    {
        //$payreciepts->delete($request->all());
        
        $PettyCash = Circularlist::where(['id'=>$id])->delete();
      
        return response()->json([
            'message' => "Data deleted successfully!",
            'data'=>$PettyCash,
            'code'=>'200'
        ], 200);
    }
  
    
    
    
    
}