<?php

namespace App\Http\Controllers;
use App\Enums\ImpressionType;
use App\Http\Requests\DoctorOrdersRequest;
use App\Http\Requests\OrdersWithFilters;
use App\Http\Requests\StoreOrderDiscountRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateDiscountRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrdersResource;
use App\Jobs\SendFirebaseNotificationJob;
use App\Models\Category;
use App\Models\OrderDiscount;
use App\Models\OrderProductHistory;
use App\Models\Subscriber;
use App\Models\Type;
use App\Services\PriceSittingsService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderProduct;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
use Kreait\Firebase\Exception\Auth\AuthError;

class OrderController extends Controller
{
    private function logOrderProductHistory($orderProductId, $user, $subscriberId)
    {
        OrderProductHistory::create([
            'order_product_id'   => $orderProductId,
            'user_id'            => $user->id,
            'subscriber_id'      => $subscriberId,
            'specialization_name'=> $user->specialization_name ?? null,
        ]);
    }
    private function validateOrderRequest($data)
    {
        return Validator::make($data, [
            'doctor_id'     => 'required|integer|exists:doctors,id',
            'status'        => 'required|in:pending,completed,cancelled',
            'type_id'       => 'required|exists:types,id',
            'paid'          => 'nullable|integer|min:0',
            'patient_name'  => 'required|string|max:255',
            'receive'       => 'nullable|date_format:Y-m-d H:i:s',
            'delivery'      => 'nullable|date_format:Y-m-d H:i:s',
            'patient_id'    => 'nullable|string|max:255',
            'impression_type' => ['required', new Enum(ImpressionType::class)],
            'products'                                      => 'required|array',
            'products.*.product_id'                         => 'required|integer|exists:products,id',
            'products.*.tooth_color_id'                     => 'required|integer|exists:tooth_colors,id',
            'products.*.tooth_numbers'                      => 'required|array|min:1',
            'products.*.tooth_numbers.*'                    => 'required|string|max:2',
            'products.*.specialization_subscriber_id'       => 'sometimes|integer|exists:specialization__subscribers,id',
            'products.*.note'                               => 'nullable|string',
        ]);
    }

    private function fetchUserWithSpecialization($specializationSubscriberId, $subscriberId)
    {
        return User::join('specialization__users', 'users.id', '=', 'specialization__users.user_id')
            ->join('specialization__subscribers', 'specialization__users.subscriber_specializations_id', '=', 'specialization__subscribers.id')
            ->join('specializations', 'specializations.id', '=', 'specialization__subscribers.specializations_id')
            ->where('specialization__subscribers.id', $specializationSubscriberId)
            ->where('specialization__subscribers.subscriber_id', $subscriberId)
            ->select(
                'users.*',
                'specialization__users.id as specialization_users_id',
                'specializations.name as specialization_name'
            )
            ->orderBy('users.working_on', 'asc')
            ->first();
    }

    private function calculateTotalCost($products, $doctorId)
    {
        $productIds = collect($products)->pluck('product_id')->toArray();

        $clinicId = DB::table('doctors')->where('id', $doctorId)->value('clinic_id');

        $specialPrices = DB::table('clinic_products')
            ->whereIn('product_id', $productIds)
            ->where('clinic_id', $clinicId)
            ->pluck('price', 'product_id');

        $productPrices = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $totalCost = 0;

        foreach ($products as $prod) {

            $productId = $prod['product_id'];

            $basePrice = $specialPrices[$productId]
                ?? $productPrices[$productId]->price;

            $teethCount = is_array($prod['tooth_numbers'])
                ? count($prod['tooth_numbers'])
                : 1;

            $totalCost += ($basePrice * $teethCount);
        }

        return $totalCost;
    }

    private function incrementUserWorkingOn($userId)
    {
        $user = User::findOrFail($userId);
        $user->increment('working_on');
    }
    private function decrementUserWorkingOn($userId)
    {
        $user = User::findOrFail($userId);
        $user->decrement('working_on');
    }

