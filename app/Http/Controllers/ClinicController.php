<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\ClinicProduct;
use App\Models\ClinicSubscriber;
use App\Models\Product;
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

        $clinic = Clinic::find($id);
        if (!$clinic) {
            return response()->json([
                'message' => 'Clinic not found'
            ],404);
        }

        if ($request->exists('name'))
            $clinic->name = $validated['name'];
        if ($request->exists('has_special_price'))
            $clinic->has_special_price = $validated['has_special_price'];
        if ($request->exists('tax_number')) {
            $clinic->tax_number = $validated['tax_number'];
        }

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
    public function addSpecialPrice(Request $request)
    {
        $validatedData = $request->validate([
            'clinic_id' => 'required|exists:clinics,id',
            'product_id' => 'required|exists:products,id',
            'price' => 'required|numeric|min:0',
        ]);

        // Use updateOrCreate to handle clinic-product pricing
        $clinicProduct = ClinicProduct::updateOrCreate(
            [
                'clinic_id' => $validatedData['clinic_id'],
                'product_id' => $validatedData['product_id'],
            ],
            [
                'price' => $validatedData['price'],
            ]
        );

        // Update the clinic's has_special_price flag if not already true
        Clinic::where('id', $validatedData['clinic_id'])
            ->where('has_special_price', false)
            ->update(['has_special_price' => true]);

        return response()->json([
            'message' => 'Special price added successfully',
            'clinic_product' => $clinicProduct,
        ]);
    }
    public function deleteSpecialPrice(Request $request)
    {
        $validatedData = $request->validate([
            'clinic_id' => 'required',
            'product_id' => 'required',
        ]);
        if (!is_numeric($validatedData['clinic_id']) || $validatedData['clinic_id'] <= 0) {
            return response()->json([
                'message' => 'Invalid clinic_id format',
            ], 400);
        }
        if (!is_numeric($validatedData['product_id']) || $validatedData['product_id'] <= 0) {
            return response()->json([
                'message' => 'Invalid product_id format',
            ], 400);
        }
        $deleted = ClinicProduct::where('clinic_id', $validatedData['clinic_id'])
            ->where('product_id', $validatedData['product_id'])
            ->delete();
        $remainingSpecialPrices = ClinicProduct::where('clinic_id', $validatedData['clinic_id'])->exists();

        if (!$remainingSpecialPrices) {
            Clinic::where('id', $validatedData['clinic_id'])->update(['has_special_price' => false]);
        }

        // Check if the record was deleted
        if ($deleted) {
            return response()->json([
                'message' => 'Special price deleted successfully',
            ]);
        }

        return response()->json([
            'message' => 'No special price found for the specified clinic and product',
        ], 404);
    }

    public function clinics_with_special_price($subscriberId)
    {
        $clinics = Clinic::with('doctors')->whereHas('subscribers', function ($query) use ($subscriberId) {
            $query->where('subscriber_id', $subscriberId);
        })->where('has_special_price',true)->
        get();
        return response()->json([
            'clinics' => $clinics
        ]);
    }





}
