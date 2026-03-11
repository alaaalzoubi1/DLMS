<?php

namespace App\Zatca\Contracts;

interface ZatcaPolicyInterface
{
    public function requiresOnboarding(): bool;
    public function canIssueInvoice(): bool;
}