    /**
     * @throws Exception
     */
    private function createOrderProducts($products, $orderId, $subscriber_id)
    {
        foreach ($products as $product) {

            $user = null;

            if (array_key_exists('specialization_subscriber_id', $product)) {

                $user = $this->fetchUserWithSpecialization(
                    $product['specialization_subscriber_id'],
                    $subscriber_id
                );

                if (!$user) {
                    throw new Exception("No user found for specialization.");
                }

                $this->incrementUserWorkingOn($user->id);
            }

            $originalProduct = Product::find($product['product_id']);

            $orderProduct = OrderProduct::create([
                'product_id'              => $product['product_id'],
                'order_id'                => $orderId,
                'tooth_color_id'          => $product['tooth_color_id'],
                'tooth_numbers'           => $product['tooth_numbers'],
                'specialization_users_id' => $user->specialization_users_id ?? null,
                'note'                    => $product['note'] ?? null,
                'unit_price'              => $originalProduct->final_price,
                'product_name'            => $originalProduct->name
            ]);

            if ($user) {
                $this->logOrderProductHistory($orderProduct->id, $user, $subscriber_id);
            }
        }
    }
    public function createOrder(Request $request)
    {
        $subscriber_id = Auth::guard('admin')->user()->subscriber_id;
        $data = $request->all();
        $validator = $this->validateOrderRequest($data);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $patientId = $data['patient_id'] ?? strtoupper(Str::random(9));

            $totalCost = $this->calculateTotalCost($data['products'], $data['doctor_id']);

            $order = Order::create([
                'doctor_id'     => $data['doctor_id'],
                'subscriber_id' => $subscriber_id,
                'status'        => $data['status'],
                'type_id'       => $data['type_id'],
                'invoiced'      => false,
                'paid'          => $data['paid'],
                'cost'          => $totalCost,
                'patient_name'  => $data['patient_name'],
                'receive'       => $data['receive'],
                'delivery'      => $data['delivery'],
                'patient_id'    => $patientId,
                'impression_type'  => $data['impression_type'],
            ]);

            $this->createOrderProducts($data['products'], $order->id, $subscriber_id);

            return response()->json(['message' => 'Order created successfully.', 'order' => $order], 201);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateOrderProductSpecializationUser(Request $request): JsonResponse
    {
        $data = $request->only(['order_product_id', 'specialization_subscriber_id']);

        $validator = Validator::make($data, [
            'order_product_id' => 'required|exists:order_products,id',
            'specialization_subscriber_id' => 'required|exists:specialization__subscribers,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $orderProduct = OrderProduct::with('product.category', 'order')->findOrFail($data['order_product_id']);

            $currentUser = auth('admin')->user();

            $this->decrementUserWorkingOn($currentUser->id);

            $newUser = $this->fetchUserWithSpecialization(
                $data['specialization_subscriber_id'],
                $orderProduct->order->subscriber_id
            );

            if (!$newUser) {
                throw new Exception("No user found for specialization.");
            }

            $orderProduct->specialization_users_id = $newUser->specialization_users_id;
            $orderProduct->save();

            $this->incrementUserWorkingOn($newUser->id);

            $this->logOrderProductHistory($orderProduct->id, $newUser, $orderProduct->order->subscriber_id);

            DB::commit();

            $title = "طلب '{$orderProduct->product->category->name}' جديد";
            $body = "وصلتك حالة جديدة يا '{$newUser->first_name}'!";
            $token = $newUser->FCM_token;

            if ($token) {
                SendFirebaseNotificationJob::dispatch($token, $title, $body);
            }

            return response()->json(['message' => 'Order product updated successfully.'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function listDoctorInvoices(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);

        $subscriber_id = auth()->user()->subscriber_id;

        $query = Order::where('doctor_id', $validated['doctor_id'])
            ->where('subscriber_id', $subscriber_id)
            ->where('invoiced', true)
            ->with(['doctor', 'products.specializationUser.specialization','products.specializationUser.user:id,first_name,last_name']);

        if (!empty($validated['from_date']) && !empty($validated['to_date'])) {
            $query->whereBetween('receive', [$validated['from_date'], $validated['to_date']]);
        } elseif (!empty($validated['from_date'])) {
            $query->where('receive', '>=', $validated['from_date']);
        } elseif (!empty($validated['to_date'])) {
            $query->where('receive', '<=', $validated['to_date']);
        }

        $orders = $query->paginate(10);

        $totalCost = $query->sum('cost');
        $paid = $query->sum('paid');
        $left = $totalCost - $paid;
        return response()->json([
            'doctor_id' => $validated['doctor_id'],
            'total_cost' => $totalCost,
            'paid' => $paid,
            'left' => $left,
            'orders' => $orders
        ], 200);
    }
    public function listFromToInvoices(Request $request)
    {
        $validated = $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);

        $subscriber_id = auth()->user()->subscriber_id;

        $query = Order::
        where('subscriber_id', $subscriber_id)
            ->with(['doctor', 'products.specializationUser.specialization','products.specializationUser.user:id,first_name,last_name','zatcaDocument','discount','files','creditNotes']);

        if (!empty($validated['from_date']) && !empty($validated['to_date'])) {
            $query->whereBetween('receive', [$validated['from_date'], $validated['to_date']]);
        } elseif (!empty($validated['from_date'])) {
            $query->where('receive', '>=', $validated['from_date']);
        } elseif (!empty($validated['to_date'])) {
            $query->where('receive', '<=', $validated['to_date']);
        }

        $orders = $query->get();

        $totalCost = $query->sum('cost');
        $paid = $query->sum('paid');
        $left = $totalCost - $paid;
        return response()->json([
            'total_cost' => $totalCost,
            'paid' => $paid,
            'left' => $left,
            'orders' => $orders
        ], 200);
    }

    public function updateOrder(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:pending,completed,cancelled',
            'type_id' => 'sometimes|exists:types,id',
            'paid' => 'sometimes|integer|min:0',
            'patient_name' => 'sometimes|string|max:255',
            'receive' => 'sometimes|date',
            'delivery' => 'sometimes|date',
        ]);

        $subscriber_id = auth('admin')->user()->subscriber_id;

        $order = Order::where('id', $id)
            ->where('subscriber_id', $subscriber_id)
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found or access denied.'], 404);
        }

        if ($order->invoiced) {
            return response()->json([
                'error' => 'Cannot update invoiced order.'
            ], 403);
        }


        $order->update($validated);

        return response()->json([
            'message' => 'Order updated successfully.',
            'order' => $order
        ], 200);
    }

    public function doctorCreateOrder(StoreOrderRequest $request)
    {
        $doctor = auth('api')->user()->doctor;

        if (!$doctor) {
            return response()->json(['error' => 'Only doctors can create orders.'], 403);
        }

        $subscriber = Subscriber::with([
            'users' => fn ($q) => $q->role('admin')->select('id','FCM_token')
        ])->findOrFail($request->subscriber_id);

        if ($subscriber->trial_end_at < now()) {
            return response()->json([
                'error' => 'Subscriber subscription has expired.'
            ], 403);
        }

        $this->authorize('view', $subscriber);

        $data = $request->validated();

        DB::beginTransaction();

        try {

            $type = Type::where('id', $data['type_id'])
                ->where('subscriber_id', $data['subscriber_id'])
                ->firstOrFail();

            $productIds = collect($data['products'])->pluck('product_id');

            $categories = Category::where('subscriber_id', $subscriber->id)
                ->pluck('id');

            $products = Product::whereIn('id', $productIds)
                ->whereIn('category_id', $categories)
                ->get()
                ->keyBy('id');

            if ($products->count() !== $productIds->count()) {
                throw new \Exception("Some products do not belong to subscriber");
            }

            $totalCost = 0;

            foreach ($data['products'] as $prod) {
                $product = $products[$prod['product_id']];
                $totalCost += $product->final_price * count($prod['tooth_numbers']);
            }

            $order = Order::create([
                'doctor_id' => $doctor->id,
                'subscriber_id' => $subscriber->id,
                'type_id' => $type->id,
                'status' => 'pending',
                'invoiced' => false,
                'paid' => 0,
                'cost' => $totalCost,
                'patient_name' => $data['patient_name'],
                'patient_id' => $data['patient_id'] ?? strtoupper(Str::random(9)),
                'impression_type' => $data['impression_type'],
            ]);

            foreach ($data['products'] as $prod) {
                $product = $products[$prod['product_id']];

                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $prod['product_id'],
                    'tooth_color_id' => $prod['tooth_color_id'],
                    'tooth_numbers' => $prod['tooth_numbers'],
                    'product_name' => $product->name,
                    'note' => $prod['note'] ?? null,
                    'unit_price' => $product->final_price
                ]);
            }

            DB::commit();

            $admin = $subscriber->users->first();
            if ($admin?->FCM_token) {
                SendFirebaseNotificationJob::dispatch(
                    $admin->FCM_token,
                    "طلب جديد",
                    "طلب جديد من الطبيب {$doctor->first_name}"
                );
            }

            return response()->json([
                'message' => 'Order created successfully.',
                'order' => new OrderResource($order)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'Failed to create order.',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    public function doctorOrders(DoctorOrdersRequest $request)
    {
        $doctor = auth('api')->user()->doctor;
        $doctorAccountId = auth('api')->id();

        $query = Order::where('doctor_id', $doctor->id);

        if ($request->filled('patient_name')) {
            $query->where('patient_name', 'like', "%{$request->patient_name}%");
        }

        if ($request->filled('patient_id')) {
            $query->where('patient_id', 'like', "%{$request->patient_id}%");
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('invoiced')) {
            $query->where('invoiced', (bool) $request->invoiced);
        }

        if ($request->filled('type_id')) {
            $query->where('type_id', $request->type_id);
        }

        if ($request->filled('subscriber_id')) {
            $query->where('subscriber_id', $request->subscriber_id);
        }

        $orders = $query->with([
            'type:id,type',
            'subscriber:id,company_name,tax_number',
            'orderProducts.toothColor:id,color',
            'orderProducts.specializationUser',
            'doctor:id,clinic_id,first_name,last_name',
            'doctor.clinic:id,tax_number,name',
            'discount',
            'files'
        ])->latest()->get();

        /**
         * 🔥 STEP 1: collect unique subscriber IDs
         */
        $subscriberIds = $orders->pluck('subscriber_id')->unique()->values();

        /**
         * 🔥 STEP 2: fetch hidden relations in ONE query
         */
        $hiddenMap = app(PriceSittingsService::class)
            ->getHiddenSubscribersForDoctor($doctorAccountId, $subscriberIds);

        /**
         * 🔥 STEP 3: convert to map for fast lookup
         * [subscriber_id => true/false]
         */
        $hideMap = $hiddenMap->mapWithKeys(fn ($id) => [$id => true]);
        $request->merge([
            'hide_map' => $hideMap
        ]);
        return OrdersResource::collection($orders);
    }
    public function OrdersWithFilters(OrdersWithFilters $request): JsonResponse
    {
        $admin = auth('admin')->user();
        $per_page = 10;
        $query = Order::where('subscriber_id',$admin->subscriber_id);
        if ($request->boolean('rejected')) {
            $query
                ->whereHas('zatcaDocument', function ($q) {
                    $q->where('invoice_type', 'TAX_INVOICE');
                })

                ->whereDoesntHave('zatcaDocument', function ($q) {
                    $q->where('invoice_type', 'TAX_INVOICE')
                        ->where('zatca_http_status', '!=', 400);
                });
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }
        if ($request->filled('patient_name')) {
            $query->where('patient_name', 'like', "%{$request->patient_name}%");
        }
        if ($request->filled('patient_id')) {
            $query->where('patient_id', 'like', "%{$request->patient_id}%");
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('invoiced')) {
            $query->where('invoiced', (bool) $request->invoiced);
        }

        if ($request->filled('type_id')) {
            $query->where('type_id', $request->type_id);
        }
        if ($request->filled('per_page')){
            $per_page = $request->per_page;
        }



        $orders = $query->with(['type:id,type',
            'subscriber:id,company_name,tax_number',
            'products.specializationUser.specializationSubscriber.specialization:id,name',
            'products.specializationUser.specializationSubscriber.users:id,first_name,last_name',
            'doctor:id,clinic_id,first_name,last_name',
            'doctor.clinic:id,tax_number,name',
            'discount',
            'files' => function($q) { $q->Uploaded();},
            'zatcaDocument:id,order_id,invoice_type,zatca_http_status,updated_at',
        ])
            ->latest()
            ->paginate($per_page);

        return response()->json($orders, 200);
    }

//    public function adminAddPayment(Request $request)
//    {
//        $request->validate([
//            'doctor_id'   => 'required|exists:doctors,id',
//            'amount'      => 'required|numeric|min:1',
//            'patient_id'  => 'nullable|string', // اختياري
//        ]);
//        $subscriberId = auth('admin')->user()->subscriber_id;
//        $doctorId = $request->doctor_id;
//        $amount = $request->amount;
//        $patientId = $request->patient_id;
//
//        try {
//            DB::beginTransaction();
//
//            $ordersQuery = Order::with('doctor.account')
//                ->where('doctor_id', $doctorId)
//                ->where('subscriber_id',$subscriberId)
//                ->whereColumn('paid', '<', 'cost')
//                ->orderBy('receive', 'asc');
//
//            if ($patientId) {
//                $ordersQuery->where('patient_id', $patientId);
//            }
//
//            $orders = $ordersQuery->lockForUpdate()->get(); // lock لتجنب مشاكل التوازي
//
//            $remainingAmount = $amount;
//
//            foreach ($orders as $order) {
//                $toPay = $order->cost - $order->paid;
//
//                if ($remainingAmount >= $toPay) {
//                    $order->paid += $toPay;
//                    $remainingAmount -= $toPay;
//                } else {
//                    $order->paid += $remainingAmount;
//                    $remainingAmount = 0;
//                }
//
//                $order->save();
//
//                if ($remainingAmount <= 0) break;
//            }
//
//            DB::commit();
//            $actualAppliedAmount = $amount - $remainingAmount;
//            $title = "دفعة مالية";
//            $body = "تم إيداع '{$actualAppliedAmount}' في الفواتير";
//            $token = $orders->first()->doctor->account->FCM_token;
//            if ($token)
//                SendFirebaseNotificationJob::dispatch($token, $title, $body);
//
//            return response()->json([
//                'message'          => 'Payment applied successfully',
//                'remaining_amount' => $remainingAmount,
//            ], 200);
//
//        } catch (\Exception $e) {
//            DB::rollBack();
//            return response()->json([
//                'error'   => 'Payment failed',
//                'details' => $e->getMessage(),
//            ], 500);
//        }
//    }
    public function addPayment(Request $request)
    {
        $request->validate([
            'amount'     => 'required|numeric|min:1',
            'order_ids'  => 'required|array|min:1',
            'order_ids.*'=> 'exists:orders,id',
        ]);

        $amount = $request->amount;
        $orderIds = $request->order_ids;
        $remainingAmount = $amount;

        DB::beginTransaction();

        try {
            $orders = Order::with('doctor.account')
                ->whereIn('id', $orderIds)
                ->where('subscriber_id', auth('admin')->user()->subscriber_id)
                ->whereColumn('paid', '<=', 'cost')
                ->orderBy('created_at', 'asc')
                ->lockForUpdate()
                ->get();

            if ($orders->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'message' => 'No payable orders found'
                ], 422);
            }
            if ($orders->count() !== count($orderIds)) {
                DB::rollBack();
                return response()->json([
                    'message' => 'One or more orders do not belong to your subscriber'
                ],403);
            }

            $doctorPayments = [];

            foreach ($orders as $order) {
                if ($remainingAmount <= 0) break;

                $toPay = min(
                    $order->cost - $order->paid,
                    $remainingAmount
                );

                $order->paid += $toPay;
                $order->save();

                $remainingAmount -= $toPay;

                $doctorId = $order->doctor_id;

                if (!isset($doctorPayments[$doctorId])) {
                    $doctorPayments[$doctorId] = [
                        'amount' => 0,
                        'doctor' => $order->doctor
                    ];
                }

                $doctorPayments[$doctorId]['amount'] += $toPay;
            }

            DB::commit();

            // إرسال الإشعارات
            foreach ($doctorPayments as $data) {
                $doctor = $data['doctor'];
                $paidAmount = $data['amount'];

                $token = $doctor->account->FCM_token ?? null;

                if ($token) {
                    SendFirebaseNotificationJob::dispatch(
                        $token,
                        'دفعة مالية',
                        "تم إضافة دفعة بقيمة {$paidAmount} على فواتيرك"
                    );
                }
            }

            return response()->json([
                'message'           => 'Payment applied successfully',
                'applied_amount'    => $amount - $remainingAmount,
                'remaining_amount'  => $remainingAmount,
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Payment failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function assignSpecialization(Request $request, $id)
    {
        $request->validate([
            'specialization_subscriber_id' => 'required|exists:specialization__subscribers,id',
        ]);

        $orderProduct = OrderProduct::with('order','product.category')->find($id);

        if (!$orderProduct) {
            return response()->json(['error' => 'Order product not found.'], 404);
        }

        $order = $orderProduct->order;

        if (!$order) {
            return response()->json(['error' => 'Order not found for this product.'], 404);
        }

        $subscriberId = $order->subscriber_id;

        $isValidSpecialization = DB::table('specialization__subscribers')
            ->where('id', $request->specialization_subscriber_id)
            ->where('subscriber_id', $subscriberId)
            ->exists();

        if (!$isValidSpecialization) {
            return response()->json([
                'error' => 'This specialization does not belong to the same subscriber.'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $user = $this->fetchUserWithSpecialization(
                $request->specialization_subscriber_id,
                $subscriberId
            );

            if (!$user) {
                DB::rollBack();
                return response()->json([
                    'error' => 'No available technician found for this specialization and subscriber.'
                ], 404);
            }

            $this->incrementUserWorkingOn($user->id);

            $orderProduct->update([
                'specialization_users_id' => $user->specialization_users_id,
            ]);
            $allAssigned = OrderProduct::
                where('order_id', $order->id)
                ->whereNull('specialization_users_id')
                ->doesntExist();

            if ($allAssigned && $order->status === 'pending') {
                $order->update([
                    'status' => 'in_progress'
                ]);
            }

            $this->logOrderProductHistory($orderProduct->id, $user, $subscriberId);

            DB::commit();

            $title = "طلب '{$orderProduct->product->category->name}' جديد";
            $body = "وصلتك حالة جديدة يا '{$user->first_name}'!";
            $token = $user->FCM_token;

            if ($token) {
                SendFirebaseNotificationJob::dispatch($token, $title, $body);
            }

            return response()->json([
                'message' => 'Technician assigned successfully.',
                'order_product' => $orderProduct->load('product'),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to assign technician',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    public function orderDetails($id)
    {
        $order = Order::with([
            'orderProducts',
            'orderProducts.specializationUser.user:id,first_name,last_name',
            'orderProducts.toothColor:id,color',

            'subscriber:id,company_name,tax_number',
            'doctor:id,first_name,last_name,clinic_id',
            'doctor.clinic:id,name,tax_number',

            'type:id,type',
            'discount:id,type,amount,order_id',
            'files' => fn ($q) => $q->Uploaded(),

            'zatcaDocument',
            'creditNotes.items.orderProduct'
        ])
            ->select([
                'id','paid','invoiced','cost','patient_name',
                'receive','delivery','patient_id','status',
                'created_at','updated_at',
                'subscriber_id','doctor_id','type_id','impression_type'
            ])
            ->find($id);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $doctorAccountId = auth('api')->id();

        $hidePrices = app(PriceSittingsService::class)
            ->shouldHidePrice($doctorAccountId, $order->subscriber_id);
        $order->hide_price = $hidePrices;
        return response()->json([
            'order' => new OrderResource($order)
        ], 200);
    }
    public function technicalOrderDetails(Request $request)
    {
        $technical = auth('admin')->user();

        $specializationUserId = $technical->specializationUser->id;

        $order = Order::with([
            'products' => function ($q) use ($specializationUserId) {
                $q->where('specialization_users_id', $specializationUserId)
                    ->select(
                        'id',
                        'order_id',
                        'note',
                        'tooth_numbers',
                        'status',
                        'product_id',
                        'specialization_users_id',
                        'tooth_color_id',
                        'unit_price',
                        'product_name'
                    );
            },
            'products.specializationUser.user:id,first_name,last_name',
            'products.toothColor:id,color',
            'subscriber:id,company_name,tax_number',
            'doctor:id,first_name,last_name,clinic_id',
            'doctor.clinic:id,name,tax_number',
            'type:id,type',
            'discount:id,type,amount,order_id'
        ])
            ->where('id', $request->order_id)
            ->whereHas('products', function ($q) use ($specializationUserId) {
                $q->where('specialization_users_id', $specializationUserId);
            })
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json(['order' => $order], 200);
    }
//TODO can't modify discount if the order invoiced
    public function applyDiscount(StoreOrderDiscountRequest $request)
    {
        $order = Order::with('discount','doctor.account')->findOrFail($request->order_id);

        $this->authorize('DiscountManaging', $order);
        if ($order->discount) {
            return response()->json([
                'error' => 'This order already has a discount.'
            ], 422);
        }
        $discount = OrderDiscount::create([
            'order_id'      => $order->id,
            'type'          => $request->type,
            'amount'        => $request->amount,
        ]);

        $newCost = $order->cost;

        if ($discount->type === 'percentage') {
            $newCost = $newCost - ($newCost * ($discount->amount / 100));
        } elseif ($discount->type === 'fixed'){
            $newCost = $newCost - $discount->amount;
        }

        $order->cost = $newCost;
        $order->save();

        $title = "أضيف خصم لفاتورتك";
        $body = "تم إضافة خصم {$discount->amount}% لفاتورتك رقم {$order->id}";
        $token = $order->doctor->account?->FCM_token;
        if ($token)
            SendFirebaseNotificationJob::dispatch($token, $title, $body);

        return response()->json([
            'message' => 'Discount applied successfully',
            'order'   => $order,
            'discount'=> $discount
        ], 201);
    }
    public function updateDiscount(UpdateDiscountRequest $request)
    {
        $order = Order::with('discount', 'products.product')->findOrFail($request->order_id);

        $this->authorize('DiscountManaging', $order);

        $discount = $order->discount;

        $discount->update([
            'type' => $request->type,
            'amount' => $request->amount,
        ]);

        $order->cost = $this->applyDiscountCalculation($order, $discount);
        $order->save();

        return response()->json([
            'message' => 'Discount updated successfully.',
            'discount' => $discount,
            'order' => $order
        ]);
    }


    public function removeDiscount($id)
    {
        $discount = OrderDiscount::findOrFail($id);

        $order = $discount->order;
        $this->authorize('DiscountManaging', $order);

        $discount->delete();

        $order->cost = $this->calculateOrderOriginalCost($order);
        $order->save();

        return response()->json([
            'message' => 'Discount removed successfully.',
            'order' => $order
        ]);
    }


    private function applyDiscountCalculation($order, $discount)
    {
        $originalCost = $this->calculateOrderOriginalCost($order);

        if ($discount->type === 'percentage') {
            return $originalCost - ($originalCost * ($discount->amount / 100));
        }

        if ($discount->type === 'fixed') {
            return $originalCost - $discount->amount;
        }

        return $originalCost;
    }


    private function calculateOrderOriginalCost($order)
    {
        $total = 0;

        foreach ($order->products as $prod) {
            $teeth = is_array($prod->tooth_numbers)
                ? $prod->tooth_numbers
                : json_decode($prod->tooth_numbers, true);

            $count = count($teeth);

            $total += $prod->product->final_price * $count;
        }

        return $total;
    }







}
