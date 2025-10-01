<?php

namespace App\Http\Controllers;
use App\Http\Requests\DoctorOrdersRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Category;
use App\Models\Subscriber;
use App\Models\Type;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    private function validateOrderRequest($data)
    {
        return Validator::make($data, [
            'doctor_id' => 'required|integer|exists:doctors,id',
            'status' => 'required|in:pending,completed,cancelled',
            'type_id' => 'required|exists:types,id',
            'paid' => 'nullable|integer|min:0',
            'patient_name' => 'required|string|max:255',
            'receive' => 'required|date',
            'delivery' => 'nullable|date',
            'patient_id' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'products' => 'required|array',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.tooth_color_id' => 'required|integer|exists:tooth_colors,id',
            'products.*.tooth_number' => 'required|string|max:255',
            'products.*.specialization_subscriber_id' => 'required|integer|exists:specialization__subscribers,id',
            'products.*.note' => 'nullable|string',
        ]);
    }

    private function fetchUserWithSpecialization($specializationSubscriberId, $subscriberId)
    {
        return User::join('specialization__users', 'users.id', '=', 'specialization__users.user_id')
            ->join('specialization__subscribers', 'specialization__users.subscriber_specializations_id', '=', 'specialization__subscribers.id')
            ->where('specialization__subscribers.id', $specializationSubscriberId)
            ->where('specialization__subscribers.subscriber_id', $subscriberId)
            ->select('users.*', 'specialization__users.id as specialization_users_id')
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

        $productPrices = Product::whereIn('id', $productIds)->get();

        $totalCost = 0;
        foreach ($productPrices as $product) {
            $price = $specialPrices->get($product->id, $product->price);
            $totalCost += $price;
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
    private function createOrderProducts($products, $orderId, $subscriber_id)
    {
        foreach ($products as $product) {
            $user = $this->fetchUserWithSpecialization($product['specialization_subscriber_id'], $subscriber_id);
            if (!$user) {
                throw new Exception("No user found with the required specialization for subscriber ID {$subscriber_id}.");
            }
            $this->incrementUserWorkingOn($user->id);

            OrderProduct::create([
                'product_id' => $product['product_id'],
                'order_id' => $orderId,
                'tooth_color_id' => $product['tooth_color_id'],
                'tooth_number' => $product['tooth_number'],
                'specialization_users_id' => $user->specialization_users_id,
                'note' => $product['note']
            ]);
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
            $totalCost = $this->calculateTotalCost($data['products'], $data['doctor_id']);
            $invoiced = Type::find($data['type_id'])->invoiced;
            $order = Order::create([
                'doctor_id' => $data['doctor_id'],
                'subscriber_id' =>  $subscriber_id,
                'status' => $data['status'],
                'type_id' => $data['type_id'],
                'invoiced' => $invoiced,
                'paid' => $data['paid'],
                'cost' => $totalCost,
                'patient_name' => $data['patient_name'],
                'receive' => $data['receive'],
                'delivery' => $data['delivery'],
                'patient_id' => $data['patient_id'],
                'specialization' => $data['specialization'],
            ]);
//            $products = Product::whereIn('id', $data['products'])->get();
            $this->createOrderProducts($data['products'], $order->id,$subscriber_id);

            return response()->json(['message' => 'Order created successfully.', 'order' => $order], 201);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function listInvoices($type): \Illuminate\Http\JsonResponse
    {
        $validTypes = ['futures', 'returned', 'test', 'new', 'all'];

        if (!in_array($type, $validTypes)) {
            return response()->json(['message' => 'Invalid type'], 400);
        }

        $subscriber_id = auth('admin')->user()->subscriber_id;

        $query = Order::query()
            ->where('orders.subscriber_id', $subscriber_id)
            ->join('types', 'orders.type_id', '=', 'types.id')
            ->with(['products.specializationUser.specialization', 'type']) // 'type' is the relation on Order
            ->select('orders.*'); // Avoid selecting everything from 'types'

        if ($type !== 'all') {
            $query->where('types.type', $type);
        }
        $query->orderBy('created_at','desc');
        $orders = $query->paginate(10);

        $orders->getCollection()->transform(function ($order) {
            $order->products->transform(function ($product) {
                $product->specialization = $product->specializationUser->specialization->name ?? null;
                return $product;
            });
            return $order;
        });

        return response()->json($orders, 200);
    }
    public function listOrdersByStatus($status): \Illuminate\Http\JsonResponse
    {
        $validStatuses = ['pending', 'completed', 'cancelled'];

        if (!in_array($status, $validStatuses)) {
            return response()->json(['message' => 'Invalid status'], 400);
        }

        $subscriber_id = auth('admin')->user()->subscriber_id;

        // Query to fetch orders by status
        $orders = Order::query()
            ->where('orders.subscriber_id', $subscriber_id)
            ->where('orders.status', $status) // Filter orders by status
            ->with(['products.specializationUser.specialization', 'type','doctor']) // Relations
            ->join('types', 'orders.type_id', '=', 'types.id') // Join with the types table
            ->select('orders.*') // Select only the necessary columns from orders
                ->orderByDesc('updated_at')
            ->paginate(10); // Paginate results

        // Transform each order to include specialization names for products
        $orders->getCollection()->transform(function ($order) {
            $order->products->transform(function ($product) {
                $product->specialization = $product->specializationUser->specialization->name ?? null;
                return $product;
            });
            return $order;
        });

        return response()->json($orders, 200);
    }


    public function updateOrderProductSpecializationUser(Request $request): \Illuminate\Http\JsonResponse
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

            // Fetch the order product
            $orderProduct = OrderProduct::findOrFail($data['order_product_id']);

            $currentUser = auth('admin')->user();
            $this->decrementUserWorkingOn($currentUser->getAuthIdentifier());

            $newUser = $this->fetchUserWithSpecialization($data['specialization_subscriber_id'], $orderProduct->order->subscriber_id);

            if (!$newUser) {
                throw new Exception("No user found for the provided specialization_subscriber_id and subscriber_id.");
            }

            $orderProduct->specialization_users_id = $newUser->specialization_users_id;
            $orderProduct->save();

            $this->incrementUserWorkingOn($newUser->id);

            DB::commit();

            return response()->json(['message' => 'Order product updated successfully.'], 200);
        } catch (Exception $e) {
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
            ->with(['doctor', 'products.specializationUser.specialization']);

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
            ->where('invoiced', true)
            ->with(['doctor', 'products.specializationUser.specialization']);

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
            'invoiced' => 'sometimes|boolean',
            'paid' => 'sometimes|integer|min:0',
            'cost' => 'sometimes|integer|min:0',
            'patient_name' => 'sometimes|string|max:255',
            'receive' => 'sometimes|date',
            'delivery' => 'sometimes|date|after_or_equal:receive',
            'specialization' => 'sometimes|string|max:255'
        ]);

        $subscriber_id = auth()->user()->subscriber_id;

        // Find the order and ensure it belongs to the authenticated user's subscriber
        $order = Order::where('id', $id)->where('subscriber_id', $subscriber_id)->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found or access denied.'], 404);
        }

        $order->update($validated);

        return response()->json(['message' => 'Order updated successfully.', 'order' => $order], 200);
    }

    public function doctorCreateOrder(StoreOrderRequest $request)
    {
        $doctor = auth('api')->user()->doctor;
        if (!$doctor) {
            return response()->json(['error' => 'Only doctors can create orders.'], 403);
        }

        $subscriber = Subscriber::findOrFail($request->subscriber_id);

        // تحقق عبر Policy
        $this->authorize('view', $subscriber);

        $data = $request->validated();

        try {
            DB::beginTransaction();

            // 1) تحقق أن type_id تابع للـ subscriber
            $type = Type::where('id', $data['type_id'])
                ->where('subscriber_id', $data['subscriber_id'])
                ->firstOrFail();

            $invoiced = $type->invoiced;

            // 2) جلب كل الـ products مع category و final_price دفعة واحدة
            $productIds = collect($data['products'])->pluck('product_id')->toArray();

            // جلب الـ categories الخاصة بالـ subscriber
            $categories = Category::where('subscriber_id', $subscriber->id)
                ->pluck('id');

            // جلب المنتجات مع التحقق أنها ضمن الـ categories الخاصة بالـ subscriber
            $products = Product::whereIn('id', $productIds)
                ->whereIn('category_id', $categories)
                ->get()
                ->keyBy('id');

            if (count($products) !== count($productIds)) {
                throw new \Exception("Some products do not belong to subscriber {$subscriber->id}");
            }

            // جلب أسعار العيادة الخاصة دفعة واحدة
            $clinicId = $doctor->clinic_id;
            $specialPrices = DB::table('clinic_products')
                ->where('clinic_id', $clinicId)
                ->whereIn('product_id', $productIds)
                ->pluck('price', 'product_id');

            // 3) حساب التكلفة
            $totalCost = 0;
            foreach ($data['products'] as $prod) {
                $product = $products[$prod['product_id']];
                $price = $specialPrices->get($product->id, $product->price);
                $totalCost += $price;
            }

            // 4) إنشاء الأوردر
            $order = Order::create([
                'doctor_id'     => $doctor->id,
                'subscriber_id' => $subscriber->id,
                'type_id'       => $type->id,
                'invoiced'      => $invoiced,
                'status' => 'pending',
                'paid'          => $data['paid'],
                'cost'          => $totalCost,
                'patient_name'  => $data['patient_name'],
                'receive'       => $data['receive'],
                'delivery'      => $data['delivery'],
                'patient_id'    => $data['patient_id'],
                'specialization'=> $data['specialization'],
            ]);

            // 5) إضافة المنتجات للأوردر + اختيار العامل المناسب
            foreach ($data['products'] as $prod) {

                $user = $this->fetchUserWithSpecialization(
                    $prod['specialization_subscriber_id'],
                    $subscriber->id
                );

                if (!$user) {
                    throw new \Exception(
                        "No user found with specialization_subscriber_id={$prod['specialization_subscriber_id']} for subscriber_id={$subscriber->id}."
                    );
                }

                $this->incrementUserWorkingOn($user->id);

                OrderProduct::create([
                    'product_id'             => $prod['product_id'],
                    'order_id'               => $order->id,
                    'tooth_color_id'         => $prod['tooth_color_id'],
                    'tooth_number'           => $prod['tooth_number'] ?? null,
                    'specialization_users_id'=> $user->specialization_users_id,
                    'note'                   => $prod['note'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Order created successfully.',
                'order'   => $order->load('products')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function doctorOrders(DoctorOrdersRequest $request): JsonResponse
    {
        $doctor = auth('api')->user()->doctor;

        $query = Order::where('doctor_id', $doctor->id);

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

        $orders = $query->with(['type:id,type', 'subscriber:id,company_name', 'products'])->latest()->paginate(15);
        return response()->json($orders, 200);
    }
    public function adminAddPayment(Request $request)
    {
        $request->validate([
            'doctor_id'   => 'required|exists:doctors,id',
            'amount'      => 'required|numeric|min:1',
            'patient_id'  => 'nullable|string', // اختياري
        ]);
        $subscriberId = auth('admin')->user()->subscriber_id;
        $doctorId = $request->doctor_id;
        $amount = $request->amount;
        $patientId = $request->patient_id;

        try {
            DB::beginTransaction();

            // جلب الأوردرات المستحقة الدفع
            $ordersQuery = Order::where('doctor_id', $doctorId)
                ->where('subscriber_id',$subscriberId)
                ->whereColumn('paid', '<', 'cost')
                ->orderBy('receive', 'asc');

            if ($patientId) {
                $ordersQuery->where('patient_id', $patientId);
            }

            $orders = $ordersQuery->lockForUpdate()->get(); // lock لتجنب مشاكل التوازي

            $remainingAmount = $amount;

            foreach ($orders as $order) {
                $toPay = $order->cost - $order->paid;

                if ($remainingAmount >= $toPay) {
                    $order->paid += $toPay;
                    $remainingAmount -= $toPay;
                } else {
                    $order->paid += $remainingAmount;
                    $remainingAmount = 0;
                }

                $order->save();

                if ($remainingAmount <= 0) break;
            }

            DB::commit();

            return response()->json([
                'message'          => 'Payment applied successfully',
                'remaining_amount' => $remainingAmount,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error'   => 'Payment failed',
                'details' => $e->getMessage(),
            ], 500);
        }
    }




}
