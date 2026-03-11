<?php

namespace App\Http\Middleware;

use App\Zatca\ZatcaPolicyResolver;
use Closure;
use Illuminate\Support\Facades\Cache;

class EnsureZatcaIsCompleted
{
    public function handle($request, Closure $next)
    {
        $subscriber = auth('admin')->user()->subscribers;
        $canIssueInvoice = Cache::get("subscriber_onboarded:{$subscriber->id}");
        if ($canIssueInvoice === null)
        {
            $policy = ZatcaPolicyResolver::resolve($subscriber);
            $canIssueInvoice = $policy->canIssueInvoice();
            Cache::put("subscriber_onboarded:{$subscriber->id}", $canIssueInvoice, now()->addHours(12));
        }

        if (!$canIssueInvoice) {
            abort(403, 'ZATCA onboarding is required.');
        }
        return $next($request);
    }
}

