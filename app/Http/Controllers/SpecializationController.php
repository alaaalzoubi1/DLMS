<?php

namespace App\Http\Controllers;

use App\Models\Specialization;
use App\Http\Requests\StoreSpecializationRequest;
use App\Http\Requests\UpdateSpecializationRequest;
use App\Models\Specialization_Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpecializationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    public function addSpecialization(Request $request)
    {
        $user = Auth::user();
        $validatedData = $request->validate([
            'name' => 'required|string|max:50',
        ]);
        $specialization = Specialization::where('name',$validatedData['name'])->first();
        if ($specialization == null){
            $specialization = Specialization::create([
                'name' => $validatedData['name']
            ]);
        }
        $exists = Specialization_Subscriber::
            where('specializations_id', $specialization->id)
            ->where('subscriber_id', $user->subscriber_id)
            ->exists();

        if (!$exists) {
            Specialization_Subscriber::create
            ([
                'specializations_id' => $specialization->id,
                'subscriber_id' => $user->subscriber_id,
            ]);

            return response()->json(['message' => 'Specialization added successfully'], 201);
        }

        return response()->json(['message' => 'Specialization already exists for this user'], 409);

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
    public function store(StoreSpecializationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Specialization $specialization)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Specialization $specialization)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSpecializationRequest $request, Specialization $specialization)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Specialization $specialization)
    {
        //
    }
}
