<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Validator;

use App\User; 

class ApiController extends Controller
{
    public $successStatus = 200;

    public function login(Request $request){ 

        $validator = Validator::make($request->all(), [ 
            'email' => 'required|email', 
            'password' => 'required', 
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        // check if user email exists - NICE TO HAVE

        if(Auth::attempt(['email' => $request['email'], 'password' => $request['password']])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('washrobe')->accessToken; 
            return response()->json(['success' => $success], $this-> successStatus);
        } 
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401);
        } 
    }

    public function register(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
            'firstname' => 'required', 
            'lastname' => 'required', 
            'email' => 'required|email', 
            'gender' => 'required',
            'password' => 'required', 
            'c_password' => 'required|same:password', 
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $input = $request->all(); 
        $input['password'] = bcrypt($input['password']); 
        $user = User::create($input); 
        $success['token'] =  $user->createToken('washrobe')->accessToken; 
        $success['firstname'] =  $user->firstname;

        return response()->json(['success'=>$success], $this->successStatus); 
    }

    public function getUser() 
    { 
        $user = Auth::user(); 
        return response()->json(['success' => $user], $this->successStatus);
    }

    public function logout(Request $request)
    { 
        $revoke = $request->user()->token()->revoke();
        return response()->json(['data' => $revoke]);
    }

    public function getUserByEmail(Request $request) {
        $isUser = false;
        $user = User::where('email', $request->all()['email'])->get();
        if(count($user) > 0) {
            $isUser = true;
        }

        return response()->json(['isValidUser' => $isUser]);
    }
}
