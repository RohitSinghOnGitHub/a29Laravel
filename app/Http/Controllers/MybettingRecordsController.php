<?php

namespace App\Http\Controllers;

use App\Events\BetCreated;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\CurrentBalance;
use Illuminate\Support\Carbon;
use App\Models\MybettingRecords;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreMybettingRecordsRequest;
use App\Http\Requests\UpdateMybettingRecordsRequest;
use App\Http\Resources\HistoryCollection;

class MybettingRecordsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function calcPeriod()
    {
        $object = new MybettingRecords();
        $period = $object->calcPeriod();
        return $period;
    }
    public function index()
    {
        //
    }


    public function usersBet(Request $request)
    {
        $object = new MybettingRecords();
        $period = $object->calcPeriod();
        $users_bet = MybettingRecords::where('Period', '<', $period)->orderBy('Period', 'desc')->get();
        return response(["History" =>  HistoryCollection::collection($users_bet)]);
    }




    protected function calcVilot($period, $category)
    {
        $cat = $category;
        $sumOnlyVilot = MybettingRecords::where('Period', '=', $period)->where('category', $category)->where('Select', 'Vilot')->sum('Delivery') * 4.5;
        $sumOnlyRed = MybettingRecords::where('Period', '=', $period)->where('category', $category)->where('Select', 'Red')->sum('Delivery') * 1.5;
        $sumOnlyGreen = MybettingRecords::where('Period', '=', $period)->where('category', $category)->where('Select', 'Green')->sum('Delivery') * 1.5;
        $numArray = [];

        $numArray = array_map(function ($num) use ($period, $category) {
            //    dd($category);
            return MybettingRecords::where('Period', '=', $period)->where('category', $category)->where('Select', $num)->sum('Delivery') * 9;
        }, ['0', '5']);

        $vilotKeys = ['Vilot+0', 'Vilot+5'];
        $vilotPno = [$sumOnlyVilot + $numArray[0] + $sumOnlyRed, $sumOnlyVilot + $numArray[1] + $sumOnlyGreen];
        $VilotReultValues = array_combine($vilotKeys, $vilotPno);

        return $VilotReultValues;
    }
    protected function smallestResult($category, $period)
    {
        $sum;
        $sumRed;
        $sumGreen;
        $sumVilot;
        $bet = new MybettingRecords();
        $check = $bet->select('Period')->where('period', '=', $period)->where('category', '=', $category)->count();

        if (($check == 0)) {
            return response(["Message" => "Nohting To show! No bet Yet"]);
            // throw new ModelNotFoundException("Error Processing Request");

            //   return abort(404);
        }

        for ($num = 0; $num <= 9; $num++) {
            $sum[$num] = MybettingRecords::where('Period', '=', $period)->where('category', $category)->where('Select', '=', (string) $num)->sum('Delivery');
        }

        $sum = array_filter($sum, function ($var) {
            return ($var > 0);
        });

        $sumRed = MybettingRecords::where('Period', '=', $period)->where('category', $category)->where('Select', '=', 'Red')->sum('Delivery');
        $sumGreen = MybettingRecords::where('Period', '=', $period)->where('category', $category)->where('Select', '=', 'Green')->sum('Delivery');
        $sumVilot = MybettingRecords::where('Period', '=', $period)->where('category', $category)->where('Select', '=', 'Vilot')->sum('Delivery');
        $total = MybettingRecords::where('Period', '=', $period)->where('category', $category)->sum('Delivery');

        $Colors = ["Red" => $sumRed, "Green" => $sumGreen, "Vilot" => $sumVilot];
        $Colors = array_filter($Colors, function ($var) {
            return ($var > 0);
        });
        $combine = array_merge($sum, $Colors);
        if (!empty($sum) && !empty($Colors)) {
            $smallNum = min($sum);
            $smallestResult = min($smallNum, min($Colors));
        } else if (empty($sum)) {
            $smallestResult = min($Colors);
        } else if (empty($Colors)) {
            $smallestResult = min($sum);
        }

        $keys = !empty($sum) ? array_keys($sum, min($sum)) : [];


        return [
            "smallest result" => $smallestResult,
            "Period" => $period,
            "Array" => (array) $sum,
            "Keys" => ["Numbers" => !empty($sum) ? (array) array_keys($sum, min($sum)) : [], "Colors" => !(empty($Colors)) ? (array) array_keys($Colors, min($Colors)) : ""],
            "Color" => $Colors,
            "Total" => $total,
            "CalcRed" => $bet->calcColor($period, 'Red', $category),
            "CalcGreen" => $bet->calcColor($period, 'Green', $category),
            "CalcVilot" => $this->calcVilot($period, $category),

            "Combine Minimum Result" => min(array_merge($bet->calcColor($period, 'Red', $category), $bet->calcColor($period, 'Green', $category), $this->calcVilot($period, $category))),
            "minimumValuesKeys" => (array) array_filter(array_merge($bet->calcColor($period, 'Red', $category), $bet->calcColor($period, 'Green', $category), $this->calcVilot($period, $category)), function ($val) use ($period, $category) {
                $bet = new MybettingRecords();
                return ($val === min(array_merge($bet->calcColor($period, 'Red', $category), $bet->calcColor($period, 'Green', $category), $this->calcVilot($period, $category))));
            })
        ];
    }
    public function store(StoreMybettingRecordsRequest $request)
    {
        // Carbon::parse(now('Asia/kolkata')->toDateTimeString())->format('YmdHi')
        $object = new MybettingRecords();
        $period = $object->calcPeriod();
        if ($period != $request->Period) {
            return response(["Message" => "Your Watch is Behind"]);
        }


        try {
            $user_id = Auth::user()->id;
            $balance = CurrentBalance::where("user_id", $user_id)->first();
            if ($balance->Avail_Balance < $request->Amount || $balance->Avail_Balance === 0 || $balance === null) {
                return response(["Error" => "Insufficient Balance"]);
            }
            $balance->Avail_Balance = $balance->Avail_Balance - $request->Amount;
            $balance->update();
            // dd($balance);
            $bet = new MybettingRecords();
            $bet->Period = $request->Period;
            $bet->User_id = Auth::user()->id;
            $bet->Contract_Money = $request->Contract_Money;
            $bet->Contract_Count = $request->Contract_Count;
            $bet->Delivery = $request->Delivery;
            $bet->Fee = $request->Fee;
            $bet->Open_Price = $request->Open_Price;
            $bet->Result = $request->Result;
            $bet->Select = $request->Select;
            $bet->Status = $request->Status;
            $bet->Amount = $request->Amount;
            $bet->Category = $request->Category;
            $bet->save();

            event(new BetCreated($bet->Period, $bet->Category));
            $response = [
                "data" => [
                    "Message" => "Success"
                ],
                // "small Result"=>$this->smallestResult($bet->Period)
            ];
            return response($response, 201);
        } catch (\Exception $e) {
            $response = [
                "data" => [
                    "Message" => "Un aunthicated" . $e . getMessage()
                ],
                // "small Result"=>$this->smallestResult($bet->Period)
            ];
            return response($response, 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MybettingRecords  $mybettingRecords
     * @return \Illuminate\Http\Response
     */
    public function show(MybettingRecords $mybettingRecords)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MybettingRecords  $mybettingRecords
     * @return \Illuminate\Http\Response
     */
    public function edit(MybettingRecords $mybettingRecords)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMybettingRecordsRequest  $request
     * @param  \App\Models\MybettingRecords  $mybettingRecords
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMybettingRecordsRequest $request, MybettingRecords $mybettingRecords)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MybettingRecords  $mybettingRecords
     * @return \Illuminate\Http\Response
     */
    public function destroy(MybettingRecords $mybettingRecords)
    {
        //
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MybettingRecords  $mybettingRecords
     */
}
