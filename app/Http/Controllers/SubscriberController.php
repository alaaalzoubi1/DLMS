<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use App\Http\Requests\StoreSubscriberRequest;
use App\Http\Requests\UpdateSubscriberRequest;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SubscriberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function check_company_code(Request $request)
    {
        $companyExists = Subscriber::where('company_code', $request->company_code)->select('company_name')->first();
        if ($companyExists==null){
            return response()->json([
                'message' => 'company not exist'
            ]);
        }
        return response()->json(
             $companyExists
        );
    }
    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $subscriber = Subscriber::with(['categories.products', 'specializations'])
            ->findOrFail($id);

        return response()->json([
            'data' => $subscriber
        ]);
    }
    public function subscribeToPlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        try {
            DB::beginTransaction();

            $plan = SubscriptionPlan::findOrFail($request->plan_id);

            $subscriber = auth('admin')->user()->subscribers;
            $baseDate = $subscriber->trial_end_at && $subscriber->trial_end_at->isFuture()
                ? $subscriber->trial_end_at
                : now();

            $subscriber->trial_end_at = $baseDate->copy()->addDays($plan->duration_days);
            $subscriber->save();

            $cacheKey = "subscriber_active:{$subscriber->id}";
            Cache::forget($cacheKey);
            Cache::put($cacheKey, true, now()->addDay());

            DB::commit();

            return response()->json([
                'message' => "Subscription updated successfully.",
                'subscriber' => $subscriber,
                'new_end_date' => $subscriber->trial_end_at->toDateTimeString(),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to subscribe to plan',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    public function cancelSubscription()
    {
        try {
            DB::beginTransaction();
            $subscriber = auth('admin')->user()->subscribers;
            // إنهاء الاشتراك فوراً
            $subscriber->trial_end_at = now();
            $subscriber->save();

            // تحديث الكاش
            $cacheKey = "subscriber_active:{$subscriber->id}";
            Cache::forget($cacheKey);
            Cache::put($cacheKey, false, now()->addDay());

            DB::commit();

            return response()->json([
                'message' => 'Subscription canceled successfully.',
                'new_end_date' => $subscriber->trial_end_at->toDateTimeString(),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to cancel subscription',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    public function remainingDays()
    {
        $subscriber = auth('admin')->user()->subscribers;
        $now = now();
        $end = $subscriber->trial_end_at;

        $daysLeft = $now->diffInDays($end, false);

        return response()->json([
            'days_left'     => max($daysLeft, 0), // إذا أقل من صفر منرجع صفر
            'end_date'      => $end->toDateTimeString(),
        ], 200);
    }
    public function index(Request $request)
    {
        $request->validate([
            'company_name' => 'sometimes|string',
            'company_code' => 'sometimes|string',
            'tax_number'   => 'sometimes|string',
        ]);

        $subscribers = Subscriber::with('users:id,first_name,last_name,email,subscriber_id','users.roles:id,name')
            ->when($request->company_name, function ($q) use ($request) {
                $q->where('company_name', 'like', '%' . $request->company_name);
            })
            ->when($request->company_code, function ($q) use ($request) {
                $q->where('company_code', 'like', '%' . $request->company_code);
            })
            ->when($request->tax_number, function ($q) use ($request) {
                $q->where('tax_number', 'like', '%' . $request->tax_number);
            })
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'data' => $subscribers
        ]);
    }



}
