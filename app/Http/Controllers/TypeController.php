<?php

namespace App\Http\Controllers;

use App\Models\Type;
use App\Http\Requests\StoreTypeRequest;
use App\Http\Requests\UpdateTypeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TypeController extends Controller
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
    public function createType(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:futures,new,test,returned',
            'invoiced' => 'required|boolean',
        ]);

        $subscriber_id = Auth::guard('admin')->user()->subscriber_id;

        $existingType = Type::where('subscriber_id', $subscriber_id)
            ->where('type', $validated['type'])
            ->first();

        if ($existingType) {
            return response()->json(['error' => 'This type already exists for the subscriber.'], 422);
        }

        // Create type for subscriber
        $type = Type::create([
            'subscriber_id' => $subscriber_id,
            'type' => $validated['type'],
            'invoiced' => $validated['invoiced'],
        ]);

        return response()->json(['message' => 'Type created successfully.', 'type' => $type], 201);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTypeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function listTypes()
    {
        $subscriber_id = Auth::guard('admin')->user()->subscriber_id;

        $types = Type::where('subscriber_id', $subscriber_id)->get();

        return response()->json(['types' => $types], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function updateType(Request $request, $id)
    {
        $validated = $request->validate([
            'type' => 'sometimes|string|in:futures,new,test,returned',
            'invoiced' => 'sometimes|boolean',
        ]);

        $subscriber_id = Auth::guard('admin')->user()->subscriber_id;
        $type = Type::where('id', $id)->where('subscriber_id', $subscriber_id)->first();

        if (!$type) {
            return response()->json(['error' => 'Type not found or unauthorized.'], 404);
        }

        $type->update($validated);

        return response()->json(['message' => 'Type updated successfully.', 'type' => $type], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTypeRequest $request, Type $type)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Type $type)
    {
        //
    }
}
