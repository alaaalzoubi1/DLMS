<?php

namespace App\Zatca\Policies;

use App\Zatca\Contracts\ZatcaPolicyInterface;

class NullZatcaPolicy implements ZatcaPolicyInterface
{
    public function requiresOnboarding(): bool
    {
        return false;
    }

    public function canIssueInvoice(): bool
    {
        return true;
    }
}

