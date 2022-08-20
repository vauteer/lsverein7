<?php

namespace App\Policies;

use App\Models\Section;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SectionPolicy
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

    public function update(User $user, Section $section)
    {
        return $user->isClubAdmin() && $user->club_id === $section->club_id;
    }

    public function delete(User $user, Section $section)
    {
        return $user->isClubAdmin() && $user->club_id === $section->club_id;
    }

}
