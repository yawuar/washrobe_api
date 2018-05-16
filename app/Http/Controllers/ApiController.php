<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Validator;

use App\User; 

class ApiController extends Controller
{
    public $successStatus = 200;

    public function login(){ 

        $validator = Validator::make($request->all(), [ 
            'email' => 'required|email', 
            'password' => 'required', 
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('washrobe')->accessToken; 
            return response()->json(['success' => $success], $this-> successStatus)->withHeaders([
                'Access-Control-Allow-Origin', '*',
                'Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS'
            ]); 
        } 
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401)->withHeaders([
                'Access-Control-Allow-Origin', '*',
                'Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS'
            ]); 
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
        return response()->json(['success' => $user], $this->successStatus)->withHeaders([
            'Access-Control-Allow-Origin', '*',
            'Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS'
        ]); 
    } 
}
