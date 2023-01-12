<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Reward;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\ApprovedWithdraw;
use App\Models\MybettingRecords;
use Illuminate\Support\Facades\Auth;


class TotalBalanceController extends Controller
{
    public function calcTotalAmount()
    {
        $user_id = Auth::user()->id;
        $total_reward = 0;
        $nickname = User::where("id", $user_id)->first()->nickname;
        $total_bet_amount = MybettingRecords::where("user_id", $user_id)->sum("Amount");
        $total_win_amount = MybettingRecords::where("user_id", $user_id)->where("Status", "Success")->sum("win_amount");
        $total_withdraw_amount = ApprovedWithdraw::where("user_id", $user_id)->sum("Amount");
        $total_recharge = Payment::where("user_id", $user_id)->sum("Amount");
        if ($nickname !== null) {
            $total_reward = Reward::where("sponsor_id", $nickname)->sum("Amount");
        }
        $netAvail_balance = ($total_recharge + $total_reward + $total_win_amount) - ($total_withdraw_amount + $total_bet_amount);
        return response(["Avail_Balance" => $netAvail_balance, "win" => $total_win_amount, "reward" => $total_reward, "recharge" => $total_recharge, "withdraw" => $total_withdraw_amount, "bet" => $total_bet_amount]);
    }
    public function earningCalculation($from = null, $to = null)
    {
        $Amount = MybettingRecords::whereBetween("Created_Time", [$from, $to])->sum("Amount");
        $All_records = MybettingRecords::Select("Period", "Amount", "Contract_Money", "Delivery", "Fee", "Select", "Status", "category", "win_amount")->whereBetween("Created_Time", [$from, $to])->orderBy('Period', 'DESC')->paginate(10);
        $win_amount = MybettingRecords::whereBetween("Created_Time", [$from, $to])->sum("win_amount");
        $Total_Income = $Amount - $win_amount;


        return  [
            "Balance" => ["Amount" => $Amount, "Winning_Amount" => $win_amount, "Total_Income" => $Total_Income, "From" => $from, "To" => $to],
            "Records" => $All_records
        ];
    }
}
