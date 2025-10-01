<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use App\Models\ToothColor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ToothColorController extends Controller
{
    /**
     * Add a new tooth color.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Request $request)
    {
        // Validate the request data
        $user = Auth::guard('admin')->user();

        $validatedData = $request->validate([
            'color' => 'required|string|max:255',
        ]);
        $toothColor = ToothColor::where('subscriber_id',$user->subscriber_id)
            ->where('color', $validatedData['color'])
            ->first();

        if ($toothColor) {
            // If the tooth color exists and is_deleted is true, update it
            if ($toothColor->is_deleted) {
                $toothColor->is_deleted = false;
                $toothColor->save();
                $message = 'Tooth color restored successfully';
            } else {
                $message = 'Tooth color already exists';
            }
        } else {
            // Create the tooth color if it doesn't exist
            $toothColor = ToothColor::create([
                'subscriber_id' => $user->subscriber_id,
                'color' => $validatedData['color'],
            ]);
            $message = 'Tooth color added successfully';
        }

        return response()->json([
            'message' => $message,
            'tooth_color' => $toothColor,
        ], 201);
    }
    public function delete($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return response()->json([
                'message' => 'Invalid ID format',
            ], 400);
        }
        // Find the tooth color by ID
        $toothColor = ToothColor::find($id);

        // Check if the tooth color exists
        if (!$toothColor) {
            return response()->json([
                'message' => 'Tooth color not found',
            ], 404);
        }

        // Mark the tooth color as deleted
        $toothColor->is_deleted = true;
        $toothColor->save();

        return response()->json([
            'message' => 'Tooth color deleted successfully',
        ], 200);
    }
    public function show()
    {
        $subscriber_id = Auth::guard('admin')->user()->subscriber_id;

        // Get all non-deleted tooth colors for the subscriber
        $toothColors = ToothColor::where('subscriber_id', $subscriber_id)
            ->where('is_deleted', false)
            ->get();

        return response()->json([
            'tooth_colors' => $toothColors,
        ], 200);
    }
    public function doctorShow($subscriber_id)
    {
        $subscriber = Subscriber::findOrFail($subscriber_id);

        // تحقق عبر Policy
        $this->authorize('view', $subscriber);

        $toothcolors = $subscriber->toothcolors()->NotDeleted()->get();

        return response()->json(['tooth-colors' => $toothcolors], 200);
    }
}
