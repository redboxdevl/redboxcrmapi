<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Leads;
use App\Models\ListAssignLeads;
use Illuminate\Http\Request;

class LeadsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $apiResponse = "";

        switch ($request->type) {
                // ASSIGNED LEADS
            case 'assign':
                $apiResponse = $this->getAssignedLeads($request);
                break;

                // MY LEADS
            case 'my':
                $apiResponse = $this->getMyLeads($request);
                break;

                // HOT LEADS
            case 'hot':
                $apiResponse = $this->getHotLeads($request);
                break;

                // TRANSFER LEADS
            case 'transfer':
                $apiResponse = $this->getTransferLeads($request);
                break;

                // VIRGIN LEADS
            case 'virgin':
                $apiResponse = $this->getVirginLeads($request);
                break;

                // REMINDER LEADS
            case 'reminder':
                $apiResponse = $this->getReminderLeads($request);
                break;

                // ACTIVITY LEADS
            case 'activity':
                $apiResponse = $this->getActivityLeads($request);
                break;

                // NOT INTERESTED LEADS
            case 'notinterested':
                $apiResponse = $this->getNotInterestedLeads($request);
                break;

            default:
                break;
        }

        return $apiResponse;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Leads  $leads
     * @return \Illuminate\Http\Response
     */
    public function show(Leads $leads)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Leads  $leads
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Leads $leads)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Leads  $leads
     * @return \Illuminate\Http\Response
     */
    public function destroy(Leads $leads)
    {
        //
    }

    // ############################################################################################################
    // ############################################ GET LIST OF LEADS #############################################
    // ############################################################################################################

    // ASSIGNED LEADS
    public function getAssignedLeads(Request $request)
    {
        $orderBy = $request->orderBy ?? "id";
        $orderType = $request->orderType ?? "desc";
        $perPage = $request->perPage ?? "20";
        $page = $request->page ?? "1";

        $agentId = $request->agentId;

        if (!is_null($agentId)) {

            $apiResponse = ListAssignLeads::with(['getleads', 'getreminder' => function ($query) {
                $query->orderBy('id', 'desc')->get();
            }])
                ->where([
                    'agentid' => $agentId,
                    'shiftStatus' => '0',
                ])
                ->orderBy($orderBy, $orderType)
                ->paginate($perPage);


            // SUCCESS
            if (count($apiResponse) > 0) {
                $data = $this->renderList($apiResponse);

                return response()->json([
                    'code' => '200',
                    'success' => true,
                    'message' => 'Record Found',
                    'data' => $data,
                    'orderBy' => $orderBy,
                    'orderType' => $orderType,
                    'perPage' => $perPage,
                    'pageNo' => $page,
                    // 'totalRecords' => $listAssignCount,
                    // 'TotalPerPage' => $TOtalPerPage,
                ]);
            }

            // FAIL
            else {
                return $this->getEmptyListResponse();
            }
        }

        // AGENT ID NOT SELECTED
        else {
            return $this->getExceptionResponse("Agent Id is required");
        }
    }

    // MY LEADS
    public function getMyLeads(Request $request)
    {
        $orderBy = $request->orderBy ?? "id";
        $orderType = $request->orderType ?? "desc";
        $perPage = $request->perPage ?? "20";
        $pageNo = $request->pageNo ?? "1";
    }

    // HOT LEADS
    public function getHotLeads(Request $request)
    {
        $orderBy = $request->orderBy ?? "id";
        $orderType = $request->orderType ?? "desc";
        $perPage = $request->perPage ?? "20";
        $pageNo = $request->pageNo ?? "1";
    }

    // TRANSFER LEADS
    public function getTransferLeads(Request $request)
    {
        $orderBy = $request->orderBy ?? "id";
        $orderType = $request->orderType ?? "desc";
        $perPage = $request->perPage ?? "20";
        $pageNo = $request->pageNo ?? "1";
    }

    // VIRGIN LEADS
    public function getVirginLeads(Request $request)
    {
        $orderBy = $request->orderBy ?? "id";
        $orderType = $request->orderType ?? "desc";
        $perPage = $request->perPage ?? "20";
        $pageNo = $request->pageNo ?? "1";
    }

    // REMINDER LEADS
    public function getReminderLeads(Request $request)
    {
        $orderBy = $request->orderBy ?? "id";
        $orderType = $request->orderType ?? "desc";
        $perPage = $request->perPage ?? "20";
        $pageNo = $request->pageNo ?? "1";
    }

    // ACTIVITY LEADS
    public function getActivityLeads(Request $request)
    {
        $orderBy = $request->orderBy ?? "id";
        $orderType = $request->orderType ?? "desc";
        $perPage = $request->perPage ?? "20";
        $pageNo = $request->pageNo ?? "1";
    }

    // NOT INTERESTED LEADS
    public function getNotInterestedLeads(Request $request)
    {
        $orderBy = $request->orderBy ?? "id";
        $orderType = $request->orderType ?? "desc";
        $perPage = $request->perPage ?? "20";
        $pageNo = $request->pageNo ?? "1";
    }

    // PREPARE LIST
    public function renderList(Object $apiResponse)
    {
        $data = [];

        foreach ($apiResponse as $key => $value) {

            array_push($data, (object) [
                "id" => $value->id,
                "agentId" => $value->agentid,
                "shiftStatus" => $value->shiftStatus,
                "publishAgentDate" => $value->publishAgentDate,
                "createdAt" => $value->created_at,
                "updatedAt" => $value->updated_at,
                "getReminder" => $value->getreminder ?? false,
                "leadDetails" => [
                    "id" => $value->getleads->id,
                    "clientName" => $value->getleads->clientName,
                    "clientPhone" => $value->getleads->clientPhone,
                    "clientEmail" => $value->getleads->clientemail,
                    "clientProject" => $value->getleads->clientProject,
                    "clientDesignation" => $value->getleads->clientDesignation,
                    "nature" => $value->getleads->nature,
                    "leadStatus" => $value->getleads->leadStatus,
                    "leadType" => $value->getleads->leadType,
                    "createdAt" => $value->getleads->created_at,
                    "updatedAt" => $value->getleads->updated_at,
                ],
            ]);
        }

        return $data;
    }

    public function getEmptyListResponse()
    {
        return response()->json([
            'code' => '403',
            'success' => false,
            'message' => 'Record Not Found',
            'data' => [],
        ]);
    }

    public function getExceptionResponse($message)
    {
        return response()->json([
            'code' => '403',
            'success' => false,
            'message' => $message,
            'data' => [],
        ]);
    }
}
