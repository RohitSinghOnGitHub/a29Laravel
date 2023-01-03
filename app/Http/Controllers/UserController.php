<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\User;
use App\Models\Addupi;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Responce;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{

    protected function isValidUser(User $user)
    {
        if (Auth::user()->id == $user->id) {
            return  true;
        } else {
            return  false;
        }
    }
    public function getOtp(Request $request)
    {

        $username = 'TECHCARTEL';
        $password = 'Tech@2022';
        $otp = rand(1000, 9990);
        $contacts = $request->contact;
        $sender_id = 'SKRAPP';
        $peid = '1301163063714722854';
        $api_url = "http://india.jaipurbulksms.com/api/mt/SendSMS?user=" . $username . "&password=" . $password . "&senderid=" . $sender_id . "&channel=trans&DCS=0&flashsms=0&number=" . $contacts . "&text=Hi%20,%20your%20login%20OTP%20on%20TECHCARTEL(SE)%20is%20" . $otp . "&route=3&peid=" . $peid;
        $response = file_get_contents($api_url);
        return response(["otp" => strval($otp)]);
    }
    public function changeUpi(Request $request)
    {
        if ($this->isAdmin()) {
            $validator = Validator::make($request->all(), [
                "UPI" => "required",
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return response(["errors" => $errors->first()]);
            }
            $add_upi = Addupi::first();
            if ($add_upi === null) {
                $add_upi = new Addupi();
                $add_upi->UPI = $request->UPI;
            } else {
                $add_upi->UPI = $request->UPI;
            }
            $add_upi->save();
            return response(["Message" => "UPI Added Successfully", "UPI" => $add_upi->UPI]);
        }
    }
    public function fetchUpi()
    {
        $upi = Addupi::first();
        // dd($upi->UPI);
        if ($upi === null) {
            return "nothing";
        }
        return response(["UPI" => $upi->UPI]);
    }
    protected function isAdmin()
    {
        if (Auth::user()->is_Admin == 1) {
            return  true;
        } else {
            return  false;
        }
    }
    protected function okResponse(User $user)
    {
        $response = [
            "data" => ['user' => new UserResource($user)],
        ];
        return response($response, 201);
    }

    protected function unauthorisedMessage()
    {
        $response =

            ["data" => ['Message' => "You are Not Allowed to see Other Users"],];
        return response($response, 401);
    }
    public function index() //Shows all users
    {
        return  $this->isAdmin() ? UserResource::collection(User::all()) : $this->unauthorisedMessage();
    }


    public function store(Request $request) //Saves/ registers new user
    {
        $validator = Validator::make($request->all(), [
            "mobile" => "required|unique:users|min:10|max:10",
            "password" => "required|max:20|min:8",
            "Email" => "required|unique:users"
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response(["errors" => $errors->first()]);
        }
        $user = new User;
        $user->name = "Person one";
        $user->mobile = $request->mobile;
        $user->email = $request->Email;
        $user->nickname = Str::random(10);
        $user->sponcer_id = $request->sponcer_id;
        $user->password = bcrypt($request->password);
        $user->save();
        $token = $user->createToken('myapptoken')->plainTextToken;
        $response = [
            'user' => new UserResource($user),
            'token' => $token
        ];
        return response($response);
    }

    public function show(User $user) //Shows a single user
    {
        if ($this->isValidUser($user) || $this->isAdmin()) {
            return $this->okResponse($user);
        } else {
            return $this->unauthorisedMessage();
        }
    }


    public function update(Request $request, User $user) //Updates user
    {
        if ($this->isValidUser($user)) {
            $user->update($request->all());
            $this->show($user);
        } else {
            return $this->unauthorisedMessage();
        }
    }
    public function forgetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "Email" => "required",

            ], [
                'Email.required' => "Email Field is Required.",

            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response(["errors" => $errors->first()]);
            }
            $user = User::where("email", $request->Email)->first();
            if ($user === null) {
                return  response(["errors" => "No Record Found"]);
            } else {
                $forgetPassword_token = Str::random(80);
                $url = "https://a29.in/#/resetPassword/" . $forgetPassword_token;
                // $url = "http://localhost:3000/#/resetPassword/" . $forgetPassword_token;
                $data['url'] = $url;
                $data['email'] = $request->Email;
                $data['title'] = "Reset Password";
                $data['body'] = "Please Click the below link to reset your Password.";
                Mail::send('forgetPassword', ["data" => $data], function ($message) use ($data) {
                    $message->to($data['email'])->subject($data['title']);
                });
                $Password_reset = PasswordReset::where("email", $request->Email)->first();
                if ($Password_reset === null) {
                    $Password_reset = new PasswordReset;
                    $Password_reset->email = $request->Email;
                    $Password_reset->token = $forgetPassword_token;
                } else {
                    $Password_reset->token = $forgetPassword_token;
                }

                $Password_reset->save();

                return response(["Message" => "Please Check Your Registered Email Account."]);
            }
        } catch (\Exception $e) {
            return response(["errors" => $e->getMessage()]);
        }
    }

    public function UpdatePassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "password" => "required|max:20|min:8",
            "temp_token" => "required"
        ], [
            "temp_token.required" => "Varification Code is required"
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response(["errors" => $errors->first()]);
        }
        $Password_token = PasswordReset::where("token", $request->temp_token)->first();
        if ($Password_token === null) {
            return  response(["errors" => "UnAuthenticated User"]);
        }
        $user = User::where("email", $Password_token->email)->first();
        if ($user === null) {
            return  response(["errors" => "No Record Found"]);
        }
        $user->password = bcrypt($request->password);
        $user->save();
        $response = ["Message" => "Password Changed Successfully"];
        return response($response);
    }

    public function destroy(User $user) // Delete user
    {
        if ($this->isValidUser($user) || $this->isAdmin()) {
            $user->delete();
            $response = ["data" => [
                "user" => $this->show($user),
                "Message" => "User Deleted Successfully"
            ]];
            return response($response, 201);
        } else {
            return $this->unauthorisedMessage();
        }
    }
    public function login(Request $request) // Login user
    {
        $user = User::where('mobile', $request->mobile)->first();
        if ($user === null) {
            return  response(["errors" => "No Record Found"]);
        }
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response(['errors' => "Credentials are not Matching"]);
        }
        $token = $user->createToken('myapptoken')->plainTextToken;
        if ($user->is_Admin === 1) {
            $response = ['user' => new UserResource($user), 'token' => $token, "HSN" => "516332"];
            return response($response);
        }
        $response = ['user' => new UserResource($user), 'token' => $token];
        return response($response);
    }

    public function logout() //Sign out user
    {
        Auth::user()->tokens()->delete();
        $response = ['Message' => "Log Out Successfully"];
        return response($response);
    }
}
