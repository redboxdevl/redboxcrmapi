<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTestRequest;
use App\Models\Test;
use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // // WITHOUT PAGINATION
        // $tests = Test::all();
        // return response()->json([
        //     'data' => $tests
        // ]);

        // WITH PAGINATION
        // $tests = Test::paginate(request()->all());
        $tests = Test::paginate(20);
        return response()->json($tests);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    public function store(StoreTestRequest $request)
    {
        $test = Test::create($request->all());

        return response()->json([
            'message' => "Test saved successfully!",
            'data' => $test
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Test  $test
     * @return \Illuminate\Http\Response
     */
    public function show(Test $test)
    {
        $test = Test::findOrFail($test->id);

        return response()->json([
            'message' => "Test data retrieved successfully!",
            'data' => $test
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Test  $test
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, Test $test)
    public function update(StoreTestRequest $request, Test $test)
    {
        $test->update($request->all());

        return response()->json([
            'message' => "Test updated successfully!",
            'data' => $test
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Test  $test
     * @return \Illuminate\Http\Response
     */
    public function destroy(Test $test)
    {
        $test->delete();

        return response()->json([
            'message' => "Test deleted successfully!",
        ], 200);
    }
}
