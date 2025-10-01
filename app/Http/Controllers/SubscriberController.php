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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubscriberRequest $request)
    {
        //
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



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subscriber $subscriber)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubscriberRequest $request, Subscriber $subscriber)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscriber $subscriber)
    {
        //
    }

    public function subscribeToPlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        try {
            DB::beginTransaction();

            $plan = SubscriptionPlan::findOrFail($request->plan_id);

            // إذا عنده اشتراك شغال → نضيف الأيام على تاريخ الانتهاء
            // إذا منتهي → نبدأ من اليوم
            $subscriber = auth('admin')->user()->subscribers;
            $baseDate = $subscriber->trial_end_at && $subscriber->trial_end_at->isFuture()
                ? $subscriber->trial_end_at
                : now();

            $subscriber->trial_end_at = $baseDate->copy()->addDays($plan->duration_days);
            $subscriber->save();

            // تحديث الكاش
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



}
