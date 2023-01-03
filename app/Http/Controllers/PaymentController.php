<?php

namespace App\Http\Controllers;
use Validator;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function payumoney(Request $request){
        
        // return response(["data"=> Auth::user()->id]);
    }
    
}
