<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\ClinicSubscriber;
use Illuminate\Http\Request;

class ClinicController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'has_special_price' => 'boolean',
            'tax_number' => 'nullable|string|unique:clinics,tax_number',
        ]);

        // Create the clinic
        $clinic = Clinic::create($validated);

        // Attach the clinic to the subscriber if authenticated user has a subscriber

        ClinicSubscriber::create([
            'clinic_id' => $clinic->id,
            'subscriber_id' => auth('admin')->user()->subscriber_id,
        ]);


        return response()->json([
            'message' => 'Clinic created successfully!',
            'clinic' => $clinic,
        ], 201);
    }
    public function show()
    {
        // Get the subscriber_id from the authenticated user
        $subscriberId = auth('admin')->user()->subscriber_id;

        // Ensure the user has a subscriber_id
        if (!$subscriberId) {
            return response()->json([
                'message' => 'No subscriber ID found for the authenticated user.',
            ], 404);
        }

        // Fetch all clinics associated with the subscriber_id
        $clinics = Clinic::with('doctors')->whereHas('subscribers', function ($query) use ($subscriberId) {
            $query->where('subscriber_id', $subscriberId);
        })->get();

        return response()->json([
            'clinics' => $clinics,
        ]);
    }
    public function edit($id, Request $request)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'has_special_price' => 'boolean',
            'tax_number' => 'nullable|string|unique:clinics,tax_number',
        ]);

        $clinic = Clinic::findOrFail($id);

        $clinic->update($validated);

        return response()->json([
            'message' => 'Clinic updated successfully!',
            'clinic' => $clinic,
        ]);
    }
    public function destroy($id)
    {
        // Find the clinic by ID
        $clinic = Clinic::find($id);

        if (!$clinic) {
            return response()->json([
                'message' => 'Clinic not found!',
            ], 404);
        }

        try {
            // Delete the clinic
            $clinic->delete();

            return response()->json([
                'message' => 'Clinic deleted successfully!',
            ], 200);
        } catch (\Exception $e) {
            // Handle errors during deletion
            return response()->json([
                'message' => 'Failed to delete clinic!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


}
