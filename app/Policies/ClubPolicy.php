<?php

namespace App\Policies;

use App\Models\Club;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClubPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->admin;
    }

    public function create(User $user)
    {
        return $user->admin;
    }

    public function update(User $user, Club $club)
    {
        return $user->admin || $user->hasAdminRights($club->id);
    }

    public function delete(User $user, Club $club)
    {
        return $user->admin;
    }

    public function change(User $user, Club $club)
    {
        return $user->admin;
    }

    public function download(User $user)
    {
        return $user->hasAdminRights();
    }
}
