<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\HR\CompanyRequest;
use App\Models\HR\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
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

            $response = Company::with(['getCountry','getCity' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->orderBy($orderBy, $orderType)->paginate($perPage);
            $count = Company::count();
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
                "company_name" => $value->company_name,
                "country_id" => $value->country_id,
                "country_name" => $value->getCountry->country_name,
                "city_id" => $value->city_id,
                "cityName" => $value->getCity->cityName,
                "postal_code" => $value->postal_code,
                "address" => $value->address,
                "contact_number" => $value->contact_number,
                "company_abbr" => $value->company_abbr,
                "companyLogo"=>$value->companyLogo,
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
    public function store(CompanyRequest $request)
    {
        
        if($request->hasFile("companyLogo")){
            $image = $request->file("companyLogo");
            $ext = $image->extension();
            $image_name="company-".time().'.'.$ext;
            $image->storeAs('/public/AssetImages2/',$image_name);
            $image_name = "public/storage/AssetImages2/".$image_name;
        }else{
            $image_name="";
        }
        
        $response = Company::create([
              'company_name'=>$request->company_name,
              'country_id'=>$request->country_id,
              'city_id'=>$request->city_id,
              'postal_code'=>$request->postal_code,
              'address'=>$request->address,
              'contact_number'=>$request->contact_number,
              'company_abbr'=>$request->company_abbr,
              'companyLogo'=>$request->companyLogo,
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
     * @param  \App\Models\Branch  $record
     * @return \Illuminate\Http\Response
     */
    public function show(Company $record , $id)
    {
        
        
        $response = Company::findOrFail($id);

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
     * @param  \App\Models\Branch  $company
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyRequest $request, Company $company)
    {
        
        $checkPic = Company::findOrFail($company->id);
        
        if($request->hasFile("companyLogo")){
            $image = $request->file("companyLogo");
            $ext = $image->extension();
            $image_name="company-".time().'.'.$ext;
            $image->storeAs('/public/AssetImages2/',$image_name);
            $image_name = "public/storage/AssetImages2/".$image_name;
        }else{
            $image_name=$checkPic->companyLogo;
        }
        
        $company->update([
            'company_name'=>$request->company_name,
            'country_id'=>$request->country_id,
            'city_id'=>$request->city_id,
            'postal_code'=>$request->postal_code,
            'address'=>$request->address,
            'contact_number'=>$request->contact_number,
            'company_abbr'=>$request->company_abbr,
            'companyLogo'=>$image_name,
        ]);
        
        // $company->update($request->all());

        return response()->json([
            'message' => "Data updated successfully!",
            'data' => $company,
            'code'=>'200'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Branch  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompanyRequest $request, Company $company)
    {
        $company->delete($request->all());

        return response()->json([
            'message' => "Data deleted successfully!",
            'code'=>'200'
        ], 200);
    }
}
