<?php

namespace App\Http\Controllers;

use Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Reward;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\CurrentBalance;
use App\Models\PaymentRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PaymentRequestResource;

class PaymentRequestController extends Controller
{
    public function index()
    {
        $Requests = PaymentRequest::where("status", "Pending")->Select('user_id', 'Name', 'Amount', 'UPI', 'UTR', 'Email_id', 'Status', 'Image_Path')->orderBy('created_at', 'DESC')->paginate(5);
        return $Requests;
    }
    public function changeStatus(Request $request)
    {
        $Requests = PaymentRequest::where('UTR', $request->UTR)->first();
        if ($Requests === null) {
            return  response(["Error" => "No Record Found"]);
        }
        $user = User::where("id", $Requests->user_id)->first();

        if ($Requests->Amount >= 200 && $user->sponcer_id !== null) {
            $reward = new Reward();
            $reward->sponsor_id = $user->sponcer_id;
            $reward->nickname = $user->nickname;
            $reward->Amount = 50.00;
            $reward->save();
        }
        $user_id = $Requests->user_id;
        $Amount = $Requests->Amount;
        $Requests->status = "Approved";
        $Requests->save();

        $payment = new Payment();
        $payment->user_id = $Requests->user_id;
        $payment->Amount = $Requests->Amount;
        $payment->upi = $Requests->upi;
        $payment->name = $Requests->name;
        $payment->Email_id = $Requests->Email_id;
        $payment->UTR = $Requests->UTR;
        $payment->image_path = $Requests->image_path;
        $payment->save();

        //  $balance=CurrentBalance::where('user_id',$user_id)->first();
        //  if($balance===null)
        //  {
        //     $balance=new CurrentBalance();
        //     $balance->user_id=$user_id;
        //     $balance->Avail_Balance=$Amount;
        //     $balance->save();
        //  }
        //  else{

        //     $balance->user_id=$user_id;
        //     $balance->Avail_Balance=$balance->Avail_Balance+$Amount;
        //     $balance->save();

        //  }

        return  response(["PendingRequests" => PaymentRequestResource::collection($this->index()), "Message" => "Request Approved Successfuly"]);
    }
    public function showRequest(Request $request)
    {
        $date = Carbon::now()->subDays($request->days);

        $Requests = PaymentRequest::where('created_at', '>=', $date)->Select('Name', 'Amount', 'UPI', 'UTR', 'Email_id', 'Status', 'Image_Path')->paginate(5);
        return $Requests;
    }
    public function PaymentRequest(Request $request)
    {

        //  return $request->_parts[0][1]['uri']
        // $req=$request;
        //  return $req->_parts;
        //  $req->file('image')->getClientOriginalName();
        // $request->file('image')->getClientOriginalName();
        $validator = Validator::make($request->all(), [
            'Amount' => 'required|min:3',
            'upi' => 'required',
            'name' => 'required',
            'UTR' => 'required|unique:payment_requests',
            'image' => 'required|image',
        ], [
            'Amount.min' => "Amount Must be More 100 Rs.",
            'UTR.unique' => "Request with this UTR has already been uploaded."
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->has("UTR")) {

                return response(["errors" => $errors->first()]);
            }
            return response(["errors" => $errors->first()]);
        }
        $payment = new PaymentRequest;
        if ($request->file()) {
            $file_name = time() . "_" . $request->file('image')->getClientOriginalName();
            $file_path = $request->file('image')->move('public/storage/uploads', $file_name);
            $payment->image_path = $file_path;
            $payment->user_id = Auth::user()->id;
            $payment->Amount = $request->Amount;
            $payment->name = $request->name;
            $payment->upi = $request->upi;
            $payment->Email_id = $request->Email_id;
            $payment->UTR = $request->UTR;
            $payment->save();
        }
        return response(["data" => "Request Uploaded Successfully"]);
    }
}
