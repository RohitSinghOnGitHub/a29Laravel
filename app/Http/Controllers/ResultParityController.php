<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Result_Parity;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreResult_ParityRequest;
use App\Http\Requests\UpdateResult_ParityRequest;

class ResultParityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $period=(int)$request->period;
        // dd($request->period);
        // $result=DB::table('result__parities')->where('Period','<=',$period-1)->selectRaw('Period, number, Color')->orderByDesc('Period')->get();
        // dd($result);
        $result=Result_Parity::where('Period', '<', $period)->Select('Period','number','Color')->orderBy('Period','DESC')->paginate(5);
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
     * @param  \App\Http\Requests\StoreResult_ParityRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreResult_ParityRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Result_Parity  $result_Parity
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // dd($request->period);
        $result=Result_Parity::where('Period',$request->period)->first();
            if($result===null){
                return response(["Error"=>"No Record Found"],200); 
            }
        return response(["Period"=>$result->Period,"Price"=>"Price","number"=>$result->number,"Color"=>$result->Color],200);
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Result_Parity  $result_Parity
     * @return \Illuminate\Http\Response
     */
    public function edit(Result_Parity $result_Parity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateResult_ParityRequest  $request
     * @param  \App\Models\Result_Parity  $result_Parity
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateResult_ParityRequest $request, Result_Parity $result_Parity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Result_Parity  $result_Parity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Result_Parity $result_Parity)
    {
        //
    }
}
