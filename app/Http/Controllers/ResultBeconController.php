<?php

namespace App\Http\Controllers;

use App\Models\Result_Becon;
use App\Models\Result_Spare;
use Illuminate\Http\Request;
use App\Http\Requests\StoreResult_BeconRequest;
use App\Http\Requests\UpdateResult_BeconRequest;

class ResultBeconController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $period=(int)$request->period;
        $result=Result_Becon::where('Period', '<', $period)->Select('Period','number','Color')->orderBy('Period','DESC')->paginate(5);
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
     * @param  \App\Http\Requests\StoreResult_BeconRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreResult_BeconRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Result_Becon  $result_Becon
     * @return \Illuminate\Http\Response
     */
    public function show(Result_Becon $result_Becon)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Result_Becon  $result_Becon
     * @return \Illuminate\Http\Response
     */
    public function edit(Result_Becon $result_Becon)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateResult_BeconRequest  $request
     * @param  \App\Models\Result_Becon  $result_Becon
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateResult_BeconRequest $request, Result_Becon $result_Becon)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Result_Becon  $result_Becon
     * @return \Illuminate\Http\Response
     */
    public function destroy(Result_Becon $result_Becon)
    {
        //
    }
}
