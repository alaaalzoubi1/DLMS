<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterCompanyRequest;
use App\Http\Requests\UpdateAdminProfileRequest;
use App\Http\Requests\UpdateTechnicalProfileRequest;
use App\Jobs\SendFirebaseNotificationJob;
use App\Models\Subscriber;
use App\Models\Subscriber_Doctor;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
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


    public function registerCompany(RegisterCompanyRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $companyCode = Str::slug($data['company_name']) . Str::random(3);

            $subscriber = Subscriber::create([
                'company_name' => $data['company_name'],
                'company_code' => strtoupper($companyCode),
                'trial_start_at' => now(),
                'trial_end_at' => now()->addDays(14),
                'tax_number' => $data['tax_number'] ?? null,
                'commercial_registration' => $data['commercial_registration'] ?? null,
                'country_code' => $data['country_code'],
            ]);

            $subscriber->address()->create([
                'street' => $data['street'] ?? null,
                'building_number' => $data['building_number'] ?? null,
                'additional_number' => $data['additional_number'] ?? null,
                'district' => $data['district'],
                'city' => $data['city'],
                'postal_code' => $data['postal_code'] ?? null,
                'locationAddress' => $data['locationAddress'] ?? null,
            ]);

            $user = User::create([
                'subscriber_id' => $subscriber->id,
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'FCM_token' => $data['fcm_token'] ?? null,
            ]);

            $user->assignRole('admin');

            $token = auth('admin')->attempt([
                'email' => $data['email'],
                'password' => $data['password']
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Company registered successfully',
                'token' => $token,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 422);
        }
    }


    public function login(Request $request): JsonResponse
    {
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
        $user = auth('admin')
            ->user()
            ->load(['roles', 'subscribers.address']);


        return response()->json([
            'admin' => [
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
                    'country_code' => $user->subscribers->country_code,
                    'commercial_registration' => $user->subscribers->commercial_registration,
                    'tax_number' => $user->subscribers->tax_number,
                    'trial_start_at' => $user->subscribers->trial_start_at,
                    'trial_end_at' => $user->subscribers->trial_end_at,
                    'address' => $user->subscribers->address
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
            $subscriberData = $request->input('subscriber', []);
            $admin->subscribers->update($subscriberData);

        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'admin' => $admin->load('roles', 'subscribers')
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
