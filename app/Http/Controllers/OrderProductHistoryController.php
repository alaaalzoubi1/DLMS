<?php

namespace App\Http\Controllers;
use App\Models\OrderProductHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderProductHistoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $subscriberId = auth('admin')->user()->subscriber_id;

        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);

        $query = OrderProductHistory::where('subscriber_id', $subscriberId);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $histories = $query->latest()->paginate(20);

        return response()->json($histories);
    }

    public function topTechnicians(Request $request): JsonResponse
    {
        $subscriberId = auth('admin')->user()->subscriber_id;

        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);

        $query = OrderProductHistory::where('subscriber_id', $subscriberId);

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $top = $query
            ->select('user_id', DB::raw('COUNT(*) as total_work'))
            ->groupBy('user_id')
            ->limit(10)
            ->with('user:id,first_name,last_name')
            ->orderByDesc('total_work')
            ->get();
        return response()->json($top);
    }
    public function orderProductHistory($orderProductId): JsonResponse
    {
        $subscriberId = auth('admin')->user()->subscriber_id;

        $history = OrderProductHistory::with('user:id,first_name,last_name')
            ->where('subscriber_id', $subscriberId)
            ->where('order_product_id', $orderProductId)
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'order_product_id' => $orderProductId,
            'history' => $history
        ]);
    }
}
