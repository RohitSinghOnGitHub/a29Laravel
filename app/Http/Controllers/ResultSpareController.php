<?php

namespace App\Http\Controllers;

use App\Models\Result_Spare;
use Illuminate\Http\Request;
use App\Http\Requests\StoreResult_SpareRequest;
use App\Http\Requests\UpdateResult_SpareRequest;

class ResultSpareController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $period=(int)$request->period;
        $result=Result_Spare::where('Period', '<', $period)->Select('Period','number','Color')->orderBy('Period','DESC')->paginate(5);
        return response($result,200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreResult_SpareRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreResult_SpareRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Result_Spare  $result_Spare
     * @return \Illuminate\Http\Response
     */
    public function show(Result_Spare $result_Spare)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Result_Spare  $result_Spare
     * @return \Illuminate\Http\Response
     */
    public function edit(Result_Spare $result_Spare)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateResult_SpareRequest  $request
     * @param  \App\Models\Result_Spare  $result_Spare
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateResult_SpareRequest $request, Result_Spare $result_Spare)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Result_Spare  $result_Spare
     * @return \Illuminate\Http\Response
     */
    public function destroy(Result_Spare $result_Spare)
    {
        //
    }
}
