<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Reward;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\CurrentBalance;
use App\Models\WithdrawRequest;
use App\Models\ApprovedWithdraw;
use App\Models\MybettingRecords;
use Illuminate\Support\Facades\Auth;

class CurrentBalanceController extends Controller
{
    public function index(Request $request)
    {
        // dd($request->Period);
        $user_id = Auth::user()->id;
        $Period = $request->Period;
        $total_reward = 0;
        $nickname = User::where("id", $user_id)->first()->nickname;
        // dd($nickname);
        $total_bet_amount = MybettingRecords::where("user_id", $user_id)->sum("Amount");
        $total_win_amount = MybettingRecords::where("user_id", $user_id)->where("Period", "<>", $Period)->where("Status", "Success")->sum("win_amount");
        $total_withdraw_amount = WithdrawRequest::where("user_id", $user_id)->whereIn("Status", ["Pending", "Approved"])->sum("Amount");
        $total_recharge = Payment::where("user_id", $user_id)->sum("Amount");
        if ($nickname !== null) {
            $total_reward = Reward::where("sponsor_id", $nickname)->sum("Amount");
        }
        $netAvail_balance = ($total_recharge + $total_reward + $total_win_amount) - ($total_withdraw_amount + $total_bet_amount);
        $current_balance = CurrentBalance::where("user_id", $user_id)->first();
        if ($current_balance === null) {
            $current_balance = new CurrentBalance();
            $current_balance->user_id = $user_id;
            $current_balance->Avail_Balance = $netAvail_balance;
            $current_balance->save();
        } else {
            $current_balance->Avail_Balance = $netAvail_balance;
            $current_balance->save();
        }

        return response(["Avail_Balance" => $netAvail_balance]);
    }
}
