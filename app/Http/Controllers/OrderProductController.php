<?php

namespace App\Http\Controllers;

use App\Models\ClinicProduct;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderProductController extends Controller
{
    private function resolvePrice($productId, $order)
    {
        $clinicId = $order->doctor->clinic_id ?? null;

        if ($clinicId) {
            $clinicPrice = ClinicProduct::where('product_id', $productId)
                ->where('clinic_id', $clinicId)
                ->value('price');

            if ($clinicPrice !== null) {
                return $clinicPrice;
            }
        }

        return Product::where('id', $productId)->value('price') ?? 0;
    }
    private function recalculateOrderCost($order)
    {
        //TODO handel the total cost with the discount.
        $total = $order->orderProducts->sum(function ($item) {
            return count($item->tooth_numbers) * $item->unit_price;
        });

        $order->update(['cost' => $total]);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'unit_price' => 'nullable|numeric|min:0',
            'tooth_numbers' => 'required|array',
            'note' => 'nullable|string',
            'tooth_color_id' => 'required|exists:tooth_colors,id',
        ]);

        $order = Order::with('doctor')->findOrFail($data['order_id']);

        if ($order->invoiced) {
            return response()->json(['error' => 'Cannot modify invoiced order'], 403);
        }

        if (!isset($data['unit_price'])) {
            $data['unit_price'] = $this->resolvePrice($data['product_id'], $order);
        }

        $product = Product::find($data['product_id']);

        $orderProduct = $order->orderProducts()->create([
            ...$data,
            'product_name' => $product->name,
        ]);

        $this->recalculateOrderCost($order->fresh('orderProducts'));

        return response()->json($orderProduct);
    }
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'product_id' => 'sometimes|exists:products,id',
            'unit_price' => 'nullable|numeric|min:0',
            'tooth_numbers' => 'sometimes|array',
            'note' => 'nullable|string',
            'tooth_color_id' => 'sometimes|exists:tooth_colors,id',
            'specialization_users_id' => 'sometimes|exists:specialization__users,id',
        ]);

        $orderProduct = OrderProduct::with('order.doctor')->findOrFail($id);
        $order = $orderProduct->order;

        if ($order->invoiced) {
            return response()->json(['error' => 'Cannot modify invoiced order'], 403);
        }

        if (isset($data['product_id'])) {

            if (!array_key_exists('unit_price', $data)) {
                $data['unit_price'] = $this->resolvePrice($data['product_id'], $order);
            }

            $data['product_name'] = Product::where('id', $data['product_id'])->value('name');
        }

        $orderProduct->update($data);

        $this->recalculateOrderCost($order->fresh('orderProducts'));

        return response()->json($orderProduct);
    }
    public function destroy($id)
    {
        $orderProduct = OrderProduct::with('order')->findOrFail($id);
        $order = $orderProduct->order;

        if ($order->invoiced) {
            return response()->json(['error' => 'Cannot modify invoiced order'], 403);
        }

        $orderProduct->delete();

        $this->recalculateOrderCost($order->fresh('orderProducts'));

        return response()->json(['message' => 'Deleted successfully']);
    }

}
