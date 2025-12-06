<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAdminProfileRequest;
use App\Http\Requests\UpdateTechnicalProfileRequest;
use App\Jobs\SendFirebaseNotificationJob;
use App\Models\Subscriber;
use App\Models\Subscriber_Doctor;
use App\Models\User;
use Illuminate\Http\JsonResponse;
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
            'tax_number' => 'nullable|string|max:20|unique:subscribers',
            'fcm_token'  => 'nullable|string|max:500',
        ]);

        $companyCode = Str::slug($validatedData['company_name']) . Str::random(3);
        $trial_start_at = now();
        $trial_end_at = Carbon::now()->addDays(14);

        $subscriber = Subscriber::create([
            'company_name' => $validatedData['company_name'],
            'company_code' => strtoupper($companyCode),
            'trial_start_at' => $trial_start_at,
            'trial_end_at' => $trial_end_at,
            'tax_number' => $validatedData['tax_number'],
        ]);

        $user = User::create([
            'subscriber_id' => $subscriber->id,
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'FCM_token' => $validatedData['fcm_token']
        ]);

        $user->assignRole('admin');
        $credentials = $request->only('email', 'password');
        $token = auth('admin')->attempt($credentials);
        return response()->json([
            'message' => 'Company registered successfully',
            'token' => $token,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        // Validation
        $request->validate([
            'email'      => 'required|email',
            'password'   => 'required|string',
            'fcm_token'  => 'nullable|string|max:500',
        ]);

        try {
            $credentials = $request->only('email', 'password');

            $token = Auth::guard('admin')->attempt($credentials);

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'error'   => 'Invalid credentials'
                ], 401);
            }

            $admin = Auth::guard('admin')->user();

            if ($request->filled('fcm_token')) {
                $admin->update(['FCM_token' => $request->fcm_token]);
            }

            return response()->json([
                'success' => true,
                'token'   => $token,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => 'Failed to login, please try again.'
            ], 500);
        }
    }

    public function adminInfo()
    {
        $admin = auth()->user();
        $company = Subscriber::where('id',$admin->subscriber_id)->first();
        return response()->json([
            'admin' => $admin,
            'company' => $company
        ]);
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
            'company_name' => $subscriber->company_name,
            'token' => $token,
        ], 201);
    }
    public function loginTechnical(Request $request): JsonResponse
    {
        // Validation
        $request->validate([
            'email'      => 'required|email',
            'password'   => 'required|string',
            'fcm_token'  => 'nullable|string|max:500',
        ]);

        try {
            $credentials = $request->only('email', 'password');

            $token = Auth::guard('admin')->attempt($credentials);

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'error'   => 'Invalid credentials'
                ], 401);
            }

            $user = Auth::guard('admin')->user();

            if (!$user->hasRole('technical')) {
                return response()->json([
                    'success' => false,
                    'error'   => 'User does not have technical role'
                ], 403);
            }

            // Update FCM token if provided
            if ($request->filled('fcm_token')) {
                $user->update(['FCM_token' => $request->fcm_token]);
            }

            return response()->json([
                'company_name' => $user->subscribers->company_name,
                'token'        => $token,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => 'Failed to login, please try again.',
            ], 500);
        }
    }

    public function technicalProfile(): JsonResponse
    {
        $user = auth('admin')->user();

        return response()->json([
            'technical' => [
                'id' => $user->id,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'is_available' => $user->is_available,
                'role' => $user->roles->pluck('name')->first(),
                'subscriber' => $user->subscribers ? [
                    'id' => $user->subscribers->id,
                    'name' => $user->subscribers->company_name,
                    'company code' => $user->subscribers->company_code,
                ] : null,
            ],
        ]);
    }
    public function adminProfile(): JsonResponse
    {
        $user = auth('admin')->user();

        return response()->json([
            'admin' => [
                'id' => $user->id,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'is_available' => $user->is_available,
                'role' => $user->roles->pluck('name')->first(), // اسم الدور فقط
                'subscriber' => $user->subscribers ? [
                    'id' => $user->subscribers->id,
                    'name' => $user->subscribers->company_name,
                    'company code' => $user->subscribers->company_code,
                ] : null,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            Auth::guard('admin')->logout();
            return response()->json(['message' => 'Logged out successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Failed to log out'], 500);
        }
    }
    public function getTechnical(Request $request)
    {
        $user = Auth::user();
        $technicals = User::with('specializations')->whereHas('roles', function ($query) use ($user) {
            $query->where('name', 'technical')
                ->where('subscriber_id', $user->subscriber_id);
        })->get();

        return response()->json($technicals);
    }

    public function setAvailability($userId)
    {
        if (!is_numeric($userId) || $userId <= 0) {
            return response()->json([
                'message' => 'Invalid ID format',
            ], 400);
        }

        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $user->is_available = !$user->is_available;
        $user->save();
        if ($user->is_available){
            $title = "قام مدير المعمل بتغيير حالة توفرك";
            $body = "أنت الآن جاهز لاستقبال الطلبات";
        }
        else{
            $title = "قام مدير المعمل بتغيير حالة توفرك";
            $body = "لا يمكنك استقبال الطلبات الآن";
        }
        $token = $user->FCM_token;
        if ($token)
            SendFirebaseNotificationJob::dispatch($token, $title, $body);

        return response()->json([
            'message' => 'User availability updated successfully',
            'is_available' => $user->is_available,
        ]);
    }
    public function getAvailability(): JsonResponse
    {
        $user = auth('admin')->user();

        return response()->json([
            'id' => $user->id,
            'is_available' => $user->is_available,
        ]);
    }
    public function toggleAvailability(): JsonResponse
    {
        $user = auth()->user();

        $user->is_available = !$user->is_available;
        $user->save();

        if ($user->is_available) {
            $title = "الفني {$user->first_name} {$user->last_name} متوفر";
            $body = "يمكن لـ {$user->first_name} استقبال الطلبات الآن";
        } else {
            $title = "الفني {$user->first_name} {$user->last_name} غير متوفر";
            $body = "لا يمكن لـ {$user->first_name} استقبال الطلبات الآن";
        }

        $admin = User::where('subscriber_id', $user->subscriber_id)
            ->whereHas('roles', fn($q) => $q->where('name', 'admin'))
            ->first();

        $token = $admin->FCM_token ?? null;

        if ($token) {
            SendFirebaseNotificationJob::dispatch($token, $title, $body);
        }

        return response()->json([
            'message' => 'Availability updated successfully',
            'id' => $user->id,
            'is_available' => $user->is_available,
        ]);
    }


    public function technicalUpdateProfile(UpdateTechnicalProfileRequest $request): JsonResponse
    {
        $user = auth('admin')->user();

        $data = $request->only([
            'first_name',
            'last_name',
            'email',
        ]);

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'technical' => [
                'id' => $user->id,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'is_available' => $user->is_available,
                'role' => $user->roles->pluck('name')->first(),
                'subscriber' => $user->subscribers ? [
                    'id' => $user->subscribers->id,
                    'name' => $user->subscribers->name,
                ] : null,
            ],
        ]);
    }


    public function adminUpdateProfile(UpdateAdminProfileRequest $request): JsonResponse
    {
        $admin = auth('admin')->user();

        $admin->update($request->only([
            'first_name',
            'last_name',
            'email',
        ]));

        if ($request->has('subscriber') && $admin->subscribers) {
            $admin->subscribers->update([
                'name' => $request->input('subscriber.name', $admin->subscribers->name),
                'company_code' => $request->input('subscriber.company_code', $admin->subscribers->company_code),
            ]);
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'admin' => [
                'id' => $admin->id,
                'email' => $admin->email,
                'first_name' => $admin->first_name,
                'last_name' => $admin->last_name,
                'is_available' => $admin->is_available,
                'role' => $admin->roles->pluck('name')->first(),
                'subscriber' => $admin->subscribers ? [
                    'id' => $admin->subscribers->id,
                    'name' => $admin->subscribers->name,
                    'company code' => $admin->subscribers->company_code,
                ] : null,
            ],
        ]);
    }

    public function deleteAccount()
    {
        $account = auth('admin')->user();

        if ($account->hasRole('admin')) {
            $account->subscribers->delete();
        }
        $account->delete();
        auth('admin')->logout();
        return response()->json([
            'message' => 'Account deleted successfully'
        ]);
    }



}
