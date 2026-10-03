<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Requests\Inventory\ProjectRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Models\Inventory\Project;
use App\Models\Inventory\Projectasset;
use App\Models\Inventory\Projectcategory;
use App\Models\Inventory\Projectinventory;
use App\Models\Finance\Inventoriesfilters;

use Illuminate\Http\Request;

class ProjectController extends Controller
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
        $company_id = $request->company_id ?? "";

        $response = Project::with(['getProCategory','getProLogo' => function ($query){
            $query->orderBy('id', 'desc')->get();
        } ,])->where(['status'=>1,'companyId'=>$company_id])->orderBy($orderBy, $orderType)->paginate($perPage);
        $count = Project::where(['status'=>1,'companyId'=>$company_id])->count();
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
            
            $countProInve = Projectinventory::where(['project_id'=>$value->id])->count();
        
            array_push($data, (object) [
                "id" => $value->id,
                "project_name" => $value->project_name,
                "project_short_name" => $value->project_short_name,
                "paymentPlanYearly" => json_decode($value->paymentPlanYearly),
                "updated_at" => $value->updated_at,
                "deleted_at" => $value->deleted_at,
                "location_id" => $value->location_id,
                "country_id" => $value->country_id,
                "city_id" => $value->city_id,
                "address" => $value->address,
                "commission" => $value->commission,
                "kam" => $value->kam,
                "project_category"=>$value->getProCategory,
                "asset_name"=>$value->getProLogo == '' ? '' : $value->getProLogo->asset_name,
                "project_asset_path"=>$value->getProLogo == '' ? '' :$value->getProLogo->project_asset_path,
                "totalInventories" => $countProInve,
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
    public function store(ProjectRequest $request)
    {   
        

        $response = Project::create($request->all());
        $Proid = $response->id;

        if($request->hasFile("projectLogo")){
            $image = $request->file("projectLogo");
            $ext = $image->extension();
            $image_name=time().'.'.$ext;
            $image->storeAs('/public/projectLogo/',$image_name);

            Projectasset::create([
                'project_id'=>$Proid,
                'asset_name'=>$image_name,
                'project_asset_path'=>"public/storage/projectLogo/".$image_name,
                'asset_type'=>$ext,
            ]);

        }else{
        }

        $category = $request->post("category");
        $expCat = explode(",",$category);
        foreach ($expCat as $key => $value) {
            
            Projectcategory::create([
                'project_id'=>$Proid,
                'project_category_name'=>$value,
            ]);

        }


        return response()->json([
            'message' => "Data saved successfully!",
            'data' => $response,
            'code' => '200',
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inventory\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        $response = Project::with(['getProCategory','getProLogo' => function ($query){
            $query->orderBy('id', 'desc')->get();
        } ,])->where(['id'=>$project->id])->get();
        $count = Project::count();
        // SUCCESS
        if (count($response) > 0) {
            $data = $this->renderList($response);

            return response()->json([
                'code' => '200',
                'success' => true,
                'message' => 'Record Found',
                'data' => $data,
            ], 200);
        }
      
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inventory\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(ProjectRequest $request, Project $project)
    {
       
       $ProjectId = $request->post('ProjectId');
        
        $checkPic = Projectasset::where(['project_id'=>$ProjectId])->first();
        
        if($request->hasFile("projectLogo")){
             $image = $request->file("projectLogo");
            $ext = $image->extension();
            $image_name=time().'.'.$ext;
            $image->storeAs('/public/projectLogo/',$image_name);
            $image_name = $image_name;
            
        }else{
            $image_name=$checkPic->asset_name;
        }
        
        Projectasset::where('project_id', $ProjectId)
            ->update([
            'asset_name' => $image_name,
            'project_asset_path'=>"public/storage/projectLogo/".$image_name,
            ]);
        
        $project->update([
            'project_name' =>  $request->project_name,
            'project_short_name' =>  $request->project_short_name,
            'paymentPlanYearly' =>  $request->post('PaymentPlan'),
            'location_id' => $request->location_id,
            'country_id' => $request->country_id,
            'city_id' => $request->city_id,
            'address' => $request->address,
            'commission' => $request->commission,
            'kam' => $request->kam,
        ]);
        
        //$project->update($request->all());

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $project,
            'code' => '200',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inventory\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProjectRequest $request, Project $project)
    {
        //$project->delete($request->all());
        
        $id = $request->post('proid');
        
        $res=Project::where('id',$id)->delete();
       
        return response()->json([
            'message' => "Data deleted successfully!",
            'data'=>$res,
            'code' => '200',
        ], 200);
    }
    
    
    public function Addinventoriesfilters(Request $request){
        
        $fieldName = $request->fieldName;
        $companyId = $request->companyId;
        
        $check = Inventoriesfilters::where(['fieldName'=>$fieldName,'companyId'=>$companyId])->first();
        
            if(is_null($check)){
                
                $data = Inventoriesfilters::create([
                'fieldName'=>$fieldName,
                'companyId'=>$companyId,
                'status'=>'active',
                ]);
            
                if($data){
                
                    return response()->json([
                    'message' => "FIlter Inventories Add Successfully!",
                    'data'=>$data,
                    'code' => '200',
                    ], 200);
                    
                }else{
                    
                    return response()->json([
                    'message' => "Error Please Try Again",
                    'data'=>'',
                    'code' => '200',
                    ], 200);
                    
                }
                
            }else{
                
               return response()->json([
                    'message' => "Record Already Exists",
                    'data'=>'',
                    'code' => '201',
                ], 201);
                
            }
        
        
    }
    
    public function getInventoriesFilters(Request $request){
        
        $companyId = $request->companyId;
        
        $data = Inventoriesfilters::where(['companyId'=>$companyId])->get();
        if(count($data) > 0){
            return response()->json([
                'message' => "Data Found",
                'data'=>$data,
                'code' => '200',
            ], 200);
        }else{
            return response()->json([
            'message' => "Data not Found",
            'data'=>$data,
            'code' => '201',
        ], 201);
        }
         
    }
}
