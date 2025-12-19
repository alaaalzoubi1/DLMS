<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Subscriber;

class CheckSubscriberActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth('admin')->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $subscriberId = $user->subscriber_id;
        if (!$subscriberId) {
            return response()->json(['error' => 'No subscriber assigned'], 403);
        }

        $isActive = Cache::get("subscriber_active:{$subscriberId}");

        if ($isActive === null) {
            $subscriber = Subscriber::find($subscriberId);

            if (!$subscriber) {
                return response()->json(['error' => 'Subscriber not found'], 404);
            }

            $isActive = $subscriber->trial_end_at >= now();

            Cache::put("subscriber_active:{$subscriberId}", $isActive, now()->addDay());
        }

        if (!$isActive) {
            return response()->json(['error' => 'انتهى الاشتراك الخاص بك, يرجى تجديد الاشتراك والمحاولة مرة أُخرى'], 403);
        }

        return $next($request);
    }
}
