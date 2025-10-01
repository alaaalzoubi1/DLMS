<?php

namespace App\Http\Controllers;

use App\Models\Specialization_Subscriber;
use App\Http\Requests\StoreSpecialization_SubscriberRequest;
use App\Http\Requests\UpdateSpecialization_SubscriberRequest;
use App\Models\Subscriber;

class SpecializationSubscriberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreSpecialization_SubscriberRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Specialization_Subscriber $specialization_Subscriber)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Specialization_Subscriber $specialization_Subscriber)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSpecialization_SubscriberRequest $request, Specialization_Subscriber $specialization_Subscriber)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Specialization_Subscriber $specialization_Subscriber)
    {
        //
    }


    public function getSubscriberSpecializations()
    {
        $subscriberId = auth('admin')->user()->subscriber_id;
        $specializations = Specialization_Subscriber::with('specialization')
            ->where('subscriber_id', $subscriberId)
            ->get();

        if ($specializations->isEmpty()) {
            return response()->json([
                'message' => 'No specializations found for this subscriber',
            ], 404);
        }

        return response()->json([
            'message' => 'Specializations retrieved successfully',
            'specializations' => $specializations,
        ]);
    }
    public function doctorShow($subscriber_id)
    {
        $subscriber = Subscriber::with('specialization_subscriber.specialization')->findOrFail($subscriber_id);

        // تحقق عبر Policy
        $this->authorize('view', $subscriber);

        $specialization_subscriber = $subscriber->specialization_subscriber;

        return response()->json(['$specialization_subscriber' => $specialization_subscriber], 200);
    }

}
