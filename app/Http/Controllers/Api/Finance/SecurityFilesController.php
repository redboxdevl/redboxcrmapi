<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Finance\SecurityfilesRequest;
use App\Models\Finance\SecurityFiles;
use App\Models\DocumentType;
use App\Models\Issuingauthority;
use App\Models\propertyType;
use App\Models\Purposeprop;
use App\Models\Finance\Releaselog;


use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Validator;
use Mail;
use DB;

class SecurityFilesController extends Controller
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
        
        $agentId = $request->agentId;
        
        $dtype = $request->dtype ?? "";
        $ptype = $request->ptype ?? "";
        $dref = $request->dref ?? "";
        $dstatus = $request->dstatus ?? "";
        
        $Title = $request->Title ?? "";
        $Description = $request->Description ?? "";
        $IssuingAuthority = $request->IssuingAuthority ?? "";
        $companyId = $request->companyId ?? "";

        if (is_null($agentId) && $agentId == '') {

           
            if($dtype != ''){
               
                $response = SecurityFiles::with(['getDocTypeId','getProTypeId' => function ($query){
                 $query->orderBy('id', 'desc')->get();
            } ,])->where(['docTypeId'=>$dtype,'company_id'=>$companyId])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = SecurityFiles::where(['docTypeId'=>$dtype,'company_id'=>$companyId])->count();
            
            }else if($ptype !=''){
            
                $response = SecurityFiles::with(['getDocTypeId','getProTypeId' => function ($query){
                 $query->orderBy('id', 'desc')->get();
            } ,])->where(['proTypeId'=>$ptype,'company_id'=>$companyId])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = SecurityFiles::where(['proTypeId'=>$ptype,'company_id'=>$companyId])->count();
            
            }else if($dref !=''){
            
                $response = SecurityFiles::with(['getDocTypeId','getProTypeId' => function ($query){
                 $query->orderBy('id', 'desc')->get();
            } ,])->where('docRef', 'LIKE', "%$dref%")->where(['company_id'=>$companyId])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = SecurityFiles::where('docRef', 'LIKE', "%$dref%")->where(['company_id'=>$companyId])->count();
            
            }else if($dstatus !=''){
                
                $response = SecurityFiles::with(['getDocTypeId','getProTypeId' => function ($query){
                 $query->orderBy('id', 'desc')->get();
            } ,])->where(['docStatus'=>$dstatus,'company_id'=>$companyId])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = SecurityFiles::where(['docStatus'=>$dstatus,'company_id'=>$companyId])->count();
            
            }else if($Title !=''){
                
                $response = SecurityFiles::with(['getDocTypeId','getProTypeId' => function ($query){
                 $query->orderBy('id', 'desc')->get();
            } ,])->where('ownerShipTitle', 'LIKE', "%$Title%")->where(['company_id'=>$companyId])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = SecurityFiles::where('ownerShipTitle', 'LIKE', "%$Title%")->where(['company_id'=>$companyId])->count();
            
            }else if($Description !=''){
                
                $response = SecurityFiles::with(['getDocTypeId','getProTypeId' => function ($query){
                 $query->orderBy('id', 'desc')->get();
            } ,])->where('docDesc', 'LIKE', "%$Description%")->where(['company_id'=>$companyId])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = SecurityFiles::where('docDesc', 'LIKE', "%$Description%")->where(['company_id'=>$companyId])->count();
            
            }else if($IssuingAuthority !=''){
                
                $response = SecurityFiles::with(['getDocTypeId','getProTypeId' => function ($query){
                 $query->orderBy('id', 'desc')->get();
            } ,])->where(['issuingauthorityId'=>$IssuingAuthority,'company_id'=>$companyId])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = SecurityFiles::where(['issuingauthorityId'=>$IssuingAuthority,'company_id'=>$companyId])->count();
            
            }else{
                
                $response = SecurityFiles::with(['getDocTypeId','getProTypeId' => function ($query){
                 $query->orderBy('id', 'desc')->get();
            } ,])->where(['company_id'=>$companyId])->orderBy($orderBy, $orderType)->paginate($perPage);
            
             $count = SecurityFiles::where(['company_id'=>$companyId])->count();
            }
            
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
        }   else if ($agentId == 'all') {
            
            if($dtype != ''){
               
                $response = SecurityFiles::with(['getDocTypeId','getProTypeId' => function ($query){
                 $query->orderBy('id', 'desc')->get();
            } ,])->where(['docTypeId'=>$dtype,'company_id'=>$companyId])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = SecurityFiles::where(['docTypeId'=>$dtype,'company_id'=>$companyId])->count();
            
            }else if($ptype !=''){
            
                $response = SecurityFiles::with(['getDocTypeId','getProTypeId' => function ($query){
                 $query->orderBy('id', 'desc')->get();
            } ,])->where(['proTypeId'=>$ptype,'company_id'=>$companyId])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = SecurityFiles::where(['proTypeId'=>$ptype,'company_id'=>$companyId])->count();
            
            }else if($dref !=''){
            
                $response = SecurityFiles::with(['getDocTypeId','getProTypeId' => function ($query){
                 $query->orderBy('id', 'desc')->get();
            } ,])->where('docRef', 'LIKE', "%$dref%")->where(['company_id'=>$companyId])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = SecurityFiles::where('docRef', 'LIKE', "%$dref%")->where(['company_id'=>$companyId])->count();
            
            }else if($dstatus !=''){
                
                $response = SecurityFiles::with(['getDocTypeId','getProTypeId' => function ($query){
                 $query->orderBy('id', 'desc')->get();
            } ,])->where(['docStatus'=>$dstatus,'company_id'=>$companyId])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = SecurityFiles::where(['docStatus'=>$dstatus,'company_id'=>$companyId])->count();
            
            }else if($Title !=''){
                
                $response = SecurityFiles::with(['getDocTypeId','getProTypeId' => function ($query){
                 $query->orderBy('id', 'desc')->get();
            } ,])->where('ownerShipTitle', 'LIKE', "%$Title%")->where(['company_id'=>$companyId])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = SecurityFiles::where('ownerShipTitle', 'LIKE', "%$Title%")->where(['company_id'=>$companyId])->count();
            
            }else if($Description !=''){
                
                $response = SecurityFiles::with(['getDocTypeId','getProTypeId' => function ($query){
                 $query->orderBy('id', 'desc')->get();
            } ,])->where('docDesc', 'LIKE', "%$Description%")->where(['company_id'=>$companyId])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = SecurityFiles::where('docDesc', 'LIKE', "%$Description%")->where(['company_id'=>$companyId])->count();
            
            }else if($IssuingAuthority !=''){
                
                $response = SecurityFiles::with(['getDocTypeId','getProTypeId' => function ($query){
                 $query->orderBy('id', 'desc')->get();
            } ,])->where(['issuingauthorityId'=>$IssuingAuthority,'company_id'=>$companyId])->orderBy($orderBy, $orderType)->paginate($perPage);
            
            $count = SecurityFiles::where(['issuingauthorityId'=>$IssuingAuthority,'company_id'=>$companyId])->count();
            
            }else{
                
                $response = SecurityFiles::with(['getDocTypeId','getProTypeId' => function ($query){
                 $query->orderBy('id', 'desc')->get();
            } ,])->where(['company_id'=>$companyId])->orderBy($orderBy, $orderType)->paginate($perPage);
            
             $count = SecurityFiles::where(['company_id'=>$companyId])->count();
            }
            
                
            
           
            
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
                "publish_date" => date("Y-m-d",strtotime($value->created_at)),
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "rec_id" => $value->rec_id,
                "document_title" => $value->getDocTypeId == null || $value->getDocTypeId == '' ? '' : $value->getDocTypeId->document_title,
                "type_name" => $value->getProTypeId == null || $value->getProTypeId == '' ? '' : $value->getProTypeId->type_name,
                "docRef" => $value->docRef,
                "docStatus" => $value->docStatus,
                "releaseName" => $value->releaseName,
                "releaseDate" => $value->releaseDate,
                "Attachement" => $value->Attachement,
                "status" => $value->status,
                "ownerShipTitle" => $value->ownerShipTitle,
                "docNature" => $value->docNature,
                "docDesc" => $value->docDesc,
                "issuingauthorityId" => $value->issuingauthorityId,
                "company_abbr" => $value->getCompanyId == null || $value->getCompanyId == '' ? '' : $value->getCompanyId->company_abbr,
                "company_name" => $value->getCompanyId == null || $value->getCompanyId == '' ? '' : $value->getCompanyId->company_name,
                "PurposeId"=>$value->PurposeId,
                        
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
    public function store(SecurityfilesRequest $request)
    {
        
        if($request->hasFile("Attachement")){
            $image = $request->file("Attachement");
            $ext = $image->extension();
            $image_name=time().'.'.$ext;
            $image->storeAs('/public/securityFiles/',$image_name);
             $FileData = "public/storage/securityFiles/".$image_name;
            
        }else{
            $FileData="";
        }
       
        
        $docTypeIds=0;
        
        if($request->docTypeId == 'others'){
            
            $DocumType = DocumentType::create([
                'document_title' =>  $request->otherdocTypeId,
                'status' =>  1,
            ]);
            
            $lastId = $DocumType->id;
            $docTypeIds = $lastId;
            
        }else{
            $docTypeIds = $request->docTypeId;
        }
        
        if($request->proTypeId == 'others'){
            
            $ProPType = propertyType::create([
                'type_name' =>  $request->otherproTypeId,
                'approved' => 'yes',
            ]);
            
            $lastId = $ProPType->id;
            $ProPType = $lastId;
            
        }else{
            $ProPType = $request->proTypeId;
        }
        
        if($request->issuingAuthority == 'others'){
            
            $IssuingType = Issuingauthority::create([
                'issuingTitle' =>  $request->OtherissuingAuthority,
                'status' => '1',
            ]);
            
            $lastId = $IssuingType->id;
            $IssUingPType = $lastId;
            
        }else{
            $IssUingPType = $request->issuingAuthority;
        }
        
        if($request->Purpose == 'others'){
            
            $PurposeType = Purposeprop::create([
                'purposetitle' =>  $request->OtherPurpose,
                'status' => '1',
            ]);
            
            $lastId = $PurposeType->id;
            $PurposeTypeId = $lastId;
            
        }else{
            $PurposeTypeId = $request->Purpose;
        }
        
         $secCount = SecurityFiles::select('rec_id')->latest()->first();
        
        $countItem = $secCount->rec_id + 1; 
        
        $response = SecurityFiles::create([
            'docTypeId' =>  $docTypeIds,
            'rec_id'=>$countItem,
            'proTypeId' =>  $ProPType,
            'issuingauthorityId' =>  $IssUingPType,
            'docRef' =>  $request->docRef,
            'docStatus' =>  $request->docStatus,
            'releaseName' =>  $request->releaseName,
            'releaseDate' =>  $request->releaseDate,
            'Attachement' =>  $FileData,
            'status' =>  'pending',
            'uploadedBy' => $request->uploadedBy,
            'docNature' => $request->docNature,
            'ownerShipTitle' => $request->ownerShipTitle,
            'docDesc' => $request->docDesc,
            'CompaniesId' => $request->CompaniesId,
            'company_id' => $request->company_id,
            'PurposeId' => $PurposeTypeId,
        ]);
        
        // $securityId = $response->id;
        
        // if($request->releaseName ==''){
            
        // }else{
            
        //   Releaselog::create([
        //         'securityId' =>  $securityId,
        //         'releaseTo' =>  $request->releaseName,
        //         'releaseDate' =>  $request->releaseDate,
        //         'releaseUpdateDate' =>  date("Y-m-d H:i:s"),
        //     ]);
            
        // }
        
        
        
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
    public function show(SecurityFiles $securityfiles , $id="")
    {
        $response = SecurityFiles::where(['id'=>$id])->get();
            
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
    public function update(SecurityfilesRequest $request, SecurityFiles $Securityfiles,$id="")
    {

        $checkFiles = SecurityFiles::findOrFail($id);
        
        if($request->hasFile("Attachement")){
            $image = $request->file("Attachement");
            $ext = $image->extension();
            $image_name=time().'.'.$ext;
            $image->storeAs('/public/securityFiles/',$image_name);
            $image_name = "public/storage/securityFiles/".$image_name;
        }else{
            $image_name=$checkFiles->Attachement;
        }
        
        $docTypeIds=0;
        
        if($request->docTypeId == 'others'){
            
            $DocumType = DocumentType::create([
                'document_title' =>  $request->otherdocTypeId,
                'status' =>  1,
            ]);
            
            $lastId = $DocumType->id;
            $docTypeIds = $lastId;
            
        }else{
            $docTypeIds = $request->docTypeId;
        }
        
        if($request->proTypeId == 'others'){
            
            $ProPType = propertyType::create([
                'type_name' =>  $request->otherproTypeId,
                'approved' => 'yes',
            ]);
            
            $lastId = $ProPType->id;
            $ProPType = $lastId;
            
        }else{
            $ProPType = $request->proTypeId;
        }
        
        if($request->issuingAuthority == 'others'){
            
            $IssuingType = Issuingauthority::create([
                'issuingTitle' =>  $request->OtherissuingAuthority,
                'status' => '1',
            ]);
            
            $lastId = $IssuingType->id;
            $IssUingPType = $lastId;
            
        }else{
            $IssUingPType = $request->issuingAuthority;
        }
        
        
        if($request->Purpose == 'others'){
            
            $PurposeType = Purposeprop::create([
                'purposetitle' =>  $request->OtherPurpose,
                'status' => '1',
            ]);
            
            $lastId = $PurposeType->id;
            $PurposeTypeId = $lastId;
            
        }else{
            $PurposeTypeId = $request->Purpose;
        }

        SecurityFiles::where(['id'=>$id])->update([
            'docTypeId' =>  $docTypeIds,
            'proTypeId' =>  $ProPType,
            'docRef' =>  $request->docRef,
            'docStatus' =>  $request->docStatus,
            'releaseName' =>  $request->releaseName,
            'releaseDate' =>  $request->releaseDate,
            'Attachement' =>  $image_name,
            'status' =>  $request->status,
            'uploadedBy' => $request->uploadedBy,
            'docNature' => $request->docNature,
            'ownerShipTitle' => $request->ownerShipTitle,
            'docDesc' => $request->docDesc,
            'issuingauthorityId'=>$IssUingPType,
            'CompaniesId' => $request->CompaniesId,
            'PurposeId' => $PurposeTypeId,
            'company_id' => $request->company_id,
        ]);
        
        if($request->releaseName =='' || $request->releaseName == 'null'){
            
        }else{
            
          Releaselog::create([
                'securityId' =>  $id,
                'releaseTo' =>  $request->releaseName,
                'releaseDate' =>  $request->releaseDate,
                'releaseUpdateDate' =>  date("Y-m-d H:i:s"),
            ]);
            
        }
        
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
    public function destroy(SecurityfilesRequest $request, SecurityFiles $securityfiles)
    {
        $securityfiles->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code'=>'200'
        ], 200);
    }
    
    
    public function SecurityFilters(Request $request){
     
        $agentId = $request->agentId;
        
        $SerialNoStart = $request->SerialNoStart ?? "";
        $SerialNoEnd = $request->SerialNoEnd ?? "";
        
        $EntryDateFrom = $request->EntryDateFrom ?? "";
        $EntryDateTo = $request->EntryDateTo ?? "";
        
        $ReleaseDateFrom = $request->ReleaseDateFrom ?? "";
        $ReleaseDateTo = $request->ReleaseDateTo ?? "";
        
        $DocTypeFrom = $request->DocTypeFrom ?? "";
        $DocTypeTo = $request->DocTypeTo ?? "";
        
        $DocNatureFrom = $request->DocNatureFrom ?? "";
        $DocNatureTo = $request->DocNatureTo ?? "";
        
        $PropTypeFrom = $request->PropTypeFrom ?? "";
        $PropTypeTo = $request->PropTypeTo ?? "";
        
        $CompaniesStartId = $request->CompaniesStartId ?? "";
        $CompaniesEndId = $request->CompaniesEndId ?? "";

        if (is_null($agentId) && $agentId == '') {

           
            if($EntryDateFrom != '' && $EntryDateTo !=''){
               
                $response = SecurityFiles::with(['getDocTypeId','getProTypeId' => function ($query){
                 $query->orderBy('id', 'desc')->get();
                 } ,])->whereBetween(DB::raw('DATE(created_at)'), array($EntryDateFrom,$EntryDateTo))->get();
                $count = SecurityFiles::whereBetween(DB::raw('DATE(created_at)'), array($EntryDateFrom,$EntryDateTo))->count();
            
            }else if($ReleaseDateFrom != '' && $ReleaseDateTo !=''){
               
                $response = SecurityFiles::with(['getDocTypeId','getProTypeId' => function ($query){
                 $query->orderBy('id', 'desc')->get();
                 } ,])->whereBetween(DB::raw('DATE(releaseDate)'), array($ReleaseDateFrom,$ReleaseDateTo))->get();
                $count = SecurityFiles::whereBetween(DB::raw('DATE(releaseDate)'), array($ReleaseDateFrom,$ReleaseDateTo))->count();
            
            }else if($DocTypeFrom != '' && $DocTypeTo !=''){
               
                $response = SecurityFiles::with(['getDocTypeId','getProTypeId' => function ($query){
                 $query->orderBy('id', 'desc')->get();
                 } ,])->whereBetween('docTypeId', array($DocTypeFrom,$DocTypeTo))->get();
                $count = SecurityFiles::whereBetween('docTypeId', array($DocTypeFrom,$DocTypeTo))->count();
            
            }else if($DocNatureFrom != '' && $DocNatureTo !=''){
               
                $response = SecurityFiles::with(['getDocTypeId','getProTypeId' => function ($query){
                 $query->orderBy('id', 'desc')->get();
                 } ,])->whereBetween('docNature', array($DocNatureFrom,$DocNatureTo))->get();
                $count = SecurityFiles::whereBetween('docNature', array($DocNatureFrom,$DocNatureTo))->count();
            
            }else if($PropTypeFrom != '' && $PropTypeTo !=''){
               
                $response = SecurityFiles::with(['getDocTypeId','getProTypeId' => function ($query){
                 $query->orderBy('id', 'desc')->get();
                 } ,])->whereBetween('proTypeId', array($PropTypeFrom,$PropTypeTo))->get();
                $count = SecurityFiles::whereBetween('proTypeId', array($PropTypeFrom,$PropTypeTo))->count();
            
            }else if($CompaniesStartId != '' && $CompaniesEndId !=''){
               
                $response = SecurityFiles::with(['getDocTypeId','getProTypeId' => function ($query){
                 $query->orderBy('id', 'desc')->get();
                 } ,])->whereBetween('CompaniesId', array($CompaniesStartId,$CompaniesEndId))->get();
                $count = SecurityFiles::whereBetween('CompaniesId', array($CompaniesStartId,$CompaniesEndId))->count();
            
            }else{
                
                $response = SecurityFiles::with(['getDocTypeId','getProTypeId' => function ($query){
                 $query->orderBy('id', 'desc')->get();
            } ,])->get();
            
             $count = SecurityFiles::count();
            }
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Security Files Found',
                    'data' => $data,
                    'orderBy' => '',
                    'orderType' => '',
                    'perPage' => '',
                    'pageNo' => '',
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
    
    
    
}