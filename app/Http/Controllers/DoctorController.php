<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDoctorRequest;
use App\Models\Doctor;
use App\Models\Doctor_Account;
use App\Models\Subscriber;
use App\Models\Subscriber_Doctor;
use App\Models\User;
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
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    public function registerDoctor(Request $request)
    {
//        try {
            $validatedData = $request->validate([
                'company_code' => 'required|string|max:255',
                'email' => 'required|string|email:rfc,dns|max:255|unique:doctor__accounts',
                'password' => 'required|string|min:8',
                'confirmed_password' => 'required|same:password',
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'clinic_name' => 'required|string|max:50',
            ]);
            $subscriber = Subscriber::where('company_code', $validatedData['company_code'])->first();
            if (!$subscriber) {
                throw ValidationException::withMessages([
                    'company_code' => ['Company not found. Please check the company code and try again.'],
                ])->status(422);
            }
            $doctor = Doctor::create([
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'clinic_name' => $validatedData['clinic_name'],
            ]);
            $doctor_account = Doctor_Account::create([
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'doctor_id' => $doctor->id,
                'FCM_token' => $validatedData['FCM_token'] ?? null,

            ]);
            Subscriber_Doctor::create([
                'subscriber_id' => $subscriber->id,
                'doctor_id' => $doctor->id,
            ]);
            $credentials = ['email' => $validatedData['email'], 'password' => $validatedData['password']];
            $token = Auth::guard('api')->attempt($credentials);
            return response()->json(['message' => 'Doctor registered successfully',
                'token' => $token], 201);

//        } catch (\Exception $e) {
//            return response()->json(['error' => 'An error occurred while registering the doctor'], 500);
//        }
    }


    public function loginDoctor(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            $token = Auth::guard('api')->attempt($credentials);

            if (!$token) {
                return response()->json(['success' => false, 'error' => 'Invalid credentials'], 401);
            }

            // Retrieve doctor information
            $doctor = Doctor_Account::with('doctor')->where('email', $request->email)->first();

            if (!$doctor) {
                return response()->json(['success' => false, 'error' => 'Doctor not found'], 404);
            }


            $response = [
                'token' => $token,
                'doctor' => [
                    'first_name' => $doctor->doctor->first_name,
                    'last_name' => $doctor->doctor->last_name,
                    'clinic_name' => $doctor->doctor->clinic_name,
                ]
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Failed to login, please try again.'], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            Auth::guard('api')->logout();
            return response()->json(['message' => 'Logged out successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Failed to log out'], 500);
        }
    }


    public function getDoctors(Request $request)
    {
        $user = Auth::user();
        $subscriberDoctors = Subscriber_Doctor::with('doctor.account')
            ->where('subscriber_id','=',$user->subscriber_id)->get();
        return response()->json($subscriberDoctors);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDoctorRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Doctor $doctor)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Doctor $doctor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDoctorRequest $request, Doctor $doctor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor)
    {
        //
    }

}
