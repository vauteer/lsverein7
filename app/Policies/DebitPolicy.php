<?php

namespace App\Policies;

use App\Models\Debit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DebitPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasAdminRights();
    }

    public function create(User $user)
    {
        return $user->hasAdminRights();
    }

    public function update(User $user, Debit $debit)
    {
        return $user->hasAdminRights();
    }

    public function delete(User $user, Debit $debit)
    {
        return $user->hasAdminRights();
    }

}
