<?php

namespace App\Policies;

use App\Models\Doctor_Account;
use App\Models\Type;
use Illuminate\Auth\Access\Response;

class TypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Doctor_Account $doctorAccount): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Doctor_Account $doctorAccount, Type $type): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Doctor_Account $doctorAccount): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Doctor_Account $doctorAccount, Type $type): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Doctor_Account $doctorAccount, Type $type): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Doctor_Account $doctorAccount, Type $type): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Doctor_Account $doctorAccount, Type $type): bool
    {
        //
    }
}
