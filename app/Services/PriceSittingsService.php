<?php

namespace App\Services;

use App\Models\SubscriberDoctorPriceSittings;
use Illuminate\Support\Collection;

class PriceSittingsService
{
    function shouldHidePrice($doctorAccountId, $subscriberId): bool
    {
        return SubscriberDoctorPriceSittings::where('doctor_account_id', $doctorAccountId)
            ->where('subscriber_id', $subscriberId)
            ->where('hide_prices', true)
            ->exists();
    }
    public function shouldHideSpecializationInfo(int $doctorAccountId, int $subscriberId): bool
    {
        return SubscriberDoctorPriceSittings::where([
            'doctor_account_id' => $doctorAccountId,
            'subscriber_id' => $subscriberId,
        ])->value('hide_specialization_info') ?? false;
    }
    public function getHiddenSubscribersForDoctor($doctorAccountId, $subscriberIds) : Collection
    {
        return SubscriberDoctorPriceSittings::where('doctor_account_id', $doctorAccountId)
            ->whereIn('subscriber_id', $subscriberIds)
            ->where('hide_prices', true)
            ->pluck('subscriber_id');
    }
    public function getSubscribersWithHiddenSpecializationInfo(
        int $doctorAccountId,
        Collection $subscriberIds
    ): Collection {
        return SubscriberDoctorPriceSittings::where('doctor_account_id', $doctorAccountId)
            ->whereIn('subscriber_id', $subscriberIds)
            ->where('hide_specialization_info', true)
            ->pluck('subscriber_id');
    }
}
