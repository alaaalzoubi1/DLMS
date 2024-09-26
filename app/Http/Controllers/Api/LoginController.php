<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use App\Models\User;

class LoginController extends Controller
{
//    public function __construct()
//    {
//        $this->middleware('auth:api');
//    }

    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'token' => $token,
            'user' => auth()->user(),
        ]);
    }
//    public function loginUser(Request $request)
//    {
//        $credentials = $request->only('email', 'password');
//        try {
//            $token = auth('api')->attempt($credentials);
//            if (!$token) {
//                return response()->json(['success' => false, 'error' => 'Some Error Message'], 401);
//            }
//        } catch (JWTException $e) {
//            return response()->json(['success' => false, 'error' => 'Failed to login, please try again.'], 500);
//        }
//        return $this->finalResponse($token);
//    }
//
//
//    public function registerUser(Request $request)
//    {
//        $credentials = $request->only('email', 'password');
//        $request->merge(['password' => Hash::make($request->password)]);
//        $username = explode('@', $request->email)[0];
//        $user = User::create([
//            'name' => $username,
//            'username' => $username,
//            'email' => $request->email,
//            'password' => $request->password,
//        ]);
//        return response()->json('success' . ' ' . $user->name . '  ' . $user->email . ' ');
//    }
//    public function registerAdmin(Request $request)
//    {
//        $credentials = $request->only('email', 'password');
//        $request->merge(['password' => Hash::make($request->password)]);
//        $username = explode('@', $request->email)[0];
//        $user = Admin::create([
//            'name' => $username,
//            'username' => $username,
//            'email' => $request->email,
//            'password' => $request->password,
//        ]);
//        return response()->json('success');
//    }
//
//
//
//    public function loginAdmin(Request $request)
//    {
//        $credentials = $request->only('email', 'password');
//        try {
//            if (!$token = auth()->guard('admin')->attempt($credentials)) {
//                return response()->json(['success' => false, 'error' => 'Some Error Message'], 401);
//            }
//        } catch (JWTException $e) {
//            return response()->json(['success' => false, 'error' => 'Failed to login, please try again.'], 500);
//        }
//        return $this->finalResponse($token);
//    }
}
