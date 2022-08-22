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

    public function create(User $user)
    {
        return $user->isClubAdmin();
    }

    public function update(User $user, Member $member)
    {
        return $user->isClubAdmin() && $user->club_id === $member->club_id;
    }

    public function delete(User $user, Member $member)
    {
        return $user->isClubAdmin() && $user->club_id === $member->club_id;
    }

}
