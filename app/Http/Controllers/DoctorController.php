<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoctorRegisterRequest;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Doctor_Account;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class DoctorController extends Controller
{
    /**
     * Store a newly created doctor in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'clinic_id' => 'required|exists:clinics,id',
        ]);

        // Create a new doctor
        $doctor = Doctor::create($validatedData);

        return response()->json([
            'message' => 'Doctor created successfully',
            'doctor' => $doctor,
        ], 201);
    }

    /**
     * Display the specified doctor.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Find the doctor by ID
        $doctor = Doctor::find($id);

        // Check if the doctor exists
        if (!$doctor) {
            return response()->json([
                'message' => 'Doctor not found',
            ], 404);
        }

        return response()->json([
            'doctor' => $doctor,
        ], 200);
    }
    public function doctorsByClinic($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return response()->json([
                'message' => 'Invalid ID format',
            ], 400);
        }
        $doctors = Doctor::where('clinic_id',$id)->get();
        return response()->json([
            'doctors' =>$doctors
        ]);
    }
    /**
     * Remove the specified doctor from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Find the doctor by ID
        $doctor = Doctor::find($id);

        // Check if the doctor exists
        if (!$doctor) {
            return response()->json([
                'message' => 'Doctor not found',
            ], 404);
        }

        // Delete the doctor
        $doctor->delete();

        return response()->json([
            'message' => 'Doctor deleted successfully',
        ], 200);
    }


    public function doctorRegister(DoctorRegisterRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $clinic = Clinic::where('clinic_code', $request->clinic_code)->firstOrFail();

            $doctor = Doctor::create([
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'clinic_id'  => $clinic->id,
            ]);

            // 3. إنشاء حساب الدكتور
            $doctorAccount = Doctor_Account::create([
                'doctor_id' => $doctor->id,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
            ]);

            $token = auth('api')->attempt(['email' => $request->email,'password' =>$request->password]);
            DB::commit();
            return response()->json([
                'message' => 'Doctor registered successfully',
                'doctor' => [
                    'id' => $doctor->id,
                    'first_name' => $doctor->first_name,
                    'last_name' => $doctor->last_name,
                    'clinic' => $clinic->name,
                ],
                'account' => [
                    'email' => $doctorAccount->email,
                ],
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            // Rollback transaction in case of failure
            DB::rollBack();
            // Return error response
            return response()->json([
                'message' => 'Registration failed. Please try again later.',
            ], 500);
        }
    }
    public function doctorLogin(Request $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'token' => $token,
            'user' => auth()->user()->doctor,
        ]);
    }
    public function logout(): JsonResponse
    {
        auth('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function doctorProfile(){
        $doctor = auth('api')->user();
        return response()->json([
            'doctor' => [
                'email' => $doctor->email,
                'doctor'=> $doctor->doctor->only(['id','first_name','last_name']),
                'clinic' => $doctor->doctor->clinic->only(['id','name','clinic_code'])
        ]]);
    }

    public function doctorPatients(Request $request): JsonResponse
    {
        $doctor = auth('api')->user()->doctor;

        if (!$doctor) {
            return response()->json(['error' => 'Only doctors can access patients.'], 403);
        }

//        $patients = Order::where('doctor_id', $doctor->id)
//            ->select('patient_id', 'patient_name')
//            ->distinct()
//            ->paginate(15);
        $patients = Order::where('doctor_id',$doctor->id)
            ->groupBy('patient_name')
            ->paginate(15);


        return response()->json($patients, 200);
    }
    public function updateProfile(Request $request)
    {
        $doctorAccount = auth('api')->user();
        $doctor = $doctorAccount->doctor;

        $validated = $request->validate([
            'email' => 'sometimes|email|unique:doctor__accounts,email,' . $doctorAccount->id,
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
        ]);

        if ($request->has('email')) {
            $doctorAccount->email = $request->email;
            $doctorAccount->save();
        }

        if ($request->has('first_name')) {
            $doctor->first_name = $request->first_name;
        }

        if ($request->has('last_name')) {
            $doctor->last_name = $request->last_name;
        }

        $doctor->save();

        return response()->json([
            'doctor' => [
                'email' => $doctorAccount->email,
                'doctor' => [
                    'id' => $doctor->id,
                    'first_name' => $doctor->first_name,
                    'last_name' => $doctor->last_name,
                ],
            ],
        ]);
    }

    public function deleteAccount()
    {
        $doctorAccount = auth('api')->user();
        if ($doctorAccount) {
            if ($doctorAccount->doctor) {
                $doctorAccount->doctor->delete();
            }
            $doctorAccount->delete();
            auth('api')->logout();
            return response()->json([
                'message' => 'Account deleted successfully'
            ]);
        }
        return response()->json([
            'message' => 'No authenticated user found'
        ], 404);
    }


}
