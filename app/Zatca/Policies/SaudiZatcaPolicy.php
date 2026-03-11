<?php

namespace App\Zatca\Policies;

use App\Models\Subscriber;
use App\Models\SubscriberZatcaCredential;
use App\Zatca\Contracts\ZatcaPolicyInterface;

class SaudiZatcaPolicy implements ZatcaPolicyInterface
{
    protected Subscriber $subscriber;

    public function __construct(Subscriber $subscriber)
    {
        $this->subscriber = $subscriber;
    }
    public function requiresOnboarding(): bool
    {
        return true;
    }

    public function canIssueInvoice(): bool
    {
        return SubscriberZatcaCredential::isValidForSubscriber(
            $this->subscriber->id,
            config('services.zatca.environment')
        );
    }
}

