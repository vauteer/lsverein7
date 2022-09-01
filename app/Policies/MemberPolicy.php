<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MemberPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Member $model)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->hasAdminRights();
    }

    public function update(User $user, Member $member)
    {
        return $user->hasAdminRights() && $user->club_id === $member->club_id;
    }

    public function delete(User $user, Member $member)
    {
        return $user->hasAdminRights() && $user->club_id === $member->club_id;
    }

}
