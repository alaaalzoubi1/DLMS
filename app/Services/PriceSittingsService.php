<?php

namespace App\Services;

use App\Models\SubscriberDoctorPriceSittings;

class PriceSittingsService
{
    function shouldHidePrice($doctorAccountId, $subscriberId): bool
    {
        return SubscriberDoctorPriceSittings::where('doctor_account_id', $doctorAccountId)
            ->where('subscriber_id', $subscriberId)
            ->where('hide_prices', true)
            ->exists();
    }
    public function getHiddenSubscribersForDoctor($doctorAccountId, $subscriberIds)
    {
        return SubscriberDoctorPriceSittings::where('doctor_account_id', $doctorAccountId)
            ->whereIn('subscriber_id', $subscriberIds)
            ->where('hide_prices', true)
            ->pluck('subscriber_id');
    }
}
