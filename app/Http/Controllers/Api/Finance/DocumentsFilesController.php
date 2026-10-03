<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\DocumentfilesRequest;
use App\Models\Finance\Documentlist;
use App\Models\DocumentType;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;
use DB;

class DocumentsFilesController extends Controller
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
        
        $response = Documentlist::orderBy($orderBy, $orderType)->paginate($perPage);
        $count = Documentlist::count();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Security Files Found',
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
                "doctype"=>$value->doctype,
                "docImg"=>$value->docImg,
                "status"=>$value->status,
                        
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
    public function store(DocumentfilesRequest $request)
    {
        
        if($request->hasFile("docImg")){
            $image = $request->file("docImg");
            $ext = $image->extension();
            $image_name=time().'.'.$ext;
            $image->storeAs('public/AssetImages/',$image_name);
             $FileData = "public/storage/AssetImages/".$image_name;
             $FileExt = $ext;
            
        }else{
            $FileData="";
             $FileExt = "";
        }
        
        $response = Documentlist::create([
            'doctype' =>  $FileExt,
            'docImg' =>  $FileData,
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
    public function show($id="")
    {
       
        $response = Documentlist::where(['id'=>$id])->get();
            
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
    public function update(DocumentfilesRequest $request,$id="")
    {

        $checkFiles = Documentlist::findOrFail($id);
        
        if($request->hasFile("docImg")){
            $image = $request->file("docImg");
            $ext = $image->extension();
            $image_name=time().'.'.$ext;
            $image->storeAs('public/AssetImages/',$image_name);
            $image_name = "public/storage/AssetImages/".$image_name;
            $fileType=$ext;
        }else{
            $image_name=$checkFiles->docImg;
            $fileType=$checkFiles->doctype;
        }
        
        
        Documentlist::where(['id'=>$id])->update([
            'doctype' =>  $fileType,
            'docImg' =>  $image_name,
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
    public function destroy(DocumentfilesRequest $request, Documentlist $documentlist)
    {
        $documentlist->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code'=>'200'
        ], 200);
    }
    
}