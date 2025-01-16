<?php

namespace App\Http\Controllers;
use Exception;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function Symfony\Component\String\s;

class OrderController extends Controller
{
    private function validateOrderRequest($data)
    {
        return Validator::make($data, [
            'specialization_subscriber_id' => 'required|integer|exists:specialization__subscribers,id',
            'subscriber_id' => 'required|integer|exists:subscribers,id',
            'doctor_id' => 'required|integer|exists:doctors,id',
            'status' => 'required|in:pending,completed,cancelled',
            'type' => 'required|in:futures,new,test,returned',
            'invoiced' => 'required|boolean',
            'paid' => 'required|integer|min:0',
            'patient_name' => 'required|string|max:255',
            'receive' => 'required|date',
            'delivery' => 'nullable|date',
            'patient_id' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'products' => 'required|array',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.tooth_color_id' => 'required|integer|exists:tooth_colors,id',
            'products.*.tooth_number' => 'required|string|max:255',
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

    private function createOrderProducts($products, $orderId)
    {
        foreach ($products as $product) {
            OrderProduct::create([
                'product_id' => $product['product_id'],
                'order_id' => $orderId,
                'tooth_color_id' => $product['tooth_color_id'],
                'tooth_number' => $product['tooth_number'],
            ]);
        }
    }

    public function createOrder(Request $request)
    {
        $data = $request->all();
        $validator = $this->validateOrderRequest($data);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $user = $this->fetchUserWithSpecialization($data['specialization_subscriber_id'], $data['subscriber_id']);

            if (!$user) {
                return response()->json(['error' => 'No user found for the given specialization and subscriber.'], 404);
            }

            $this->incrementUserWorkingOn($user->id);

            // Calculate total cost with clinic-specific prices derived from doctor_id
            $totalCost = $this->calculateTotalCost($data['products'], $data['doctor_id']);
            $order = Order::create([
                'doctor_id' => $data['doctor_id'],
                'specialization_users_id' => $user->specialization_users_id,
                'subscriber_id' =>  $data['subscriber_id'],
                'status' => $data['status'],
                'type' => $data['type'],
                'invoiced' => $data['invoiced'],
                'paid' => $data['paid'],
                'cost' => $totalCost,
                'patient_name' => $data['patient_name'],
                'receive' => $data['receive'],
                'delivery' => $data['delivery'],
                'patient_id' => $data['patient_id'],
                'specialization' => $data['specialization'],
            ]);
//            $products = Product::whereIn('id', $data['products'])->get();
            $this->createOrderProducts($data['products'], $order->id);

            return response()->json(['message' => 'Order created successfully.', 'order' => $order], 201);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function listInvoices(Request $request)
    {
        $validated = $request->validate([
            'type' => 'sometimes|string|in:futures,returned,test,new',
        ]);

        $subscriber_id = auth('admin')->user()->subscriber_id;

        $query = Order::query()
            ->where('subscriber_id', $subscriber_id)
            ->with(['products', 'specializationUser.specialization']); // Include relationships

        if (!empty($validated['type'])) {
            $query->where('type', $validated['type']);
        }

        // Fetch orders with relationships
        $orders = $query->paginate(10);

        // Map orders to include the specialization name directly
        $orders->getCollection()->transform(function ($order) {
            $order->specialization = $order->specializationUser->specialization->name ?? null; // Extract specialization name
            unset($order->specialization_user); // Optionally remove the nested specialization_user relationship
            return $order;
        });

        return response()->json($orders, 200);
    }


    public function updateOrderSpecializationUser(Request $request)
    {
        $data = $request->only(['order_id', 'specialization_subscriber_id']);

        $validator = Validator::make($data, [
            'order_id' => 'required',
            'specialization_subscriber_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            // Fetch the order
            $order = Order::findOrFail($data['order_id']);
            $user = $this->fetchUserWithSpecialization($data['specialization_subscriber_id'], $data['subscriber_id']);


            if ($user) {
                $this->incrementUserWorkingOn($user->id);
            } else {
                throw new Exception("New specialization user not found.");
            }

            // Update the order with the new specialization_users_id
            $order->specialization_users_id = $user->specialization_users_id;
            $order->save();

            DB::commit();

            return response()->json(['message' => 'Order updated successfully.', 'order' => $order], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


}
