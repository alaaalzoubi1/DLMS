<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AccountantController extends Controller
{
    public function registerAccountant(Request $request)
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
            'fcm_token'  => 'nullable|string|max:500',
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
            'FCM_token' => $validatedData['fcm_token']
        ]);

        $user->assignRole('accountant');

        $credentials = ['email' => $validatedData['email'], 'password' => $validatedData['password']];
        $token = Auth::guard('admin')->attempt($credentials);

        return response()->json([
            'message' => 'Accountant registered successfully',
            'company_code' => strtoupper($validatedData['company_code']),
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'trial_start_at' => $subscriber->trial_start_at,
            'trial_end_at' => $subscriber->trial_end_at,
            'company_name' => $subscriber->company_name,
            'token' => $token,
        ], 201);
    }
    public function deleteAccount()
    {
        $account = auth('admin')->user();
        Auth::guard('admin')->logout();
        $account->delete();
        return response()->json([
            'message' => 'تم حذف الحساب بنجاح'
        ]);
    }
    public function accountantProfile(): JsonResponse
    {
        $user = auth('admin')->user();

        return response()->json([
            'delegate' => [
                'id' => $user->id,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'role' => $user->roles->pluck('name')->first(),
                'subscriber' => $user->subscribers ? [
                    'id' => $user->subscribers->id,
                    'name' => $user->subscribers->company_name,
                    'company code' => $user->subscribers->company_code,
                ] : null,
            ],
        ]);
    }
}
