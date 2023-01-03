<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\BankCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\BankCardResource;

class BankCardController extends Controller
{
    public function index(){
        $user_id=Auth::user()->id;
        $bank_card=BankCard::where("user_id",$user_id)->get();
        return response(["data"=>BankCardResource::collection($bank_card)]);
        
    }
    public function store(Request $request){
        $validator=Validator::make($request->all(), [
            'Name' => 'required',
            'IFSC' => 'required',
            'Bank_Name' => 'required',
            'Account_No' => 'required',
            'State' => 'required',
            'City' => 'required',
            'Address' => 'required',
            'UPI' => 'required',
        ]);
        if($validator->fails()){
            $errors=$validator->errors();
            return response(["errors"=>$errors->first()]);
        }
        $card =  New BankCard();
        $card ->user_id=Auth::user()->id;
        $card ->Name=$request->Name;
        $card ->IFSC=$request->IFSC;
        $card ->Bank_Name =$request->Bank_Name;
        $card ->Account_No =$request->Account_No;
        $card ->State =$request->State;
        $card ->City =$request->City;
        $card ->Address =$request->Address;
        $card ->UPI =$request->UPI;
        $card->save();
        return response(["data"=>"Detail Added Successfully"]);
    }
}
