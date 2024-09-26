<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function registerCompany(Request $request)
    {
        $validatedData = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'confirmed_password' => 'required|same:password',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
        ]);

        $companyCode = Str::slug($validatedData['company_name']) . Str::random(3);
        $trial_start_at = now();
        $trial_end_at = Carbon::now()->addDays(7);

        $subscriber = Subscriber::create([
            'company_name' => $validatedData['company_name'],
            'company_code' => strtoupper($companyCode),
            'trial_start_at' => $trial_start_at,
            'trial_end_at' => $trial_end_at,
        ]);

        $user = User::create([
            'subscriber_id' => $subscriber->id,
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
        ]);

        $user->assignRole('admin');
        $credentials = $request->only('email', 'password');
        $token = auth('admin')->attempt($credentials);
        return response()->json([
            'message' => 'Company registered successfully',
            'company_name' => $validatedData['company_name'],
            'company_code' => strtoupper($subscriber->company_code),
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'trial_start_at' => $trial_start_at,
            'trial_end_at' => $trial_end_at,
            'token' => $token,
        ], 201);
    }
    public function loginAdmin(Request $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            $token = Auth::guard('admin')->attempt($credentials);

            if (!$token) {
                return response()->json(['success' => false, 'error' => 'Invalid credentials'], 401);
            }

            $user = Auth::guard('admin')->user();

            if (!$user->hasRole('admin')) {
                return response()->json(['success' => false, 'error' => 'User does not have admin role'], 403);
            }

            return response()->json(['token' => $token]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Failed to login, please try again.'], 500);
        }
    }


    public function registerTechnical(Request $request)
    {
        $validatedData = $request->validate([
            'company_code' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore(User::where('email', $request->input('email'))->first()),
            ],
            'password' => 'required|string|min:8',
            'confirmed_password' => 'required|same:password',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
        ]);

        $subscriber = Subscriber::where('company_code', strtoupper($validatedData['company_code']))->first();

        if (!$subscriber) {
            throw ValidationException::withMessages([
                'company_code' => ['Company not found. Please check the company code and try again.'],
            ])->status(422);
        }

        $user = User::create([
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'subscriber_id' => $subscriber->id,
        ]);

        $user->assignRole('technical');

        $credentials = ['email' => $validatedData['email'], 'password' => $validatedData['password']];
        $token = Auth::guard('admin')->attempt($credentials);

        return response()->json([
            'message' => 'Technical registered successfully',
            'company_code' => strtoupper($validatedData['company_code']),
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'trial_start_at' => $subscriber->trial_start_at,
            'trial_end_at' => $subscriber->trial_end_at,
            'token' => $token,
        ], 201);
    }
    public function loginTechnical(Request $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            $token = Auth::guard('admin')->attempt($credentials);

            if (!$token) {
                return response()->json(['success' => false, 'error' => 'Invalid credentials'], 401);
            }

            $user = Auth::guard('admin')->user();

            if (!$user->hasRole('technical')) {
                return response()->json(['success' => false, 'error' => 'User does not have admin role'], 403);
            }

            return response()->json(['token' => $token]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Failed to login, please try again.'], 500);
        }
    }
    public function logout(Request $request)
    {
        try {

            Auth::guard('admin')->logout();
            return response()->json(['message' => 'Logged out successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Failed to log out'], 500);
        }
    }

}
