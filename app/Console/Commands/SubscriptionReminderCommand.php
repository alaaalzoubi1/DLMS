<?php
// app/Console/Commands/SubscriptionReminderCommand.php

namespace App\Console\Commands;

use App\Models\Subscriber;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Jobs\SendFirebaseNotificationJob; // تأكد من المسار الصحيح للـ Job

class SubscriptionReminderCommand extends Command
{
    protected $signature = 'subscription:remind';
    protected $description = 'Sends daily subscription expiration reminders (7, 3, 1 days) and expiration notices.';

    public function handle()
    {
        $now = Carbon::now();
        $today = $now->copy()->startOfDay();

        $subscribers = Subscriber::where('trial_end_at', '<=', $now->copy()->addDays(7)->endOfDay())
            ->get();

        foreach ($subscribers as $subscriber) {
            $endDate = Carbon::parse($subscriber->trial_end_at)->startOfDay();
            $title = $body = null;

            if ($endDate->isBefore($today)) {
                $title = "انتهى الاشتراك لديك";
                $body = "قم بتجديد الاشتراك فوراً لبقاء وصول الطلبات لديك";

            } elseif ($endDate->equalTo($today->copy()->addDays(1))) {
                $title = "متبقي 1 يوم على انتهاء الاشتراك";
                $body = "سارع بتجديد الاشتراك لبقاء العمل قائم";

            } elseif ($endDate->equalTo($today->copy()->addDays(3))) {
                $title = "متبقي 3 أيام على انتهاء الاشتراك";
                $body = "سارع بتجديد الاشتراك لبقاء العمل قائم";

            } elseif ($endDate->equalTo($today->copy()->addDays(7))) {
                $title = "متبقي 7 أيام على انتهاء الاشتراك";
                $body = "سارع بتجديد الاشتراك لبقاء العمل قائم";

            } else {
                continue;
            }

            if ($title && $body) {
                $admin = User::where('subscriber_id', $subscriber->id)
                    ->role('admin')
                    ->select('id','FCM_token')
                    ->first();

                $token = $admin->FCM_token ?? null;

                if ($token) {
                    SendFirebaseNotificationJob::dispatch($token, $title, $body);
                }
            }
        }

        $this->info('Subscription reminders dispatched successfully.');
    }
}
