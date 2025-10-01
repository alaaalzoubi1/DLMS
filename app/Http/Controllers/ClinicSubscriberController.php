<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddSubscriberRequest;
use App\Models\ClinicSubscriber;
use App\Models\Subscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClinicSubscriberController extends Controller
{

    // عرض كل المشتركين لعيادة الدكتور
    public function index(Request $request): JsonResponse
    {
        $clinicId = auth('api')->user()->doctor->clinic_id;

        $subscribers = Subscriber::whereHas('clinics', function($q) use ($clinicId) {
            $q->where('clinics.id', $clinicId);
        })->select('company_name','id')->get();

        return response()->json([
            'message' => 'Subscribers retrieved successfully',
            'data' => $subscribers
        ]);
    }

    // إضافة مشترك جديد للعيادة
    public function store(AddSubscriberRequest $request): JsonResponse
    {
        $clinicId = auth('api')->user()->doctor->clinic_id;

        $subscriber = Subscriber::where('company_code', $request->company_code)->firstOrFail();

        // تأكد انه مش مضاف قبل
        $exists = ClinicSubscriber::where('clinic_id', $clinicId)
            ->where('subscriber_id', $subscriber->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Subscriber already added to this clinic'
            ], 409);
        }

        ClinicSubscriber::create([
            'clinic_id' => $clinicId,
            'subscriber_id' => $subscriber->id,
        ]);

        return response()->json([
            'message' => 'Subscriber added successfully',
            'company_name' => $subscriber->company_name,
            'company_id' => $subscriber->id
        ], 201);
    }

    // حذف مشترك من العيادة
    public function destroy($subscriberId): JsonResponse
    {
        $clinicId = auth('api')->user()->doctor->clinic_id;

        $deleted = ClinicSubscriber::where('clinic_id', $clinicId)
            ->where('subscriber_id', $subscriberId)
            ->delete();

        if (! $deleted) {
            return response()->json([
                'message' => 'Subscriber not found in this clinic'
            ], 404);
        }

        return response()->json([
            'message' => 'Subscriber removed successfully'
        ]);
    }




}
