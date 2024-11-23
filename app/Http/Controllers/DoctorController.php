<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;

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
}
