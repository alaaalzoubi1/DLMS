<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClinicRequest;
use App\Models\Clinic;
use App\Models\ClinicProduct;
use App\Models\ClinicSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ClinicController extends Controller
{
    public function store(StoreClinicRequest $request)
    {
        try {
            $clinic = DB::transaction(function () use ($request) {

                $validated = $request->validated();

                $clinic = Clinic::create([
                    'name' => $validated['name'],
                    'tax_number' => $validated['tax_number'] ?? null,
                    'commercial_registration' => $validated['commercial_registration'] ?? null,
                    'clinic_code' => Str::uuid(),
                ]);

                ClinicSubscriber::create([
                    'clinic_id' => $clinic->id,
                    'subscriber_id' => auth('admin')->user()->subscriber_id,
                ]);

                $clinic->address()->create([
                    'street' => $validated['street'] ?? null,
                    'building_number' => $validated['building_number'] ?? null,
                    'additional_number' => $validated['additional_number'] ?? null,
                    'district' => $validated['district'] ?? null,
                    'city' => $validated['city'] ?? null,
                    'postal_code' => $validated['postal_code'] ?? null,
                    'locationAddress' => $validated['locationAddress'] ?? null,
                ]);

                return $clinic;
            });

            return response()->json([
                'message' => 'Clinic created successfully!',
                'clinic' => $clinic->load('address'),
            ], 201);

        } catch (\Throwable $e) {

            Log::error('Clinic creation failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Something went wrong while creating clinic.'
            ], 500);
        }
    }

    public function show(Request $request)
    {
        $admin = auth('admin')->user();

        $clinics = $admin->subscribers
            ->clinics()
            ->with(['doctors', 'address'])
            ->get();

        return response()->json([
            'clinics' => $clinics
        ]);
    }

    public function edit($id, Request $request)
    {
        $admin = auth('admin')->user();

        $clinic = $admin->subscribers
            ->clinics()
            ->where('clinics.id', $id)
            ->first();

        if (!$clinic) {
            return response()->json([
                'message' => 'Clinic not found'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'has_special_price' => 'sometimes|boolean',
            'tax_number' => "sometimes|string|unique:clinics,tax_number,{$clinic->id}",
            'commercial_registration' => "sometimes|string|unique:clinics,commercial_registration,{$clinic->id}",
        ]);

        $clinic->update($validated);

        return response()->json([
            'message' => 'Clinic updated successfully!',
            'clinic' => $clinic,
        ]);
    }

    public function destroy($id)
    {
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
            'clinic_ids' => 'required|array|min:1',
            'clinic_ids.*' => 'required|exists:clinics,id',
            'product_id' => 'required|exists:products,id',
            'price' => 'required|numeric|min:0',
        ]);

        $clinicProducts = [];
        foreach ($validatedData['clinic_ids'] as $clinicId) {
            // Use updateOrCreate to handle clinic-product pricing
            $clinicProduct = ClinicProduct::updateOrCreate(
                [
                    'clinic_id' => $clinicId,
                    'product_id' => $validatedData['product_id'],
                ],
                [
                    'price' => $validatedData['price'],
                ]
            );

            $clinicProducts[] = $clinicProduct;

            // Update the clinic's has_special_price flag if not already true
            Clinic::where('id', $clinicId)
                ->where('has_special_price', false)
                ->update(['has_special_price' => true]);
        }

        return response()->json([
            'message' => 'Special prices added successfully',
            'clinic_products' => $clinicProducts,
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

    public function clinics_with_special_price()
    {
        $subscriberId = auth('admin')->user()->subscriber_id;
        $clinics = Clinic::with('doctors')->whereHas('subscribers', function ($query) use ($subscriberId) {
            $query->where('subscriber_id', $subscriberId);
        })->where('has_special_price',true)->
        get();
        return response()->json([
            'clinics' => $clinics
        ]);
    }

    public function updateAddress($clinicId, Request $request)
    {
        $admin = auth('admin')->user();

        $clinic = $admin->subscribers
            ->clinics()
            ->with('address')
            ->where('clinics.id', $clinicId)
            ->first();

        if (!$clinic) {
            return response()->json([
                'message' => 'Clinic not found'
            ], 404);
        }

        $validated = $request->validate([
            'street' => 'sometimes|string|max:255',
            'building_number' => 'sometimes|string|max:50',
            'additional_number' => 'sometimes|string|max:50',
            'district' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255',
            'postal_code' => 'sometimes|string|max:20',
            'locationAddress' => 'sometimes|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($clinic, $validated) {

                if ($clinic->address) {
                    $clinic->address->update($validated);
                } else {
                    $clinic->address()->create($validated);
                }
            });

            return response()->json([
                'message' => 'Address updated successfully',
                'address' => $clinic->fresh()->address
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update address',
                'error' => $e->getMessage()
            ], 500);
        }
    }



}
