<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    // عرض كل الخطط
    public function index(): JsonResponse
    {
        return response()->json(SubscriptionPlan::all(), 200);
    }

    // إضافة خطة جديدة
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:50',
            'duration_days' => 'required|integer|min:1',
            'price'         => 'required|numeric|min:0',
            'description'   => 'nullable|string',
        ]);

        $plan = SubscriptionPlan::create($validated);

        return response()->json(['message' => 'Plan created successfully', 'plan' => $plan], 201);
    }

    // عرض خطة محددة
    public function show($id): JsonResponse
    {
        $plan = SubscriptionPlan::findOrFail($id);
        return response()->json($plan, 200);
    }

    // حذف خطة
    public function destroy($id): JsonResponse
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $plan->delete();

        return response()->json(['message' => 'Plan deleted successfully'], 200);
    }
}
