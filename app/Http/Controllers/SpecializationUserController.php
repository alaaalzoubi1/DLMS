<?php

namespace App\Http\Controllers;

use App\Models\Specialization_User;
use App\Http\Requests\StoreSpecialization_UserRequest;
use App\Http\Requests\UpdateSpecialization_UserRequest;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class SpecializationUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }


    public function addUserSpecialization(Request $request)
    {
        $validatedData = $request->validate([
            'subscriber_specializations_id' => 'required|exists:specialization__subscribers,id',
        ]);
        $user_id = auth('admin')->user()->id;
        // Attempt to find or create the specialization-user association
        $specializationUser = Specialization_User::firstOrCreate([
            'user_id' => $user_id,
            'subscriber_specializations_id' => $validatedData['subscriber_specializations_id'],
        ]);

        $message = $specializationUser->wasRecentlyCreated
            ? 'Specialization added successfully'
            : 'User already has this specialization';

        return response()->json([
            'message' => $message,
            'specialization_user' => $specializationUser,
        ]);
    }


    public function getUserSpecializations()
    {
        // Retrieve all specializations for the given user
        $userId = auth('admin')->id();
        $userSpecializations = Specialization_User::with(['specializationSubscriber.specialization'])
            ->where('user_id', $userId)
            ->get();

        // Check if the user has any specializations
        if ($userSpecializations->isEmpty()) {
            return response()->json([
                'message' => 'No specializations found for this user',
            ], 404);
        }

        // Format the response
        $formattedSpecializations = $userSpecializations->map(function ($specializationUser) {
            return [
                'id' => $specializationUser->id,
                'specialization_name' => $specializationUser->specializationSubscriber->specialization->name,
            ];
        });

        return response()->json([
            'message' => 'Specializations retrieved successfully',
            'specializations' => $formattedSpecializations,
        ]);
    }
    public function deleteUserSpecialization( $specializationId)
    {
        $userId = auth('admin')->id();
        // Find the user's specialization record
        $specializationUser = Specialization_User::find($specializationId);

        if (!$specializationUser) {
            return response()->json([
                'message' => 'Specialization not found for the user',
            ], 404);
        }

        // Delete the record
        $specializationUser->delete();

        return response()->json([
            'message' => 'Specialization deleted successfully',
        ]);
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
    public function store(StoreSpecialization_UserRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Specialization_User $specialization_User)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Specialization_User $specialization_User)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSpecialization_UserRequest $request, Specialization_User $specialization_User)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Specialization_User $specialization_User)
    {
        //
    }
}
