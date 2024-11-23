<?php

namespace App\Http\Controllers;

use App\Models\Specialization;
use App\Models\Specialization_Subscriber;
use Illuminate\Http\Request;
use function Symfony\Component\String\s;

class SpecializationController extends Controller
{
    /**
     * Store a newly created specialization or link existing specialization with subscriber.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Find or create the specialization
        $specialization = Specialization::firstOrCreate(
            ['name' => $validatedData['name']]
        );

        // Link specialization with subscriber
        Specialization_Subscriber::firstOrCreate([
            'specializations_id' => $specialization->id,
            'subscriber_id' => auth('admin')->user()->subscriber_id,
        ]);

        return response()->json([
            'message' => 'Specialization linked successfully',
            'specialization' => $specialization,
        ], 201);
    }
    public function getSpecializationsBySubscriber()
    { // Validate subscriber_id
        $subscriber_id = auth('admin')->user()->subscriber_id;
//        $specializations = Specialization_Subscriber::where('subscriber_id', $subscriber_id)
//            ->join('specializations', 'specialization_subscribers.specializations_id', '=', 'specializations.id')
//            ->get();
        $specializations = Specialization_Subscriber::with('specialization')
            ->where('subscriber_id','=',$subscriber_id)
            ->get();
        return response()->json([ 'specializations' => $specializations, ], 200);
    }
    public function delete($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return response()->json([
                'message' => 'Invalid ID format',
            ], 400);
        }
        $specialization = Specialization_Subscriber::find($id);

        // Check if the doctor exists
        if (!$specialization) {
            return response()->json([
                'message' => 'Specialization not found',
            ], 404);
        }

        // Delete the doctor
        $specialization->delete();
        return response()->json([
            'message' => 'Specialization deleted successfully',
        ], 200);
    }
}
