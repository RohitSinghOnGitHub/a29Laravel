<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Result_Parity;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreResult_ParityRequest;
use App\Http\Requests\UpdateResult_ParityRequest;
use App\Listeners\NotifyAdmin;
use App\Models\MybettingRecords;
use App\Models\Result_Becon;
use App\Models\Result_Emerd;
use App\Models\Result_Spare;
use Illuminate\Support\Facades\Auth;

class ResultParityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $object = new MybettingRecords();

        $period = (int) $object->calcPeriod();
        // dd($request->period);
        // $result=DB::table('result__parities')->where('Period','<=',$period-1)->selectRaw('Period, number, Color')->orderByDesc('Period')->get();
        // dd($result);
        $result = Result_Parity::where('Period', '<', $period)->Select('Period', 'number', 'Color')->orderBy('Period', 'DESC')->paginate(5);
        return response($result, 200);
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
        $object = new MybettingRecords();
        $period = (int) $object->calcPeriod();
        $result = Result_Parity::where('Period', $period)->first();
        if ($result === null) {
            return response(["Error" => "No Record Found"], 200);
        }
        return response(["Period" => $result->Period, "Price" => "Price", "number" => $result->number, "Color" => $result->Color], 200);
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

    /**
     * Change The Result When its Admin
     *
     * @param  \App\Models\Result_Parity  $result_Parity
     * @return \Illuminate\Http\Response
     */
    public function manualResult(Request $request)
    {
        $object = new MybettingRecords();
        $period = $object->calcPeriod();
        $is_admin = Auth::user()->is_Admin;
        $modelNameArray = ['Parity' => new Result_Parity(), 'Spare' => new Result_Spare(), 'Becon' => new Result_Becon(), 'Emerd' => new Result_Emerd()];
        if ($is_admin === 1) {
            $notify = new NotifyAdmin();
            $result = $modelNameArray[$request->category]->where('Period', '=', $period)->first();
            if (!is_null($result)) {
                $result->Color = $request->color;
                $result->number = $request->number;
                $result->is_Fixed = 1;
                $result->update();
            } else {
                $result = $modelNameArray[$request->category];
                $result->Period = $period;
                $result->Color = $request->color;
                $result->number = $request->number;
                $result->is_Fixed = 1;
                $result->save();
            }
            $notify->ManualcalcWin($period, $request->category);
        } else {
            return response(["Message" => "Unauthenticated"]);
        }
        return response(["Message" => "Success"]);
    }
}
