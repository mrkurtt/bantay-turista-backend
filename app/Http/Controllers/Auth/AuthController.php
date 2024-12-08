<?php

namespace App\Http\Controllers\Auth;

use App\Models\Admin;
use App\Models\Establishment;
use App\Models\Tourist;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    private function generateUniqueQRCode()
    {
        $date = Carbon::now();
        $check = false;

        while (!$check){
            $qr_code = 'BT' . $date->day . Str::random(4) . $date->minute . $date->second;
            $findQR = Tourist::where('qr_code', $qr_code)->first();
            
            if(!$findQR){
                $check = true;
            }
        }
        
        return $qr_code;
    }

    public function createUser(Request $request)
    {
        try {
            //Validated
            if ($request->user_type == 1 || $request->user_type == 3) {
                $request['name'] = $request->first_name . ' ' . $request->last_name;
            }

            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required|min:6'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation Error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $request['user_id'] = $user->id;

            $id_data = ["user_id" => $user->id ];

            if ($user->id) {

                $creds = $request->except(['email', 'password', 'user_type']);

                if ($request->user_type == 1) {
                    
                    $creds['qr_code'] = $this->generateuniqueQRCode();                   
                    $newTourist = Tourist::create($creds);
                    $id_data['tourist_id'] = $newTourist->id;

                } 

                if ($request->user_type == 2) {

                    $newEstablishment =  Establishment::create($creds);
                    $id_data['establishment_id'] = $newEstablishment->id;

                }

                if ($request->user_type == 3) {

                    $newAdmin = Admin::create($creds);
                    $id_data['admin_id'] = $newAdmin->id;
                }
            }

            return response()->json([
                'status' => true,
                'id' => $id_data,
                'message' => 'User Created Successfully',
                // 'token' => $user->createToken("API TOKEN")->plainTextToken,
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function loginUser(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'user_type'=>'required',
                    'email' => 'required|email',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();
            
            $id_data = ['userId' => $user->id];
            $role = '';

            if($request->user_type == 1){
                $tourist = Tourist::where('user_id', $user->id)->first();
                $id_data['touristId'] = $tourist->id;
                $role = 'tourist';
            }
            
            else if($request->user_type == 2){
                $establishment = Establishment::where('user_id', $user->id)->first();
                $id_data['establishmentId'] = $establishment->id;
                $role = 'establishment';
            }         
            else if($request->user_type == 3){
                $role = 'admin';
            }         

            return response()->json([
                'status' => true,
                'id' => $id_data,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken,
                'role' => $role
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json([
            'message' => 'Successfully logged out',
        ]);

    }

    public function refresh()
    {
        return response()->json([
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}