<?php

namespace App\Enums;

enum NotificationType: string
{
    case NEW_ORDER              = 'new_order';              // admin: a doctor placed a new order
    case NEW_ORDER_ASSIGNMENT   = 'new_order_assignment';   // technician: assigned a new case
    case PAYMENT                = 'payment';                // doctor: a payment was added to invoices
    case DISCOUNT               = 'discount';               // doctor: a discount was added to an invoice
    case ORDER_RECEIVED         = 'order_received';         // order received at the lab
    case ORDER_DELIVERED        = 'order_delivered';        // order delivered to the patient
    case ORDER_COMPLETED        = 'order_completed';        // composition finished / order completed
    case AVAILABILITY_CHANGED   = 'availability_changed';   // technician availability toggled
    case NEW_SUBSCRIPTION_PLAN  = 'new_subscription_plan';  // admin: a new subscription plan is available
    case SUBSCRIPTION_REMINDER  = 'subscription_reminder';  // admin: subscription expiring/expired

    /**
     * Frontend navigation + refresh target. The frontend opens this screen on
     * notification tap (background/killed) and refreshes it when foregrounded.
     */
    public function target(): string
    {
        return match ($this) {
            self::NEW_ORDER             => 'pending_orders',
            self::NEW_ORDER_ASSIGNMENT  => 'assigned_orders',
            self::PAYMENT               => 'invoices',
            self::DISCOUNT              => 'invoices',
            self::ORDER_RECEIVED        => 'order_details',
            self::ORDER_DELIVERED       => 'orders',
            self::ORDER_COMPLETED       => 'completed_orders',
            self::AVAILABILITY_CHANGED  => 'availability',
            self::NEW_SUBSCRIPTION_PLAN => 'subscription_plans',
            self::SUBSCRIPTION_REMINDER => 'subscription',
        };
    }

    /** Wire payload for FCM `data`. */
    public function toData(): array
    {
        return [
            'type'   => $this->value,
            'target' => $this->target(),
        ];
    }
}
