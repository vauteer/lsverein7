<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->isClubAdmin();
    }

    public function update(User $user, Role $role)
    {
        return $user->isClubAdmin() && $user->club_id === $role->club_id;
    }

    public function delete(User $user, Role $role)
    {
        return $user->isClubAdmin() && $user->club_id === $role->club_id;
    }

}
