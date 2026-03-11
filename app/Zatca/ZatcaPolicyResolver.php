<?php

namespace App\Zatca;

use App\Models\Subscriber;
use App\Zatca\Contracts\ZatcaPolicyInterface;
use App\Zatca\Policies\NullZatcaPolicy;
use App\Zatca\Policies\SaudiZatcaPolicy;

class ZatcaPolicyResolver
{
    public static function resolve(Subscriber $subscriber): ZatcaPolicyInterface
    {
        return match ($subscriber->country_code) {
            'SA' => new SaudiZatcaPolicy($subscriber),
            default => new NullZatcaPolicy($subscriber),
        };
    }
}

