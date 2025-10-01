<?php

namespace App\Policies;

use App\Models\Doctor;
use App\Models\Doctor_Account;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\DB;

class SubscriberPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Doctor_Account $doctorAccount, Subscriber $subscriber): bool
    {
        return DB::table('clinic_subscribers')
            ->join('doctors', 'clinic_subscribers.clinic_id', '=', 'doctors.clinic_id')
            ->where('doctors.id', $doctorAccount->doctor_id)
            ->where('clinic_subscribers.subscriber_id', $subscriber->id)
            ->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Subscriber $subscriber): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Subscriber $subscriber): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Subscriber $subscriber): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Subscriber $subscriber): bool
    {
        //
    }
}
