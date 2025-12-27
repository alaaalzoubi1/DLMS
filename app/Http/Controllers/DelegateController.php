<?php

namespace App\Http\Controllers;

use App\Jobs\SendFirebaseNotificationJob;
use App\Models\Order;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DelegateController extends Controller
{
    public function registerDelegate(Request $request)
    {
        $validatedData = $request->validate([
            'company_code' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore(User::where('email', $request->input('email'))->first()),
            ],
            'password' => 'required|string|min:8',
            'confirmed_password' => 'required|same:password',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'fcm_token'  => 'nullable|string|max:500',
        ]);

        $subscriber = Subscriber::where('company_code', strtoupper($validatedData['company_code']))->first();

        if (!$subscriber) {
            throw ValidationException::withMessages([
                'company_code' => ['Company not found. Please check the company code and try again.'],
            ])->status(422);
        }

        $user = User::create([
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'subscriber_id' => $subscriber->id,
            'FCM_token' => $validatedData['fcm_token']
        ]);

        $user->assignRole('delegate');

        $credentials = ['email' => $validatedData['email'], 'password' => $validatedData['password']];
        $token = Auth::guard('admin')->attempt($credentials);

        return response()->json([
            'message' => 'Technical registered successfully',
            'company_code' => strtoupper($validatedData['company_code']),
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'trial_start_at' => $subscriber->trial_start_at,
            'trial_end_at' => $subscriber->trial_end_at,
            'company_name' => $subscriber->company_name,
            'token' => $token,
        ], 201);
    }
    public function deleteAccount()
    {
        $account = auth('admin')->user();
        Auth::guard('admin')->logout();
        $account->delete();
        return response()->json([
            'message' => 'تم حذف الحساب بنجاح'
        ]);
    }
    public function delegateProfile(): JsonResponse
    {
        $user = auth('admin')->user();

        return response()->json([
            'delegate' => [
                'id' => $user->id,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'role' => $user->roles->pluck('name')->first(),
                'subscriber' => $user->subscribers ? [
                    'id' => $user->subscribers->id,
                    'name' => $user->subscribers->company_name,
                    'company code' => $user->subscribers->company_code,
                ] : null,
            ],
        ]);
    }
    public function ordersToReceive()
    {
        $subscriberId = auth('admin')->user()->subscriber_id;

        $orders = Order::with('doctor:id,first_name,last_name,clinic_id','doctor.clinic:id,name')
            ->where('subscriber_id', $subscriberId)
            ->whereNull('receive')
            ->whereNull('delivery')
            ->where('status', 'pending')
            ->Receivable()
            ->get();

        return response()->json([
            'data' => $orders
        ]);
    }

    public function ordersReadyForDelivery()
    {
        $subscriberId = auth('admin')->user()->subscriber_id;

        $orders = Order::with('doctor:id,first_name,last_name,clinic_id','doctor.clinic:id,name')
            ->where('subscriber_id', $subscriberId)
            ->whereNotNull('receive')
            ->whereNull('delivery')
            ->where('status', 'completed')
            ->Receivable()
            ->get();

        return response()->json([
            'data' => $orders
        ]);
    }
    public function receiveOrder(Request $request)
    {
        $request->validate([
            'receive' => 'required|date_format:Y-m-d H:i:s',
            'order_id' => 'required|integer'
        ]);

        $subscriberId = auth('admin')->user()->subscriber_id;

        $order = Order::with([
            'subscriber.users',
            'doctor.account'
        ])
            ->where('id', $request->order_id)
            ->where('subscriber_id', $subscriberId)
            ->firstOrFail();

        $order->receive = $request->receive;
        $order->save();
        $admin = $order->subscriber->users->first()->FCM_token;
        $doctor = $order->doctor->account->FCM_token;
        $title = "استلام طلب";
        $body = "تم استلام الطلب رقم {$order->id} للمريض {$order->patient_name} في {$order->receive}.";

        if ($admin) {
            SendFirebaseNotificationJob::dispatch(
                $admin,
                $title,
                $body
            );
        }

        if ($doctor) {
            SendFirebaseNotificationJob::dispatch(
                $doctor,
                $title,
                $body
            );
        }

        return response()->json([
            'message' => 'تم استلام الطلب بنجاح',
            'data' => [
                'id' => $order->id,
                'receive' => $order->receive,
            ]
        ]);

    }
    public function deliverOrder(Request $request)
    {
        $request->validate([
            'delivery' => 'required|date_format:Y-m-d H:i:s',
            'order_id' => 'required|integer'
        ]);
        $subscriberId = auth('admin')->user()->subscriber_id;
        $order = Order::with('doctor.account')
            ->where('subscriber_id',$subscriberId)
            ->where('id', $request->order_id)
            ->firstOrFail();

        $order->delivery = $request->delivery;
        $order->save();
        $doctor = $order->doctor->account->FCM_token;
        $title = "تم توصيل الطلب بنجاح";
        $body  = "تم توصيل الطلب رقم {$order->id} للمريض {$order->patient_name}.";
        if ($doctor)
            SendFirebaseNotificationJob::dispatch($doctor,$title,$body);
        return response()->json([
            'message' => 'تم تسجيل وقت التوصيل بنجاح',
            'data' => [
                'receive' => $order->receive,
                'delivery' => $order->delivery,
            ]
        ]);
    }





}
