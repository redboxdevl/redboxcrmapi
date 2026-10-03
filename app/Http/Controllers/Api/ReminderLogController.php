<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssignReminder;


use Carbon\Carbon;
use DB;
use Validator;


use Illuminate\Http\Request;

class ReminderLogController extends Controller
{
    
    
    public function index(Request $request)
    {
        
        $cusId = $request->cusId;
        $orderBy = $request->orderBy ?? "id";
        $orderType = $request->orderType ?? "desc";
        $perPage = $request->perPage ?? "10";
        $page = $request->page ?? "1";

        if (!empty($cusId)) {
            
              $response = AssignReminder::with(['CustomerLeadsTbl','getleads_home' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['leadid'=>$cusId])->get();
            
            // SUCCESS
            if (count($response) > 0) {
                $data = $this->renderList($response);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Reminder Log List Found',
                    'data' => $data,
                  
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
        "clientName" => $value->getleads_home == '' || $value->getleads_home == null ? '' : $value->getleads_home->clientName,
        "clientPhone" => $value->getleads_home == '' || $value->getleads_home == null ? '' : $value->getleads_home->clientPhone,
        "reminderSet" =>date("Y-m-d",strtotime($value->reminderSet)),
        "priority"=>$value->priority,
        "reminderinfo"=>$value->reminderinfo,
    ]);
}

return $data;
}


}
