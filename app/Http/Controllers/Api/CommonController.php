<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommonController extends Controller
{
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
