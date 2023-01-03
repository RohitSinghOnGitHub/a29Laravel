<?php

namespace App\Http\Controllers;

use App\Models\Result_Emerd;
use App\Models\Result_Spare;
use Illuminate\Http\Request;
use App\Http\Requests\StoreResult_EmerdRequest;
use App\Http\Requests\UpdateResult_EmerdRequest;

class ResultEmerdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $period=(int)$request->period;
        $result=Result_Emerd::where('Period', '<', $period)->Select('Period','number','Color')->orderBy('Period','DESC')->paginate(5);
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
     * @param  \App\Http\Requests\StoreResult_EmerdRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreResult_EmerdRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Result_Emerd  $result_Emerd
     * @return \Illuminate\Http\Response
     */
    public function show(Result_Emerd $result_Emerd)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Result_Emerd  $result_Emerd
     * @return \Illuminate\Http\Response
     */
    public function edit(Result_Emerd $result_Emerd)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateResult_EmerdRequest  $request
     * @param  \App\Models\Result_Emerd  $result_Emerd
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateResult_EmerdRequest $request, Result_Emerd $result_Emerd)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Result_Emerd  $result_Emerd
     * @return \Illuminate\Http\Response
     */
    public function destroy(Result_Emerd $result_Emerd)
    {
        //
    }
}
