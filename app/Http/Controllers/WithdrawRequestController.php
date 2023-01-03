<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Models\CurrentBalance;
use App\Models\WithdrawRequest;
use App\Models\ApprovedWithdraw;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WithdrawRequestController extends Controller
{
    public function index(){
        $Requests=WithdrawRequest::where("Status","Pending")->Select('id','Amount','UPI','Status')->orderBy('created_at','DESC')->paginate(5);
        return $Requests;

    }
    public function addWithRequest(Request $request)
    {
        $user_id=Auth::user()->id;
        $validator=Validator::make($request->all(), [
            'Amount' => 'required',
            'UPI'=> 'required',
            
        ]);
        if($validator->fails()){
            $errors=$validator->errors();
            return response(["errors"=>$errors->first()]);
        }
        if($request->Amount<300){
            return response(["errors"=>"Withdraw Request Must be of 300 Rs. Minimum."]);
        }
        $avail_bal= CurrentBalance::where("user_id",$user_id)->sum("Avail_Balance");
        if($request->Amount >$avail_bal){
            return response(["errors"=>"Insufficient Balance"]);
        }
        $withdraw=new WithdrawRequest();
        $withdraw->UPI=$request->UPI;
        $withdraw->Amount=$request->Amount;
        $withdraw->user_id=$user_id;
        $withdraw->save();
        $avail_bal= CurrentBalance::where("user_id",$user_id)->first();
        $avail_bal->Avail_Balance=$avail_bal->Avail_Balance- $request->Amount;
        $avail_bal->save();
        // return response(["data"=>$request->Amount]);
        return response(["data"=>"Request Uploaded Successfully"]);
    }

    public function approveWithdraw(Request $request){
        $withdrawRequest= WithdrawRequest::where("id",$request->id)->first();
        if($withdrawRequest===null)
         {
            return  response(["Error"=>"No Record Found"]);     
         }
         $withdrawRequest->Status="Approved";
         $withdrawRequest->save();
         $withdrawRequest= WithdrawRequest::where("id",$request->id)->where("Status","Approved")->first();
         $approveWithdraws=new ApprovedWithdraw();
         $approveWithdraws->user_id=$withdrawRequest->user_id;
         $approveWithdraws->UPI=$withdrawRequest->UPI;
         $approveWithdraws->Amount=$withdrawRequest->Amount;
         $approveWithdraws->save();
         return  response(["PendingWithdrawRequests"=>$this->index(),"Message"=>"Request Approved Successfuly"]);

    }
    public function rejectWithdraw(Request $request){
        $withdrawRequest= WithdrawRequest::where("id",$request->id)->first();
        if($withdrawRequest===null)
         {
            return  response(["Error"=>"No Record Found"]);     
         }
         $withdrawRequest->Status="Rejected";
         $withdrawRequest->save();
        //  $withdrawRequest=WithdrawRequest::where("id",$request->id)->first();
        //  $current_balance=CurrentBalance::where("user_id",$withdrawRequest->user_id)->first();
        //  $current_balance->Avail_Balance=$current_balance->Avail_Balance+ $withdrawRequest->Amount;
        //  $current_balance->save();
         return  response(["PendingWithdrawRequests"=>$this->index(),"Message"=>"Request Rejected Successfuly"]);
    }
}
/*
// $Requests = DB::table('bank_cards')
            //             ->join('withdraw_requests', 'bank_cards.id', '=', 'withdraw_requests.card_id')->where('withdraw_requests.Status','Pending')
            //             ->select('bank_cards.Name','bank_cards.Bank_Name','bank_cards.IFSC','bank_cards.UPI','bank_cards.Account_No','withdraw_requests.id',  'withdraw_requests.card_id','withdraw_requests.Amount', 'withdraw_requests.Status')
            //             ->paginate(5);
*/